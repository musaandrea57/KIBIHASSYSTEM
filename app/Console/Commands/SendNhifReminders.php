<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NhifMembership;
use App\Models\SmsMessage;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SendNhifReminders extends Command
{
    protected $signature = 'sms:nhif-reminders';
    protected $description = 'Send reminders for expiring NHIF memberships';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('Starting NHIF reminder process...');

        $expiringMemberships = NhifMembership::whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->whereHas('student', function($q) {
                $q->where('status', 'active');
            })
            ->with('student')
            ->get();

        $count = 0;
        foreach ($expiringMemberships as $membership) {
            /** @var NhifMembership $membership */
            /** @var \App\Models\Student $student */
            $student = $membership->student;
            
            // Idempotency: Check if sent in last 15 days
            $alreadySent = SmsMessage::where('recipient_id', $student->id)
                ->where('template_key', 'nhif_expiry')
                ->where('created_at', '>', now()->subDays(15))
                ->exists();

            if ($alreadySent) continue;

            try {
                $data = [
                    'student_name' => "{$student->first_name} {$student->last_name}",
                    'card_number' => $membership->card_number,
                    'expiry_date' => $membership->expiry_date->format('d M Y'),
                ];

                $message = $this->smsService->parseTemplate('nhif_expiry', $data);
                
                if ($message) {
                    $this->smsService->sendNow(
                        $student->phone ?? '0000000000',
                        $message,
                        'student',
                        $student->id,
                        null,
                        'nhif_expiry'
                    );
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send NHIF reminder to Student {$student->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} NHIF reminders.");
        return 0;
    }
}
