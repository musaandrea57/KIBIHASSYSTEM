<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Nursing & Midwifery', 'code' => 'NUR'],
            ['name' => 'Clinical Dentistry', 'code' => 'DEN'],
            ['name' => 'Diagnostic Radiography', 'code' => 'RAD'],
            ['name' => 'Basic Sciences', 'code' => 'SCI'],
            ['name' => 'Administration', 'code' => 'ADM'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                ['name' => $dept['name'], 'is_active' => true]
            );
        }
    }
}
