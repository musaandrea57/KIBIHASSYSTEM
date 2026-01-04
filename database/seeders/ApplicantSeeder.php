<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $program = Program::first();
        if (!$program) {
            return;
        }

        $user = User::create([
            'name' => 'John Applicant',
            'email' => 'applicant@kibihas.ac.tz',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('applicant');

        Application::create([
            'application_number' => 'KIBIHAS-APP-' . date('Y') . '-000001',
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Applicant',
            'email' => 'applicant@kibihas.ac.tz',
            'phone' => '0700000000',
            'program_id' => $program->id,
            'status' => 'submitted',
            'biodata' => [
                'dob' => '2000-01-01',
                'gender' => 'Male',
                'nationality' => 'Tanzanian',
                'address' => 'Moshi, Kilimanjaro',
            ],
            'education_background' => [
                'index_number' => 'S0101/0001/2020',
                'school_name' => 'Moshi Secondary School',
                'completion_year' => 2020,
            ],
            'documents' => [
                'passport_photo' => 'applications/photos/sample.jpg',
                'csee_certificate' => 'applications/certificates/sample.pdf',
            ],
        ]);
    }
}
