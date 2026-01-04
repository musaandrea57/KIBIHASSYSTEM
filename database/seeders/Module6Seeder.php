<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Module6Seeder extends Seeder
{
    public function run()
    {
        $this->call([
            GradeScaleSeeder::class,
            AssessmentTypeSeeder::class,
        ]);
    }
}
