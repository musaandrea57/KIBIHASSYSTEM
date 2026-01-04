<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Module;
use App\Models\Result;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ResultModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $program;
    protected $academicYear;
    protected $semester;
    protected $module;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles
        $roles = ['admin', 'student', 'parent'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Seed basic academic structure
        $this->program = Program::create(['name' => 'Test Program', 'code' => 'TP', 'min_credits_per_semester' => 15, 'max_credits_per_semester' => 30]);
        $this->academicYear = AcademicYear::create(['name' => '2025/2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);
        $this->semester = Semester::create(['name' => 'Semester 1', 'number' => 1, 'is_active' => true]);
        $this->module = Module::create(['name' => 'Test Module', 'code' => 'TM101', 'credits' => 10, 'program_id' => $this->program->id, 'nta_level' => 4, 'semester_number' => 1]);
    }

    public function test_admin_can_store_results()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = Student::create([
            'user_id' => $studentUser->id,
            'registration_number' => 'TEST/001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Male',
            'date_of_birth' => '2000-01-01',
            'program_id' => $this->program->id,
            'current_nta_level' => 4,
            'current_academic_year_id' => $this->academicYear->id,
            'current_semester_id' => $this->semester->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.results.store'), [
            'module_id' => $this->module->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'results' => [
                $student->id => [
                    'student_id' => $student->id,
                    'ca_score' => 35,
                    'exam_score' => 50,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.results.index'));
        $this->assertDatabaseHas('results', [
            'student_id' => $student->id,
            'module_id' => $this->module->id,
            'ca_score' => 35,
            'exam_score' => 50,
            'score' => 85,
            'grade' => 'A', // Assuming logic A >= 80
        ]);
    }

    public function test_student_can_view_own_results()
    {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = Student::create([
            'user_id' => $studentUser->id,
            'registration_number' => 'TEST/002',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'date_of_birth' => '2000-01-01',
            'program_id' => $this->program->id,
            'current_nta_level' => 4,
            'current_academic_year_id' => $this->academicYear->id,
            'current_semester_id' => $this->semester->id,
            'status' => 'active',
        ]);

        Result::create([
            'student_id' => $student->id,
            'module_id' => $this->module->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'score' => 75,
            'grade' => 'B',
            'grade_points' => 3,
            'remarks' => 'Pass',
            'is_published' => true,
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.results'));

        $response->assertStatus(200);
        $response->assertSee('TM101');
        $response->assertSee('75'); // Usually grade is shown, score might not be depending on view
        $response->assertSee('B');
    }

    public function test_parent_can_view_child_results()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');

        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = Student::create([
            'user_id' => $studentUser->id,
            'registration_number' => 'TEST/003',
            'first_name' => 'Child',
            'last_name' => 'One',
            'gender' => 'Male',
            'date_of_birth' => '2005-01-01',
            'program_id' => $this->program->id,
            'current_nta_level' => 4,
            'current_academic_year_id' => $this->academicYear->id,
            'current_semester_id' => $this->semester->id,
            'status' => 'active',
        ]);

        // Link parent
        $parent->children()->attach($student->id, ['relationship' => 'Father']);

        Result::create([
            'student_id' => $student->id,
            'module_id' => $this->module->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'score' => 90,
            'grade' => 'A',
            'grade_points' => 4,
            'remarks' => 'Pass',
            'is_published' => true,
        ]);

        $response = $this->actingAs($parent)->get(route('parent.child.details', $student));

        $response->assertStatus(200);
        $response->assertSee('TM101');
        $response->assertSee('A');
    }
}
