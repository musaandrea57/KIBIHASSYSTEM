<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationPeriod;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\SmsTemplate;
use App\Models\SmsBatch;
use App\Models\SmsMessage;
use App\Models\User;
use App\Models\ModuleOffering;
use App\Models\Student;
use App\Models\Evaluation;

class Module8Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Evaluation Template
        $template = EvaluationTemplate::create([
            'name' => 'Standard Lecturer Evaluation',
            'description' => 'Standard evaluation form for lecturers.',
            'is_active' => true,
        ]);

        $questions = [
            ['text' => 'The lecturer explains concepts clearly.', 'type' => 'likert'],
            ['text' => 'The lecturer is punctual and prepared.', 'type' => 'likert'],
            ['text' => 'The lecturer encourages questions and participation.', 'type' => 'likert'],
            ['text' => 'The lecturer provides helpful feedback.', 'type' => 'likert'],
            ['text' => 'The course materials were useful.', 'type' => 'likert'],
            ['text' => 'The lecturer is available for consultation.', 'type' => 'likert'],
            ['text' => 'The assessment methods were fair.', 'type' => 'likert'],
            ['text' => 'Overall, I am satisfied with this lecturer.', 'type' => 'likert'],
            ['text' => 'Any additional comments?', 'type' => 'text', 'is_required' => false],
        ];

        foreach ($questions as $index => $q) {
            EvaluationQuestion::create([
                'evaluation_template_id' => $template->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'order' => $index + 1,
                'is_required' => $q['is_required'] ?? true,
            ]);
        }

        // 2. Seed Evaluation Period
        $year = AcademicYear::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();

        if ($year && $semester) {
            $period = EvaluationPeriod::create([
                'name' => "{$year->year} - {$semester->name} Evaluation",
                'academic_year_id' => $year->id,
                'semester_id' => $semester->id,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
                'is_open' => true,
            ]);

            // 3. Generate Evaluation for a Student
            // Find a module offering
            $offering = ModuleOffering::with('module')->where('academic_year_id', $year->id)->where('semester_id', $semester->id)->first();
            
            // If no offering, try to create one or find any
            if (!$offering) {
                // Try finding any offering or skip
                // Ideally previous modules seeded offerings
            }

            if ($offering) {
                // Find a student
                $student = Student::first();
                // Find a teacher (assigned to offering or just a teacher role)
                // Assuming we have teacher assigned logic, but for seeding we can force it
                $teacher = User::role('teacher')->first();

                if ($student && $teacher) {
                    Evaluation::firstOrCreate([
                        'evaluation_period_id' => $period->id,
                        'student_id' => $student->id,
                        'module_offering_id' => $offering->id,
                    ], [
                        'teacher_id' => $teacher->id,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        // 4. Seed SMS Templates
        $smsTemplates = [
            [
                'key' => 'admissions_approved',
                'name' => 'Admission Approved',
                'message_body' => 'Dear {name}, congratulations! Your admission to KIBIHAS has been approved. Please log in to your portal to download your admission letter.',
                'variables' => ['name'],
            ],
            [
                'key' => 'fees_reminder',
                'name' => 'Fee Reminder',
                'message_body' => 'Dear {name}, you have an outstanding balance of TZS {balance}. Please clear it before exams.',
                'variables' => ['name', 'balance'],
            ],
            [
                'key' => 'registration_deadline',
                'name' => 'Registration Deadline',
                'message_body' => 'Dear {name}, registration for this semester closes on {date}. Please register to avoid penalties.',
                'variables' => ['name', 'date'],
            ],
            [
                'key' => 'results_published',
                'name' => 'Results Published',
                'message_body' => 'Dear {name}, results for {semester} {year} have been published. Log in to view them.',
                'variables' => ['name', 'semester', 'year'],
            ],
            [
                'key' => 'nhif_expiry',
                'name' => 'NHIF Expiry Alert',
                'message_body' => 'Dear {name}, your NHIF card expires on {expiry_date}. Please renew it to ensure coverage.',
                'variables' => ['name', 'expiry_date'],
            ],
        ];

        foreach ($smsTemplates as $t) {
            SmsTemplate::updateOrCreate(['key' => $t['key']], $t);
        }

        // 5. Seed SMS Batch
        $admin = User::role('admin')->first();
        
        // 5.1 Update Students with Phone Numbers (Required for SMS testing)
        $students = Student::all();
        foreach ($students as $index => $student) {
            if (empty($student->phone)) {
                // Generate a dummy valid TZ phone number: +255 7XX XXXXXX
                // Use index to make it unique-ish
                $suffix = str_pad($index, 6, '0', STR_PAD_LEFT);
                $student->phone = '+255712' . $suffix;
                $student->save();
            }
        }

        if ($admin) {
            $batch = SmsBatch::create([
                'name' => 'Test Batch Seed',
                'total_messages' => 1,
                'status' => 'completed',
                'created_by' => $admin->id,
                'success_count' => 1,
                'completed_at' => now(),
            ]);

            SmsMessage::create([
                'recipient_type' => 'custom',
                'phone_number' => '+255700000000',
                'message_body' => 'Test message from seeder',
                'status' => 'sent',
                'sent_at' => now(),
                'sms_batch_id' => $batch->id,
                'sent_by' => $admin->id,
                'meta' => ['message_id' => 'SEED-123'],
            ]);
        }
    }
}
