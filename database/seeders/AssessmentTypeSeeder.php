<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentType;

class AssessmentTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'code' => 'CW',
                'name' => 'Continuous Assessment',
                'weight' => 40,
                'description' => 'Tests, Quizzes, Assignments',
            ],
            [
                'code' => 'SE',
                'name' => 'Semester Examination',
                'weight' => 60,
                'description' => 'Final Examination',
            ],
        ];

        foreach ($types as $type) {
            AssessmentType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
