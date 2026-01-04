<?php

namespace App\Services;

use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MessagingService
{
    public function sendMessage(User $sender, array $recipientIds, string $subject, string $body, string $type = 'private', array $attachments = [], ?int $parentId = null)
    {
        return DB::transaction(function () use ($sender, $recipientIds, $subject, $body, $type, $attachments, $parentId) {
            $message = Message::create([
                'sender_id' => $sender->id,
                'subject' => $subject,
                'body' => $body,
                'type' => $type,
                'parent_id' => $parentId,
            ]);

            foreach ($recipientIds as $recipientId) {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                ]);
            }

            foreach ($attachments as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('attachments', 'private'); // Store in private disk
                    
                    Attachment::create([
                        'message_id' => $message->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            AuditService::log('message.sent', $message, [], ['recipient_count' => count($recipientIds)]);

            return $message;
        });
    }

    public function markAsRead(Message $message, User $user)
    {
        $recipient = MessageRecipient::where('message_id', $message->id)
            ->where('recipient_id', $user->id)
            ->first();

        if ($recipient && !$recipient->read_at) {
            $recipient->update(['read_at' => now()]);
        }
    }

    public function acknowledge(Message $message, User $user)
    {
        $recipient = MessageRecipient::where('message_id', $message->id)
            ->where('recipient_id', $user->id)
            ->first();

        if ($recipient && !$recipient->acknowledged_at) {
            $recipient->update(['acknowledged_at' => now()]);
            AuditService::log('message.acknowledged', $message);
        }
    }
}
