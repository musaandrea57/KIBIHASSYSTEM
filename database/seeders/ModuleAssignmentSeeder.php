<?php

namespace Database\Seeders;

use App\Models\ModuleAssignment;
use App\Models\ModuleOffering;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModuleAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@kibihas.ac.tz')->first();
        $teacher1 = User::where('email', 'teacher.nursing@kibihas.ac.tz')->first();
        $teacher2 = User::where('email', 'teacher.dental@kibihas.ac.tz')->first();

        if (!$admin || !$teacher1 || !$teacher2) return;

        // Get some module offerings
        $offerings = ModuleOffering::take(5)->get();

        foreach ($offerings as $index => $offering) {
            // Alternate teachers
            $teacher = ($index % 2 == 0) ? $teacher1 : $teacher2;

            ModuleAssignment::firstOrCreate(
                [
                    'module_offering_id' => $offering->id,
                    'status' => 'active',
                ],
                [
                    'teacher_user_id' => $teacher->id,
                    'assigned_by_user_id' => $admin->id,
                    'assigned_at' => now(),
                ]
            );
        }
    }
}
