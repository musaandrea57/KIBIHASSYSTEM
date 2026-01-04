<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SmsMessage;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SendRegistrationReminders extends Command
{
    protected $signature = 'sms:registration-reminders';
    protected $description = 'Send reminders to students who have not registered for the current semester';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('Starting registration reminder process...');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->error('No active academic year found.');
            return 1;
        }

        // Find students active but not registered in current year
        // Assuming registration is per year or semester. The logic uses academic_year_id.
        $students = Student::where('status', 'active')
            ->whereDoesntHave('semesterRegistrations', function($q) use ($activeYear) {
                /** @var AcademicYear $activeYear */
                $q->where('academic_year_id', $activeYear->id);
            })
            ->get();

        $count = 0;
        foreach ($students as $student) {
            /** @var Student $student */
            // Idempotency: Check if sent in last 3 days
            $alreadySent = SmsMessage::where('recipient_id', $student->id)
                ->where('template_key', 'registration_deadline')
                ->where('created_at', '>', now()->subDays(3))
                ->exists();

            if ($alreadySent) continue;

            try {
                $data = [
                    'student_name' => "{$student->first_name} {$student->last_name}",
                    'reg_no' => $student->registration_number,
                    'deadline' => $activeYear->end_date ? \Carbon\Carbon::parse($activeYear->end_date)->format('d M Y') : 'soon',
                ];

                $message = $this->smsService->parseTemplate('registration_deadline', $data);
                
                if ($message) {
                    $this->smsService->sendNow(
                        $student->phone ?? '0000000000',
                        $message,
                        'student',
                        $student->id,
                        null,
                        'registration_deadline'
                    );
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send registration reminder to Student {$student->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} registration reminders.");
        return 0;
    }
}
