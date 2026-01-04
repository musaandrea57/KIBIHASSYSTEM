<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeScale;

class GradeScaleSeeder extends Seeder
{
    public function run()
    {
        $scales = [
            [
                'grade' => 'A',
                'min_mark' => 80,
                'max_mark' => 100,
                'grade_point' => 4.0,
                'definition' => 'Excellent',
            ],
            [
                'grade' => 'B',
                'min_mark' => 65,
                'max_mark' => 79,
                'grade_point' => 3.0,
                'definition' => 'Good',
            ],
            [
                'grade' => 'C',
                'min_mark' => 50,
                'max_mark' => 64,
                'grade_point' => 2.0,
                'definition' => 'Satisfactory',
            ],
            [
                'grade' => 'D',
                'min_mark' => 40,
                'max_mark' => 49,
                'grade_point' => 1.0,
                'definition' => 'Poor',
            ],
            [
                'grade' => 'F',
                'min_mark' => 0,
                'max_mark' => 39,
                'grade_point' => 0.0,
                'definition' => 'Failure',
            ],
        ];

        foreach ($scales as $scale) {
            GradeScale::updateOrCreate(
                ['grade' => $scale['grade']],
                $scale
            );
        }
    }
}
