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
        $systemUser = $this->ensureSystemUser();
        $programs = $this->ensurePrograms();
        $this->ensureCreditRules($programs);
        
        $year = $this->ensureActiveAcademicYear();
        $semester = $this->ensureSemester();
        
        $this->ensureRegistrationDeadline($year, $semester, $systemUser);
        
        $student = $this->ensureTestStudent($programs->first(), $year, $semester);
        
        $modules = $this->createModulesAndOfferings($student, $year, $semester);
        
        $this->createApprovedRegistration($student, $year, $semester, $systemUser, $modules);
    }

    private function ensureSystemUser()
    {
        return User::firstOrCreate(
            ['email' => 'admin@kibihas.ac.tz'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
    }

    private function ensurePrograms()
    {
        $programs = Program::all();
        if ($programs->isEmpty()) {
            $program = Program::create(['code' => 'CS', 'name' => 'Computer Science']);
            return collect([$program]);
        }
        return $programs;
    }

    private function ensureCreditRules($programs)
    {
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
    }

    private function ensureActiveAcademicYear()
    {
        $year = AcademicYear::where('is_active', true)->first();
        if (!$year) {
            $year = AcademicYear::create([
                'name' => '2025/2026',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'is_active' => true,
            ]);
        }
        return $year;
    }

    private function ensureSemester()
    {
        return Semester::firstOrCreate(
            ['name' => 'Semester 1'],
            [
                'is_active' => true,
                'number' => 1,
            ]
        );
    }

    private function ensureRegistrationDeadline($year, $semester, $systemUser)
    {
        SemesterRegistrationDeadline::firstOrCreate(
            [
                'academic_year_id' => $year->id,
                'semester_id' => $semester->id,
            ],
            [
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addWeeks(2),
                'created_by' => $systemUser->id,
            ]
        );
    }

    private function ensureTestStudent($program, $year, $semester)
    {
        $studentUser = User::where('email', 'student@kibihas.ac.tz')->first();
        if (!$studentUser) {
            $studentUser = User::create([
                'name' => 'Test Student',
                'email' => 'student@kibihas.ac.tz',
                'password' => Hash::make('password'),
            ]);
            if (\Spatie\Permission\Models\Role::where('name', 'student')->exists()) {
                $studentUser->assignRole('student');
            }
        }

        return Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'registration_number' => 'TEST/001/2025',
                'first_name' => 'Test',
                'last_name' => 'Student',
                'gender' => 'Male',
                'date_of_birth' => '2000-01-01',
                'program_id' => $program->id,
                'current_nta_level' => 4,
                'current_academic_year_id' => $year->id,
                'current_semester_id' => $semester->id,
                'status' => 'active',
            ]
        );
    }

    private function createModulesAndOfferings($student, $year, $semester)
    {
        $moduleData = [
            ['code' => 'CSU04101', 'name' => 'Basic Computing', 'credits' => 10],
            ['code' => 'CSU04102', 'name' => 'Communication Skills', 'credits' => 10],
        ];

        $modules = collect();
        $teacher = User::role('teacher')->first() ?? User::first();
        $assigner = User::first() ?? $teacher;

        foreach ($moduleData as $data) {
            $module = Module::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'credits' => $data['credits'],
                    'program_id' => $student->program_id,
                    'nta_level' => 4,
                    'semester_number' => 1,
                ]
            );
            $modules->push($module);

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

            \Illuminate\Support\Facades\DB::table('module_assignments')->updateOrInsert(
                [
                    'module_offering_id' => $offering->id,
                    'teacher_user_id' => $teacher->id,
                ],
                [
                    'assigned_by_user_id' => $assigner->id,
                    'assigned_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        return $modules;
    }

    private function createApprovedRegistration($student, $year, $semester, $systemUser, $modules)
    {
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
                'approved_by' => $systemUser->id,
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
                    ]
                );
            }
        }
    }
}
