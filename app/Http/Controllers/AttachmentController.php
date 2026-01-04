<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment)
    {
        $user = Auth::user();
        
        // Authorization Logic
        $attachable = $attachment->attachable;
        
        if (!$attachable) {
            // Fallback for non-polymorphic records if any
            if ($attachment->message_id) {
                $attachable = \App\Models\Message::find($attachment->message_id);
            }
            
            if (!$attachable) {
                abort(404, 'Attachment source not found.');
            }
        }
        
        if ($attachable instanceof \App\Models\Message) {
            // Check if user is sender or recipient
            $isSender = $attachable->sender_id === $user->id;
            $isRecipient = $attachable->recipients()->where('recipient_id', $user->id)->exists();
            
            if (!$isSender && !$isRecipient) {
                abort(403, 'Unauthorized access to this attachment.');
            }
        } elseif ($attachable instanceof \App\Models\Announcement) {
            // Check if announcement is published or user is creator
            if (!$attachable->is_published && $attachable->created_by !== $user->id) {
                 abort(403, 'Unauthorized access to this attachment.');
            }
        }
        
        // Log the download
        AuditService::log('attachment_downloaded', $attachment, [], [
            'file_name' => $attachment->file_name,
            'attachable_type' => get_class($attachable),
            'attachable_id' => $attachable->id
        ]);

        // Check storage locations
        // Priority to 'private' disk as per MessageService
        if (Storage::disk('private')->exists($attachment->file_path)) {
            return Storage::disk('private')->download($attachment->file_path, $attachment->file_name);
        }
        
        // Fallback to default disk
        if (Storage::exists($attachment->file_path)) {
            return Storage::download($attachment->file_path, $attachment->file_name);
        }
        
        abort(404, 'File not found.');
    }
}
