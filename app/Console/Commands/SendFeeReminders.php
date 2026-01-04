<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SendFeeReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:fee-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send fee payment reminders to students with outstanding balances';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting fee reminder process...');

        $students = Student::where('status', 'active')->cursor();
        
        $count = 0;
        foreach ($students as $student) {
            /** @var Student $student */
            $balance = $student->balance;
            
            if ($balance > 0) {
                // Idempotency: Check if sent in last 3 days
                $alreadySent = \App\Models\SmsMessage::where('recipient_id', $student->id)
                    ->where('template_key', 'fees_reminder')
                    ->where('created_at', '>', now()->subDays(3))
                    ->exists();

                if ($alreadySent) continue;

                try {
                    $data = [
                        'student_name' => "{$student->first_name} {$student->last_name}",
                        'reg_no' => $student->registration_number,
                        'balance' => number_format($balance),
                    ];

                    $message = $this->smsService->parseTemplate('fees_reminder', $data);
                    
                    if (!$message) {
                        Log::warning("Fee reminder template parsing failed for student {$student->id}");
                        continue;
                    }

                    $this->smsService->sendNow(
                        $student->phone, 
                        $message, 
                        'student',
                        $student->id,
                        null,
                        'fees_reminder'
                    );
                    
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to send reminder to Student {$student->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Processed {$count} reminders.");
        return 0;
    }
}
