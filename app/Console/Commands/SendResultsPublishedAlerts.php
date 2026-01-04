<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\ModuleResult;
use App\Models\SmsMessage;
use App\Services\SmsService;
use App\Models\AcademicYear;
use App\Models\Semester;

class SendResultsPublishedAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-results-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS alerts to students when results are published, checking fee clearance.';

    protected $smsService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->info('Starting results published alerts check...');

        // 1. Identify active Academic Year and Semester
        // Assuming "current" is what we want. Or we should look for *recently published* regardless of current status?
        // Usually results are published for the *just concluded* semester which might still be active or just closed.
        // Let's look for any academic year/semester that has results published in the last 24 hours.
        // But the requirement says "When admin publishes results".
        // Let's target the *active* academic year and semester for now, or the one with the most recent publications.
        
        // Safer approach: Get the current active year/semester.
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        if (!$activeYear || !$activeSemester) {
            $this->warn('No active academic year or semester found.');
            return 0;
        }

        $this->info("Checking for Year: {$activeYear->year}, Semester: {$activeSemester->name}");

        // 2. Find students with published results in this session
        // We look for students who have at least one published result in this session.
        $studentsWithResults = Student::whereHas('moduleResults', function($q) use ($activeYear, $activeSemester) {
            $q->where('academic_year_id', $activeYear->id)
              ->where('semester_id', $activeSemester->id)
              ->where('status', 'published');
        })
        ->where('status', 'active') // Only active students
        ->get();

        $this->info("Found " . $studentsWithResults->count() . " students with published results.");

        foreach ($studentsWithResults as $student) {
            // 3. Check if we already sent an alert for this session
            $alreadySent = SmsMessage::where('recipient_id', $student->id)
                ->where('template_key', 'results_published')
                ->where('meta->academic_year_id', $activeYear->id)
                ->where('meta->semester_id', $activeSemester->id)
                ->exists();

            if ($alreadySent) {
                // Already notified for this semester's results
                continue;
            }

            // 4. Check Fee Clearance
            $balance = $student->balance; // Assuming accessor or column exists

            if ($balance <= 0) {
                // Fee Cleared -> Send Results Alert
                try {
                    $data = [
                        'name' => $student->first_name,
                        'semester' => $activeSemester->name,
                        'year' => $activeYear->year,
                    ];
                    
                    $message = $this->smsService->parseTemplate('results_published', $data);

                    if ($message) {
                        $log = $this->smsService->sendNow(
                            $student->phone,
                            $message,
                            'student',
                            $student->id,
                            null,
                            'results_published'
                        );

                        if ($log) {
                            $currentMeta = $log->meta ?? [];
                            $log->update([
                                'meta' => array_merge($currentMeta, [
                                    'academic_year_id' => $activeYear->id,
                                    'semester_id' => $activeSemester->id,
                                    'reason' => 'results_published_alert'
                                ])
                            ]);
                        }
                        $this->info("Sent results alert to {$student->registration_number}");
                    }
                } catch (\Exception $e) {
                    $this->error("Failed to send to {$student->registration_number}: " . $e->getMessage());
                }
            } else {
                // Not Cleared -> Send Fee Reminder (Optional but requested)
                // Ensure we don't spam fee reminders every time this runs (e.g., hourly)
                // Check if we sent a fee reminder in the last 3 days
                $recentFeeReminder = SmsMessage::where('recipient_id', $student->id)
                    ->whereIn('template_key', ['fees_reminder', 'results_withheld_fees'])
                    ->where('created_at', '>', now()->subDays(3))
                    ->exists();

                if (!$recentFeeReminder) {
                    try {
                        $data = [
                            'student_name' => $student->first_name,
                            'balance' => number_format($balance),
                            'currency' => 'TZS',
                            'reg_no' => $student->registration_number,
                        ];

                        $message = $this->smsService->parseTemplate('fee_reminder', $data);

                        if ($message) {
                            $log = $this->smsService->sendNow(
                                $student->phone,
                                $message,
                                'student',
                                $student->id,
                                null,
                                'fee_reminder'
                            );

                            if ($log) {
                                $currentMeta = $log->meta ?? [];
                                $log->update([
                                    'meta' => array_merge($currentMeta, [
                                        'academic_year_id' => $activeYear->id,
                                        'semester_id' => $activeSemester->id,
                                        'reason' => 'results_withheld_fees'
                                    ])
                                ]);
                            }
                            $this->info("Sent fee reminder (results withheld) to {$student->registration_number}");
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to send fee reminder to {$student->registration_number}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info('Results alerts check completed.');
        return 0;
    }
}
