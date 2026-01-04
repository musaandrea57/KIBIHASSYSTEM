<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Module;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ModuleOffering;
use Illuminate\Support\Facades\DB;

use App\Models\ProgrammeLevelRule;

class AcademicStructureSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 0. Define Academic Year & Semesters
            $year = AcademicYear::firstOrCreate(
                ['name' => '2025/2026'],
                [
                    'start_date' => '2025-09-01',
                    'end_date' => '2026-08-31',
                    'is_active' => true,
                    'is_current' => true,
                ]
            );

            $sem1 = Semester::firstOrCreate(
                ['name' => 'Semester 1'],
                ['is_active' => true, 'number' => 1]
            );
            
            $sem2 = Semester::firstOrCreate(
                ['name' => 'Semester 2'],
                ['is_active' => false, 'number' => 2]
            );

            // 1. Define Programmes
            $validProgramCodes = ['CRT', 'NMT', 'CDT'];
            
            // Deactivate legacy programs
            Program::whereNotIn('code', $validProgramCodes)->update(['is_active' => false]);

            $programmes = [
                [
                    'name' => 'Diploma in Diagnostic Radiography',
                    'code' => 'CRT',
                    'duration_years' => 3, 
                    'min_credits_per_semester' => 12,
                    'max_credits_per_semester' => 30,
                    'levels' => [4], // Only NTA 4 modules defined in prompt
                ],
                [
                    'name' => 'Diploma in Nursing and Midwifery',
                    'code' => 'NMT',
                    'duration_years' => 3, 
                    'min_credits_per_semester' => 12,
                    'max_credits_per_semester' => 30,
                    'levels' => [4, 5, 6],
                ],
                [
                    'name' => 'Diploma in Clinical Dentistry',
                    'code' => 'CDT',
                    'duration_years' => 3, 
                    'min_credits_per_semester' => 12,
                    'max_credits_per_semester' => 30,
                    'levels' => [4, 5, 6],
                ],
            ];

            foreach ($programmes as $progData) {
                $levels = $progData['levels'];
                unset($progData['levels']);

                $program = Program::updateOrCreate(
                    ['code' => $progData['code']],
                    array_merge($progData, ['is_active' => true])
                );

                // Create ProgrammeLevelRules
                foreach ($levels as $level) {
                    ProgrammeLevelRule::updateOrCreate(
                        [
                            'program_id' => $program->id,
                            'nta_level' => $level,
                        ],
                        [
                            'min_credits' => $progData['min_credits_per_semester'],
                            'max_credits' => $progData['max_credits_per_semester'],
                        ]
                    );
                }
            }

            // 2. Define Modules
            // Assumption: semester_number defaults to 1 based on 'x1xx' pattern, 
            // or we distribute them if that's more realistic for a full year. 
            // Given "strict" constraint and codes like 041xx, I will treat them as defined by their code.
            // If code is 041xx -> Sem 1. If code was 042xx -> Sem 2. 
            // Since all are 041xx, 051xx, 061xx, they are all mapped to Semester 1.
            
            $modules = [
                // A. DIPLOMA IN DIAGNOSTIC RADIOGRAPHY (CRT)
                ['code' => 'CRT 04101', 'name' => 'Anatomy, Physiology and Pathology', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CRT 04102', 'name' => 'Patient Management', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CRT 04103', 'name' => 'Radiographic Techniques and Procedures', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CRT 04104', 'name' => 'Radiology and Imaging Equipments', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CRT 04105', 'name' => 'Radiographic Imaging Science', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CRT 04106', 'name' => 'Radiation Science', 'program_code' => 'CRT', 'nta_level' => 4, 'credits' => 10],

                // B. DIPLOMA IN NURSING AND MIDWIFERY (NMT)
                // NTA 4
                ['code' => 'NMT 04101', 'name' => 'Infection Prevention and Control', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'NMT 04102', 'name' => 'Professionalism in Nursing', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'NMT 04103', 'name' => 'Human Anatomy and Physiology', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'NMT 04104', 'name' => 'Basic Computer Application', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'NMT 04105', 'name' => 'Communication Skills', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'NMT 04106', 'name' => 'Parasitology and Entomology', 'program_code' => 'NMT', 'nta_level' => 4, 'credits' => 10],
                // NTA 5
                ['code' => 'NMT 05101', 'name' => 'Reproductive Health Care', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05102', 'name' => 'Child Health Services', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05103', 'name' => 'Care of a Sick Child', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05104', 'name' => 'Care of a Patient with Medical Conditions', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05105', 'name' => 'Basic Care of Patient with Surgical Conditions', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05106', 'name' => 'Basic Mental Health Nursing', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'NMT 05107', 'name' => 'Care of a Woman During Antenatal Period', 'program_code' => 'NMT', 'nta_level' => 5, 'credits' => 10],
                // NTA 6
                ['code' => 'NMT 06101', 'name' => 'Care of Woman with Abnormal Pregnancy, Labor & Puerperium', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'NMT 06102', 'name' => 'Care of a Woman with Obstetric Emergency', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'NMT 06103', 'name' => 'Care of a Newborn with Abnormal Conditions', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'NMT 06104', 'name' => 'Supervision in Nursing & Midwifery', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'NMT 06105', 'name' => 'Epidemiology and Biostatistics', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'NMT 06106', 'name' => 'Fundamentals of Research', 'program_code' => 'NMT', 'nta_level' => 6, 'credits' => 10],

                // C. DIPLOMA IN CLINICAL DENTISTRY (CDT)
                // NTA 4
                ['code' => 'CDT 04101', 'name' => 'Anatomy and Physiology', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04102', 'name' => 'Oral Anatomy and Physiology', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04103', 'name' => 'Computer Application', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04104', 'name' => 'Microbiology, Immunology and Parasitology', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04105', 'name' => 'Infection Prevention and Control', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04106', 'name' => 'Communication and Customer Care', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                ['code' => 'CDT 04107', 'name' => 'Medical Ethics and Professionalism', 'program_code' => 'CDT', 'nta_level' => 4, 'credits' => 10],
                // NTA 5
                ['code' => 'CDT 05101', 'name' => 'Pharmacology and Pharmacy Practice', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'CDT 05102', 'name' => 'Management of Communicable Diseases', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'CDT 05103', 'name' => 'Management of Non-Communicable Diseases', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'CDT 05104', 'name' => 'Surgery', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'CDT 05105', 'name' => 'Periodontology', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                ['code' => 'CDT 05106', 'name' => 'Nutrition', 'program_code' => 'CDT', 'nta_level' => 5, 'credits' => 10],
                // NTA 6
                ['code' => 'CDT 06101', 'name' => 'Applied Oral Surgery', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'CDT 06102', 'name' => 'Operative Dentistry', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'CDT 06103', 'name' => 'Management of Medical Conditions', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'CDT 06104', 'name' => 'Forensic Medicine', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'CDT 06105', 'name' => 'Operational Research', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
                ['code' => 'CDT 06106', 'name' => 'Leadership and Management', 'program_code' => 'CDT', 'nta_level' => 6, 'credits' => 10],
            ];

            foreach ($modules as $modData) {
                $program = Program::where('code', $modData['program_code'])->first();
                if (!$program) continue;

                // Deduce semester from code? 
                // x41xx -> Sem 1. x42xx -> Sem 2.
                // All codes here are x41xx, x51xx, x61xx.
                // So all are Semester 1.
                $semesterNumber = 1;

                $module = Module::updateOrCreate(
                    ['code' => $modData['code']],
                    [
                        'name' => $modData['name'],
                        'program_id' => $program->id,
                        'nta_level' => $modData['nta_level'],
                        'credits' => $modData['credits'],
                        'semester_number' => $semesterNumber,
                    ]
                );

                // Create Offering for Current Year
                // Since all are Sem 1 modules, we assign to Sem 1
                ModuleOffering::firstOrCreate(
                    [
                        'module_id' => $module->id,
                        'academic_year_id' => $year->id,
                        'semester_id' => $sem1->id,
                    ],
                    [
                        'nta_level' => $module->nta_level,
                        'status' => 'active',
                    ]
                );
            }
        });
    }
}
