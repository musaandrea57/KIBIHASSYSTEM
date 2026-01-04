<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed Roles
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AcademicSeeder::class);
    }

    public function test_parent_can_access_dashboard()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('parent.dashboard');
    }

    public function test_parent_can_view_linked_child_details()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');

        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        $program = Program::first();
        $academicYear = AcademicYear::first();
        $semester = Semester::first();

        $student = Student::create([
            'user_id' => $studentUser->id,
            'registration_number' => 'TEST/001',
            'first_name' => 'Test',
            'last_name' => 'Child',
            'gender' => 'Male',
            'date_of_birth' => '2005-01-01',
            'program_id' => $program->id,
            'current_nta_level' => 4,
            'current_academic_year_id' => $academicYear->id,
            'current_semester_id' => $semester->id,
            'status' => 'active',
        ]);

        // Link parent to student
        DB::table('student_guardians')->insert([
            'student_id' => $student->id,
            'user_id' => $parent->id,
            'relationship' => 'Father',
        ]);

        $response = $this->actingAs($parent)->get(route('parent.child.details', $student));

        $response->assertStatus(200);
        $response->assertViewIs('parent.child-details');
        $response->assertSee($student->registration_number);
    }

    public function test_parent_cannot_view_unlinked_child_details()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');

        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        $program = Program::first();
        $academicYear = AcademicYear::first();
        $semester = Semester::first();

        $student = Student::create([
            'user_id' => $studentUser->id,
            'registration_number' => 'TEST/002',
            'first_name' => 'Other',
            'last_name' => 'Child',
            'gender' => 'Male',
            'date_of_birth' => '2005-01-01',
            'program_id' => $program->id,
            'current_nta_level' => 4,
            'current_academic_year_id' => $academicYear->id,
            'current_semester_id' => $semester->id,
            'status' => 'active',
        ]);

        // Do NOT link parent to student

        $response = $this->actingAs($parent)->get(route('parent.child.details', $student));

        $response->assertStatus(403);
    }
}
