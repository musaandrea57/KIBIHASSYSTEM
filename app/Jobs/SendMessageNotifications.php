<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\SmsService;
use App\Mail\NewMessageMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMessageNotifications implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        // 1. Get recipients
        $recipients = $this->message->recipients()->with('recipient')->get();

        // 2. Iterate and send notifications based on channels
        $channels = $this->message->channels ?? ['system'];

        foreach ($recipients as $recipientRecord) {
            $user = $recipientRecord->recipient;
            
            if (!$user) continue;

            if (in_array('email', $channels) && $user->email) {
                try {
                    Mail::to($user)->send(new NewMessageMail($this->message));
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                }
            }

            if (in_array('sms', $channels) && $user->phone) {
                try {
                    $smsService->sendNow(
                        $user->phone, 
                        "KIBIHAS: New Message - " . substr($this->message->subject, 0, 20) . "...", 
                        'user', 
                        $user->id, 
                        $this->message->sender_id
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to {$user->phone}: " . $e->getMessage());
                }
            }
        }
    }
}
