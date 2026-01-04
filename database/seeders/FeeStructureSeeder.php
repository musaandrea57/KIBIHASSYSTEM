<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\FeeItem;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\BankAccount;
use Carbon\Carbon;

class FeeStructureSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Fee Items exist
        $items = [
            ['name' => 'Tuition Fee', 'category' => 'tuition'],
            ['name' => 'Registration Fee', 'category' => 'other'],
            ['name' => 'Identity Card', 'category' => 'other'],
            ['name' => 'Student Union (KISO)', 'category' => 'other'],
            ['name' => 'Caution Money', 'category' => 'other'],
            ['name' => 'Quality Assurance (NACTVET)', 'category' => 'other'],
            ['name' => 'Health Insurance (NHIF)', 'category' => 'other'],
            ['name' => 'Clinical Rotation', 'category' => 'other'],
            ['name' => 'Hostel/Accommodation', 'category' => 'other'],
        ];

        foreach ($items as $item) {
            FeeItem::firstOrCreate(
                ['name' => $item['name']],
                ['category' => $item['category'], 'is_active' => true]
            );
        }

        // 2. Ensure Bank Accounts exist
        $tuitionAcc = BankAccount::firstOrCreate(
            ['account_number' => '0150222222200'],
            ['bank_name' => 'CRDB', 'account_name' => 'KIBIHAS TUITION', 'is_active' => true]
        );
        $otherAcc = BankAccount::firstOrCreate(
            ['account_number' => '0150333333300'],
            ['bank_name' => 'CRDB', 'account_name' => 'KIBIHAS MISC', 'is_active' => true]
        );

        // 3. Get Context
        $academicYear = AcademicYear::where('name', '2025/2026')->first();
        if (!$academicYear) {
            // Create if missing for seeding purpose
            $academicYear = AcademicYear::create(['name' => '2025/2026', 'start_date' => '2025-10-01', 'end_date' => '2026-09-30', 'status' => 'active']);
        }
        
        $semester = Semester::where('name', 'Semester 1')->first();
        if (!$semester) {
             $semester = Semester::create(['name' => 'Semester 1', 'number' => 1, 'is_active' => true]);
        }

        $programs = Program::whereIn('code', ['NMT', 'CDT', 'CRT'])->get();

        // 4. Create Fee Structures
        foreach ($programs as $program) {
            // Check if exists
            $exists = FeeStructure::where('program_id', $program->id)
                ->where('nta_level', 4)
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semester->id)
                ->exists();

            if ($exists) continue;

            $structure = FeeStructure::create([
                'program_id' => $program->id,
                'nta_level' => 4,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'name' => "{$program->code} NTA 4 Semester I 2025/2026",
                'status' => 'active',
                'version' => 1,
                'published_at' => Carbon::now(),
                'created_by' => 1, // System or Admin
            ]);

            // Add Items (Default Values as placeholders - User should edit)
            // Tuition: 1,500,000 Total -> 500k, 500k, 500k
            $tuitionItem = FeeItem::where('name', 'Tuition Fee')->first();
            FeeStructureItem::create([
                'fee_structure_id' => $structure->id,
                'fee_item_id' => $tuitionItem->id,
                'amount' => 1500000,
                'amount_oct' => 500000,
                'amount_jan' => 500000,
                'amount_apr' => 500000,
                'is_mandatory' => true,
                'bank_account_id' => $tuitionAcc->id,
                'sort_order' => 1,
            ]);

            // Registration: 20,000 (Oct only)
            $regItem = FeeItem::where('name', 'Registration Fee')->first();
            FeeStructureItem::create([
                'fee_structure_id' => $structure->id,
                'fee_item_id' => $regItem->id,
                'amount' => 20000,
                'amount_oct' => 20000,
                'amount_jan' => 0,
                'amount_apr' => 0,
                'is_mandatory' => true,
                'bank_account_id' => $otherAcc->id,
                'sort_order' => 2,
            ]);

            // Other items...
            $others = [
                'Identity Card' => 10000,
                'Student Union (KISO)' => 10000,
                'Quality Assurance (NACTVET)' => 15000,
                'Health Insurance (NHIF)' => 50400,
                'Caution Money' => 20000,
            ];

            $order = 3;
            foreach ($others as $name => $amount) {
                $fItem = FeeItem::where('name', $name)->first();
                FeeStructureItem::create([
                    'fee_structure_id' => $structure->id,
                    'fee_item_id' => $fItem->id,
                    'amount' => $amount,
                    'amount_oct' => $amount, // Usually paid in first installment
                    'amount_jan' => 0,
                    'amount_apr' => 0,
                    'is_mandatory' => true,
                    'bank_account_id' => $otherAcc->id,
                    'sort_order' => $order++,
                ]);
            }
        }
    }
}
