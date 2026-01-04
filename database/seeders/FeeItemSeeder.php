<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeItem;

class FeeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Tuition Fee', 'default_description' => 'Tuition fee for the semester'],
            ['name' => 'Registration Fee', 'default_description' => 'Semester registration fee'],
            ['name' => 'Identity Card', 'default_description' => 'Student ID card issuance'],
            ['name' => 'Examination Fee', 'default_description' => 'Fee for end of semester examinations'],
            ['name' => 'Student Union Fee', 'default_description' => 'Contribution to student government'],
            ['name' => 'Medical Fee', 'default_description' => 'Basic medical services coverage'],
            ['name' => 'Library Fee', 'default_description' => 'Library access and maintenance'],
            ['name' => 'Field Attachment Fee', 'default_description' => 'Practical field training supervision'],
            ['name' => 'Graduation Fee', 'default_description' => 'Graduation ceremony contribution'],
            ['name' => 'Caution Money', 'default_description' => 'Refundable deposit for damages'],
        ];

        foreach ($items as $item) {
            FeeItem::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
