<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnnouncementService
{
    public function createAnnouncement(User $creator, array $data)
    {
        return DB::transaction(function () use ($creator, $data) {
            $isPublished = $data['is_published'] ?? true;
            $status = $isPublished ? 'published' : 'draft';

            $announcement = Announcement::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'category' => $data['category'],
                'priority' => $data['priority'] ?? 'normal',
                'audience' => $data['audience'] ?? ['all'],
                'start_date' => $data['start_date'] ?? now(),
                'end_date' => $data['end_date'] ?? null,
                'is_published' => $isPublished,
                'is_archived' => false,
                'status' => $status,
                'published_at' => $isPublished ? now() : null,
                'created_by' => $creator->id,
            ]);

             if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                     $announcement->attachments()->create([
                        'file_path' => $file['path'],
                        'file_name' => $file['name'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size'],
                    ]);
                }
            }

            if ($status === 'published') {
                AuditService::log('announcement_published', $announcement, [], [
                    'audience' => $data['audience'] ?? ['all'],
                    'priority' => $data['priority'] ?? 'normal'
                ]);
            }

            return $announcement;
        });
    }

    public function getActiveAnnouncements(User $user)
    {
        // Filter by audience logic here if needed
        // For now, showing all active announcements
        // In real impl, check if user role matches audience
        return Announcement::active()
            ->with('creator')
            ->orderBy('priority', 'desc') // High priority first
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function markAsRead(User $user, Announcement $announcement)
    {
        $read = AnnouncementRead::firstOrCreate([
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
        ], [
            'read_at' => now(),
        ]);

        if ($read->wasRecentlyCreated) {
            AuditService::log('announcement_read', $announcement, [], [
                'user_id' => $user->id
            ]);
        }

        return $read;
    }

    public function archiveExpired()
    {
        $count = Announcement::where('status', '!=', 'archived')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update([
                'is_archived' => true,
                'status' => 'archived'
            ]);

        if ($count > 0) {
            \Illuminate\Support\Facades\Log::info("Archived {$count} expired announcements.");
        }
        
        return $count;
    }
}
