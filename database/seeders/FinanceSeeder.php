<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Program;
use App\Models\Receipt;
use App\Models\Semester;
use App\Models\Student;
use App\Services\FinanceAuditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Fee Items
        $this->command->info('Seeding Fee Items...');
        $feeItems = [
            ['name' => 'Tuition Fee', 'code' => 'TF', 'amount' => 1200000, 'category' => 'tuition', 'is_active' => true],
            ['name' => 'Registration Fee', 'code' => 'REG', 'amount' => 20000, 'category' => 'mandatory', 'is_active' => true],
            ['name' => 'Student Union', 'code' => 'SU', 'amount' => 10000, 'category' => 'mandatory', 'is_active' => true],
            ['name' => 'Caution Money', 'code' => 'CM', 'amount' => 50000, 'category' => 'one_time', 'is_active' => true],
            ['name' => 'NHIF', 'code' => 'NHIF', 'amount' => 50400, 'category' => 'insurance', 'is_active' => true],
            ['name' => 'Examination Fee', 'code' => 'EXAM', 'amount' => 150000, 'category' => 'mandatory', 'is_active' => true],
            ['name' => 'Clinical Rotation', 'code' => 'ROT', 'amount' => 200000, 'category' => 'other', 'is_active' => true],
            ['name' => 'Identity Card', 'code' => 'ID', 'amount' => 5000, 'category' => 'one_time', 'is_active' => true],
        ];

        foreach ($feeItems as $item) {
            $data = $item;
            unset($data['amount']);
            FeeItem::firstOrCreate(['code' => $item['code']], $data);
        }

        // 2. Create Fee Structures
        $this->command->info('Seeding Fee Structures...');
        $academicYear = AcademicYear::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();
        $programs = Program::all();

        if (!$academicYear || !$semester || $programs->isEmpty()) {
            $this->command->warn('Missing active academic year, semester, or programs. Skipping Fee Structures.');
            return;
        }

        foreach ($programs as $program) {
            // Create structure for NTA Level 4, 5, 6
            foreach ([4, 5, 6] as $level) {
                // Check if exists
                $exists = FeeStructure::where('program_id', $program->id)
                    ->where('nta_level', $level)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('semester_id', $semester->id)
                    ->exists();

                if ($exists) continue;

                $structure = FeeStructure::create([
                    'program_id' => $program->id,
                    'nta_level' => $level,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'name' => "{$program->code} NTA Level {$level} - {$academicYear->year} ({$semester->name})",
                    'status' => 'active',
                    'created_by' => 1,
                ]);

                // Attach items
                $itemsToAdd = [
                    ['code' => 'TF', 'amount' => 1200000], // Full tuition
                    ['code' => 'REG', 'amount' => 20000],
                    ['code' => 'SU', 'amount' => 10000],
                    ['code' => 'EXAM', 'amount' => 150000],
                ];

                if ($level == 4) { // First years pay caution money and ID
                    $itemsToAdd[] = ['code' => 'CM', 'amount' => 50000];
                    $itemsToAdd[] = ['code' => 'ID', 'amount' => 5000];
                }

                if ($program->code === 'CM' || $program->code === 'NURS') { // Clinical Medicine & Nursing pay rotation
                    $itemsToAdd[] = ['code' => 'ROT', 'amount' => 200000];
                }

                foreach ($itemsToAdd as $index => $addItem) {
                    $feeItem = FeeItem::where('code', $addItem['code'])->first();
                    if ($feeItem) {
                        // Calculate installments for Tuition (TF)
                        $amountOct = $addItem['amount'];
                        $amountJan = 0;
                        $amountApr = 0;

                        if ($addItem['code'] === 'TF') {
                            // Split Tuition into 3 equal installments
                            $installment = $addItem['amount'] / 3;
                            $amountOct = $installment;
                            $amountJan = $installment;
                            $amountApr = $installment;
                        }

                        FeeStructureItem::create([
                            'fee_structure_id' => $structure->id,
                            'fee_item_id' => $feeItem->id,
                            'amount' => $addItem['amount'],
                            'amount_oct' => $amountOct,
                            'amount_jan' => $amountJan,
                            'amount_apr' => $amountApr,
                            'is_mandatory' => $feeItem->category === 'mandatory' || $feeItem->category === 'tuition',
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        }

        // 3. Generate Invoices for Students
        $this->command->info('Generating Invoices for Students...');
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->info('No students found. Creating a test student...');
            $program = Program::first();
            if (!$program) {
                // Should have been handled above, but just in case
                return;
            }
            
            // Create a user first if needed, or assume existing user logic from DatabaseSeeder
            // For simplicity, let's create a student record linked to user 1 if possible, or just a dummy
            $student = Student::create([
                'user_id' => 1, // Assuming user 1 exists
                'program_id' => $program->id,
                'admission_number' => 'STU-' . date('Y') . '-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'M',
                'dob' => '2000-01-01',
                'phone' => '0700000000',
                'address' => 'Kibaha',
                'nationality' => 'Tanzanian',
                'is_active' => true,
            ]);
            $students = collect([$student]);
        }

        foreach ($students as $student) {
            // Find matching fee structure
            $structure = FeeStructure::where('program_id', $student->program_id)
                // Assuming students have nta_level field or we infer it. 
                // For seeding, let's assume all are NTA Level 4 if not specified, or randomize/use logic.
                // The student table might not have nta_level yet? Let's check.
                // Ah, user said "Fee Structures must be defined by... NTA Level".
                // Student model should track current level.
                // Let's assume NTA 4 for now if missing.
                ->where('nta_level', 4) 
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semester->id)
                ->where('status', 'active')
                ->first();

            if (!$structure) continue;

            // Check if invoice exists
            $invoiceExists = Invoice::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semester->id)
                ->exists();

            if ($invoiceExists) continue;

            $invoice = DB::transaction(function () use ($student, $structure, $academicYear, $semester) {
                // Generate Invoice Number
                $prefix = 'INV-' . date('Y') . '-';
                $last = Invoice::where('invoice_number', 'like', $prefix . '%')->count();
                $number = $prefix . str_pad($last + 1 + $student->id, 5, '0', STR_PAD_LEFT); // Add student id to avoid collision in loop

                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'program_id' => $structure->program_id,
                    'nta_level' => $structure->nta_level,
                    'academic_year_id' => $structure->academic_year_id,
                    'semester_id' => $semester->id,
                    'fee_structure_id' => $structure->id,
                    'invoice_number' => $number,
                    'issue_date' => now(),
                    'due_date' => now()->addDays(30),
                    'currency' => 'TZS',
                    'status' => 'unpaid',
                    'created_by' => 1,
                ]);

                foreach ($structure->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'fee_item_id' => $item->fee_item_id,
                        'description' => $item->feeItem->name,
                        'amount' => $item->amount,
                        'paid_amount' => 0,
                        'sort_order' => $item->sort_order,
                    ]);
                }

                $invoice->recalculateTotals();
                return $invoice;
            });

            // 4. Create Payments for some students
            if ($student->id % 2 == 0) { // Pay for even IDs
                $amountToPay = $invoice->total_amount * 0.5; // Pay 50%
                
                $this->createPayment($student, $invoice, $amountToPay);
            }
        }
    }

    private function createPayment($student, $invoice, $amount)
    {
        DB::transaction(function () use ($student, $invoice, $amount) {
            $prefix = 'PAY-' . date('Ymd') . '-';
            $count = Payment::where('payment_reference', 'like', $prefix . '%')->count();
            $ref = $prefix . str_pad($count + 1 + $student->id, 4, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                'payment_reference' => $ref,
                'student_id' => $student->id,
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'method' => 'bank',
                'transaction_ref' => 'TXN' . rand(100000, 999999),
                'amount' => $amount,
                'received_by' => 1,
                'status' => 'posted',
            ]);

            // Allocate
            $remaining = $amount;
            $sortedItems = $invoice->items->sortBy('sort_order');

            foreach ($sortedItems as $item) {
                if ($remaining <= 0) break;
                
                $balance = $item->amount - $item->paid_amount;
                if ($balance <= 0) continue;

                $allocate = min($balance, $remaining);
                
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'invoice_item_id' => $item->id,
                    'amount' => $allocate,
                ]);

                $item->paid_amount += $allocate;
                $item->save();
                
                $remaining -= $allocate;
            }

            $invoice->recalculateTotals();

            // Receipt
            $rcptPrefix = 'RCPT-' . date('Ymd') . '-';
            $rcptCount = Receipt::where('receipt_number', 'like', $rcptPrefix . '%')->count();
            $rcptNum = $rcptPrefix . str_pad($rcptCount + 1 + $student->id, 4, '0', STR_PAD_LEFT);

            Receipt::create([
                'receipt_number' => $rcptNum,
                'payment_id' => $payment->id,
                'student_id' => $student->id,
                'invoice_id' => $invoice->id,
                'issued_at' => now(),
                'issued_by' => 1,
            ]);
            
            FinanceAuditService::log('seed_payment', Payment::class, $payment->id, ['amount' => $amount]);
        });
    }
}
