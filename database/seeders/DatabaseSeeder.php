<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            AcademicStructureSeeder::class, // Creates programs, years, modules
            TeacherSeeder::class, // Needs departments
            ModuleAssignmentSeeder::class, // Needs teachers and offerings
            SemesterRegistrationSeeder::class,
            FinanceSeeder::class,
            AnnouncementSeeder::class,
            ApplicantSeeder::class,
            Module7Seeder::class,
            Module8Seeder::class,
        ]);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@kibihas.ac.tz'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Principal
        $principal = User::firstOrCreate(
            ['email' => 'principal@kibihas.ac.tz'],
            [
                'name' => 'Dr. Principal',
                'password' => Hash::make('password'),
            ]
        );
        $principal->assignRole('principal');

        // Academic Staff
        $academicStaff = User::firstOrCreate(
            ['email' => 'academic@kibihas.ac.tz'],
            [
                'name' => 'Academic Registrar',
                'password' => Hash::make('password'),
            ]
        );
        $academicStaff->assignRole('academic_staff');
    }
}
