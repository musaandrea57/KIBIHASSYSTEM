<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgrammeLevelRule;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\SemesterRegistrationDeadline;
use App\Models\Student;
use App\Models\SemesterRegistration;
use App\Models\SemesterRegistrationItem;
use App\Models\Program;
use App\Models\Module;
use App\Models\User;
use App\Models\ModuleOffering;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SemesterRegistrationSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Credit Rules Exist for all Programs
        $programs = Program::all();
        if ($programs->isEmpty()) {
            // Create dummy program if none
            $program = Program::create(['code' => 'CS', 'name' => 'Computer Science', 'department_id' => 1]); // Assuming dept 1 exists or is nullable, adjust if needed.
            $programs = collect([$program]);
        }

        foreach ($programs as $program) {
            foreach ([4, 5, 6] as $level) {
                ProgrammeLevelRule::firstOrCreate(
                    [
                        'program_id' => $program->id,
                        'nta_level' => $level,
                    ],
                    [
                        'min_credits' => 30,
                        'max_credits' => 45, // Example NACTVET loads
                    ]
                );
            }
        }

        // 2. Ensure Active Academic Year and Semester
        $year = AcademicYear::where('is_active', true)->first();
        if (!$year) {
            $year = AcademicYear::create([
                'name' => '2025/2026',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'is_active' => true,
            ]);
        }

        // Semesters are now generic (e.g. Semester 1, Semester 2)
        $semester = Semester::where('name', 'Semester 1')->first();
        if (!$semester) {
            $semester = Semester::create([
                'name' => 'Semester 1',
                // 'academic_year_id' => $year->id, // Removed in migration
                // 'start_date' => Carbon::now()->subMonths(1), // Removed/Ignored
                // 'end_date' => Carbon::now()->addMonths(3), // Removed/Ignored
                'is_active' => true, // Assuming this flag denotes "current semester type"
                'number' => 1,
            ]);
        }

        // 3. Create Registration Deadline
        SemesterRegistrationDeadline::firstOrCreate(
            [
                'academic_year_id' => $year->id,
                'semester_id' => $semester->id,
            ],
            [
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addWeeks(2),
                'created_by' => User::first()->id ?? 1, // Fallback to ID 1
            ]
        );

        // 4. Create a Test Student (if not exists)
        $studentUser = User::where('email', 'student@kibihas.ac.tz')->first();
        if (!$studentUser) {
            $studentUser = User::create([
                'name' => 'Test Student',
                'email' => 'student@kibihas.ac.tz',
                'password' => Hash::make('password'),
            ]);
            $studentUser->assignRole('student');
        }

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'registration_number' => 'TEST/001/2025',
                'first_name' => 'Test',
                'last_name' => 'Student',
                'gender' => 'Male',
                'date_of_birth' => '2000-01-01',
                'program_id' => $programs->first()->id,
                'current_nta_level' => 4,
                'current_academic_year_id' => $year->id,
                'current_semester_id' => $semester->id,
                'status' => 'active',
            ]
        );

        // 5. Create Modules, Offerings, and Assignments
        $moduleData = [
            ['code' => 'CSU04101', 'name' => 'Basic Computing', 'credits' => 10],
            ['code' => 'CSU04102', 'name' => 'Communication Skills', 'credits' => 10],
        ];

        $modules = collect();
        $teacher = User::role('teacher')->first() ?? User::first();

        foreach ($moduleData as $data) {
            $module = Module::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'credits' => $data['credits'],
                    'program_id' => $student->program_id,
                ]
            );
            $modules->push($module);

            // Create Offering
            $offering = ModuleOffering::firstOrCreate(
                [
                    'module_id' => $module->id,
                    'academic_year_id' => $year->id,
                    'semester_id' => $semester->id,
                    'nta_level' => 4,
                ],
                [
                    'status' => 'active',
                ]
            );

            // Create Teacher Assignment
            // Using new schema from setup_module_2_tables.php: 
            // module_offering_id, teacher_user_id, assigned_by_user_id
            
            \Illuminate\Support\Facades\DB::table('module_assignments')->updateOrInsert(
                [
                    'module_offering_id' => $offering->id,
                    'teacher_user_id' => $teacher->id,
                ],
                [
                    'assigned_by_user_id' => User::first()->id ?? 1, // Fallback to ID 1
                    'assigned_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        // 6. Create Approved Registration
        $registration = SemesterRegistration::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $year->id,
                'semester_id' => $semester->id,
            ],
            [
                'program_id' => $student->program_id,
                'nta_level' => 4,
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subDays(1),
                'approved_at' => Carbon::now(),
                'approved_by' => User::role('admin')->first()->id ?? 1,
            ]
        );

        foreach ($modules as $module) {
            $offering = ModuleOffering::where('module_id', $module->id)
                ->where('academic_year_id', $year->id)
                ->where('semester_id', $semester->id)
                ->where('nta_level', 4)
                ->first();

            if ($offering) {
                SemesterRegistrationItem::firstOrCreate(
                    [
                        'semester_registration_id' => $registration->id,
                        'module_offering_id' => $offering->id,
                    ],
                    [
                        'credits_snapshot' => $module->credits,
                        // 'is_core' => true, 
                    ]
                );
            }
        }
    }
}
