<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Module;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Programs
        $nursing = Program::create(['name' => 'Diploma in Nursing and Midwifery', 'code' => 'DNM', 'duration_years' => 3]);
        $dentistry = Program::create(['name' => 'Diploma in Clinical Dentistry', 'code' => 'DCD', 'duration_years' => 3]);
        $radiography = Program::create(['name' => 'Diploma in Diagnostic Radiography', 'code' => 'DDR', 'duration_years' => 3]);

        // Academic Years
        $y25 = AcademicYear::create(['name' => '2025/2026', 'start_date' => '2025-09-01', 'end_date' => '2026-07-30', 'is_active' => true]);
        
        Semester::create(['name' => 'Semester 1', 'number' => 1, 'is_active' => true]);
        Semester::create(['name' => 'Semester 2', 'number' => 2, 'is_active' => false]);

        // Modules - Nursing NTA 4 Sem 1
        Module::create(['code' => 'NUR04101', 'name' => 'Anatomy and Physiology', 'credits' => 12, 'program_id' => $nursing->id, 'nta_level' => 4, 'semester_number' => 1]);
        Module::create(['code' => 'NUR04102', 'name' => 'Basic Nursing Skills', 'credits' => 15, 'program_id' => $nursing->id, 'nta_level' => 4, 'semester_number' => 1]);
        
        // Modules - Dentistry NTA 4 Sem 1
        Module::create(['code' => 'DENT04101', 'name' => 'Dental Anatomy', 'credits' => 10, 'program_id' => $dentistry->id, 'nta_level' => 4, 'semester_number' => 1]);

        // Modules - Radiography NTA 4 Sem 1
        Module::create(['code' => 'RAD04101', 'name' => 'Radiation Physics', 'credits' => 10, 'program_id' => $radiography->id, 'nta_level' => 4, 'semester_number' => 1]);
    }
}
