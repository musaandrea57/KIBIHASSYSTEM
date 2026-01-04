<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\FeeItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Services\FeeClearanceService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FeeClearanceSeeder extends Seeder
{
    public function run(): void
    {
        // Cleanup existing test data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Invoice::whereIn('invoice_number', ['INV-NC001', 'INV-CL001'])->delete();
        Payment::where('payment_reference', 'PAY-CL001')->delete();
        // Also cleanup students if needed, or just let firstOrCreate handle them.
        // If we delete invoices, we should be fine re-creating them.
        // However, invoice items are cascaded? 
        // Invoice migration has cascadeOnDelete for invoice_items.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 0. Ensure Accountant Exists
        $accountantUser = User::firstOrCreate(
            ['email' => 'accountant@kibihas.ac.tz'],
            ['name' => 'Accountant User', 'password' => Hash::make('password')]
        );
        $accountantUser->assignRole('accountant');

        // 1. Setup Context
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['start_date' => '2025-10-01', 'end_date' => '2026-09-30', 'is_current' => true]
        );

        $semester = Semester::firstOrCreate(
            ['name' => 'Semester 1'],
            ['start_date' => '2025-10-01', 'end_date' => '2026-02-28', 'is_active' => true, 'number' => 1]
        );

        $program = Program::first();
        if (!$program) {
            $program = Program::create(['code' => 'CS', 'name' => 'Computer Science', 'department_id' => 1, 'duration_years' => 3, 'nta_level' => 7]);
        }

        // 2. Create Fee Item (Tuition)
        // Check schema of fee_items table first. Based on overhaul migration:
        // name, code, category, default_description, is_active
        $tuitionItem = FeeItem::firstOrCreate(
            ['name' => 'Tuition Fee Sem 1'],
            ['code' => 'TF-S1', 'category' => 'tuition', 'default_description' => 'Tuition Fee for Semester 1']
        );

        // 3. Create Student A: Not Cleared (Unpaid Invoice)
        $userA = User::firstOrCreate(
            ['email' => 'student.notcleared@kibihas.ac.tz'],
            ['name' => 'Student Not Cleared', 'password' => Hash::make('password')]
        );
        $userA->assignRole('student');

        $studentA = Student::firstOrCreate(
            ['user_id' => $userA->id],
            [
                'first_name' => 'Student',
                'last_name' => 'Not Cleared',
                'registration_number' => 'NC001',
                'program_id' => $program->id,
                'current_nta_level' => 4,
                'current_academic_year_id' => $academicYear->id,
                'current_semester_id' => $semester->id,
                'gender' => 'Male',
                'date_of_birth' => '2000-01-01'
            ]
        );

        // Create Invoice for Student A
        $invoiceA = Invoice::create([
            'student_id' => $studentA->id,
            'program_id' => $program->id,
            'nta_level' => 4,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'invoice_number' => 'INV-NC001',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'currency' => 'TZS',
            'subtotal' => 1000000,
            'total_paid' => 0,
            'balance' => 1000000,
            'status' => 'unpaid',
            'created_by' => 1,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoiceA->id,
            'fee_item_id' => $tuitionItem->id,
            'description' => $tuitionItem->name,
            'amount' => 1000000,
            'paid_amount' => 0,
        ]);

        // 4. Create Student B: Cleared (Fully Paid Invoice)
        $userB = User::firstOrCreate(
            ['email' => 'student.cleared@kibihas.ac.tz'],
            ['name' => 'Student Cleared', 'password' => Hash::make('password')]
        );
        $userB->assignRole('student');

        $studentB = Student::firstOrCreate(
            ['user_id' => $userB->id],
            [
                'first_name' => 'Student',
                'last_name' => 'Cleared',
                'registration_number' => 'CL001',
                'program_id' => $program->id,
                'current_nta_level' => 4,
                'current_academic_year_id' => $academicYear->id,
                'current_semester_id' => $semester->id,
                'gender' => 'Female',
                'date_of_birth' => '2000-01-01'
            ]
        );

        // Create Invoice for Student B
        $invoiceB = Invoice::create([
            'student_id' => $studentB->id,
            'program_id' => $program->id,
            'nta_level' => 4,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'invoice_number' => 'INV-CL001',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'currency' => 'TZS',
            'subtotal' => 1000000,
            'total_paid' => 1000000,
            'balance' => 0,
            'status' => 'paid',
            'created_by' => 1,
        ]);

        $invoiceItemB = InvoiceItem::create([
            'invoice_id' => $invoiceB->id,
            'fee_item_id' => $tuitionItem->id,
            'description' => $tuitionItem->name,
            'amount' => 1000000,
            'paid_amount' => 1000000,
        ]);

        // Create Payment for Student B (Full Payment)
        $payment = Payment::create([
            'payment_reference' => 'PAY-CL001',
            'student_id' => $studentB->id,
            'invoice_id' => $invoiceB->id,
            'amount' => 1000000,
            'payment_date' => now(),
            'method' => 'bank',
            'transaction_ref' => 'TRX-001',
            'status' => 'posted',
            'received_by' => $accountantUser->id,
        ]);
        
        // Note: Observer should automatically update clearance status.
        // But we can trigger it manually just in case observer wasn't running during seeding (it usually is).
        $feeService = new FeeClearanceService();
        $feeService->refreshSnapshot($studentA, $academicYear, $semester);
        $feeService->refreshSnapshot($studentB, $academicYear, $semester);
    }
}
