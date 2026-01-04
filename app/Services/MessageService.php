<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\Attachment;
use App\Models\MessageRecipient;
use App\Jobs\SendMessageNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function sendMessage(User $sender, array $data)
    {
        $message = DB::transaction(function () use ($sender, $data) {
            // 1. Create Message
            $message = Message::create([
                'sender_id' => $sender->id,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'type' => $data['type'] ?? 'private', // 'private' or 'announcement' or 'notification'
                'classification' => $data['classification'] ?? 'General Notice',
                'channels' => $data['channels'] ?? ['system'],
                'target_audience_filters' => $data['filters'] ?? null,
            ]);

            // 2. Add Attachments
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                     $message->attachments()->create([
                        'file_path' => $file['path'],
                        'file_name' => $file['name'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size'],
                    ]);
                }
            }

            // 3. Add Recipients
            $recipients = $this->resolveRecipients($data['recipients'] ?? [], $data['filters'] ?? []);
            
            $recipientData = collect($recipients)->unique()->map(function ($userId) {
                return ['recipient_id' => $userId];
            });

            // Chunk insert for DB records to avoid memory issues with thousands of users
            foreach ($recipientData->chunk(500) as $chunk) {
                $message->recipients()->createMany($chunk->toArray());
            }

            // Log the action
            AuditService::log('message_sent', $message, [], [
                'recipient_count' => count($recipients),
                'channels' => $data['channels'] ?? ['system'],
                'classification' => $data['classification'] ?? 'General Notice'
            ]);

            return $message;
        });

        // 4. Dispatch Job for bulk delivery / notifications
        // Even for 'system' only, we might want to trigger realtime notifications
        // For now, we dispatch if external channels are involved OR if we want to handle async processing
        if (in_array('email', $data['channels'] ?? []) || in_array('sms', $data['channels'] ?? [])) {
             SendMessageNotifications::dispatch($message);
        }

        return $message;
    }

    public function markAsRead(User $user, Message $message)
    {
        $recipient = $message->recipients()->where('recipient_id', $user->id)->first();
        
        if ($recipient && !$recipient->read_at) {
            $recipient->update(['read_at' => now()]);
            
            AuditService::log('message_read', $message, [], [
                'recipient_id' => $user->id
            ]);
        }
        
        return $recipient;
    }

    public function resolveRecipients(array $explicitRecipients, array $filters)
    {
        $recipientIds = $explicitRecipients;

        // Logic to resolve filters to user IDs
        // E.g., if filter has 'program_id', get students in that program
        if (!empty($filters)) {
            $query = User::query();

            if (!empty($filters['role'])) {
                $query->role($filters['role']);
                
                // If role is student, apply student-specific filters
                if (strtolower($filters['role']) === 'student') {
                    $query->whereHas('student', function ($q) use ($filters) {
                        if (!empty($filters['program_id'])) {
                            $q->where('program_id', $filters['program_id']);
                        }
                        if (!empty($filters['academic_year_id'])) {
                            $q->where('current_academic_year_id', $filters['academic_year_id']);
                        }
                        if (!empty($filters['semester_id'])) {
                            $q->where('current_semester_id', $filters['semester_id']);
                        }
                        if (!empty($filters['nta_level'])) {
                            $q->where('current_nta_level', $filters['nta_level']);
                        }
                    });
                }
            }
            
            $filteredIds = $query->pluck('id')->toArray();
            $recipientIds = array_merge($recipientIds, $filteredIds);
        }

        return array_unique($recipientIds);
    }

    public function getInbox(User $user, $search = null)
    {
        $query = Message::whereHas('recipients', function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
              ->where('is_archived', false);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhereHas('sender', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->with(['sender', 'attachments'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    }

    public function getSentMessages(User $user, $search = null)
    {
        $query = Message::where('sender_id', $user->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return $query->withCount('recipients')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getMessageDetails(Message $message)
    {
        return $message->load(['sender', 'attachments', 'recipients.recipient', 'replies.sender', 'replies.attachments', 'parent.sender']);
    }

    public function reply(User $sender, Message $parent, array $data)
    {
        return DB::transaction(function () use ($sender, $parent, $data) {
            // Determine recipients: usually the sender of the parent message
            // If the sender of the parent is the current user (e.g. replying to own message?), that's weird.
            // Usually we reply to the parent's sender.
            $recipientId = $parent->sender_id;
            
            // If I am the sender of the parent, I might be replying to a reply?
            // But for simple depth:
            if ($sender->id === $parent->sender_id) {
                // I am adding a note to my own message? Or this logic is for when I view a thread and add a message.
                // If I am the Principal and I sent a message to Student A. Student A replies.
                // The reply has parent_id = original. Sender = Student A.
                // If I reply to Student A's reply, the parent should be the ORIGINAL message to keep threading flat?
                // Or parent = Student A's reply for nested?
                // Let's stick to flat threading (all share same root).
                // But Message model has parent_id.
                
                // If parent has a parent, use that as root.
                $root = $parent->parent_id ? $parent->parent : $parent;
            } else {
                $root = $parent;
            }

            // Create Reply Message
            $reply = Message::create([
                'sender_id' => $sender->id,
                'subject' => 'Re: ' . str_replace('Re: ', '', $parent->subject),
                'body' => $data['body'],
                'type' => 'message',
                'classification' => $parent->classification,
                'channels' => ['system'], // Replies usually system only unless specified
                'parent_id' => $parent->id, // Simple nesting
            ]);

            // Add Attachments
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                     $reply->attachments()->create([
                        'file_path' => $file['path'],
                        'file_name' => $file['name'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size'],
                    ]);
                }
            }

            // Add Recipient (The sender of the parent message)
            // If I am replying to a thread, I want to notify the other person.
            // If parent sender was me, I am replying to myself? No.
            // If Student A sent me a message (I am recipient). I reply. Recipient is Student A.
            $targetUserId = ($parent->sender_id === $sender->id) 
                ? $parent->recipients()->first()->recipient_id // Fallback if I replied to my own sent message?
                : $parent->sender_id;

            // Wait, if I reply to a broadcast message?
            // Principal -> All Students.
            // Student A -> Principal (Reply). Parent = Broadcast.
            // Principal -> Student A (Reply to Reply).
            
            // Let's assume explicit recipient in data or derived from parent.
            $recipientIds = $data['recipients'] ?? [$targetUserId];

            foreach ($recipientIds as $uid) {
                $reply->recipients()->create(['recipient_id' => $uid]);
            }

            // Log
            AuditService::log('message_reply', $reply, [], [
                'parent_id' => $parent->id,
                'recipient_count' => count($recipientIds)
            ]);

            return $reply;
        });
    }
}
