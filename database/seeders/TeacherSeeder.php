<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\StaffProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure departments exist
        $nursing = Department::where('code', 'NUR')->first();
        $dentistry = Department::where('code', 'DEN')->first();

        if (!$nursing || !$dentistry) return;

        // Teacher 1: Nursing
        $teacher1 = User::firstOrCreate(
            ['email' => 'teacher.nursing@kibihas.ac.tz'],
            [
                'name' => 'Jane Nurse',
                'password' => Hash::make('password'),
            ]
        );
        $teacher1->assignRole('teacher');

        StaffProfile::firstOrCreate(
            ['user_id' => $teacher1->id],
            [
                'staff_id' => 'STF001',
                'department_id' => $nursing->id,
                'phone' => '0700000001',
                'gender' => 'F',
                'status' => 'active',
                'employed_at' => now()->subYears(2),
            ]
        );

        // Teacher 2: Dentistry
        $teacher2 = User::firstOrCreate(
            ['email' => 'teacher.dental@kibihas.ac.tz'],
            [
                'name' => 'John Dentist',
                'password' => Hash::make('password'),
            ]
        );
        $teacher2->assignRole('teacher');

        StaffProfile::firstOrCreate(
            ['user_id' => $teacher2->id],
            [
                'staff_id' => 'STF002',
                'department_id' => $dentistry->id,
                'phone' => '0700000002',
                'gender' => 'M',
                'status' => 'active',
                'employed_at' => now()->subYears(1),
            ]
        );
    }
}
