<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SemesterRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FeeStructureBillingService
{
    /**
     * Generate an invoice for a student registration based on the active fee structure.
     *
     * @param SemesterRegistration $registration
     * @return Invoice|null
     */
    public function generateInvoiceForRegistration(SemesterRegistration $registration)
    {
        return DB::transaction(function () use ($registration) {
            // 1. Find Active Fee Structure
            $structure = FeeStructure::where('program_id', $registration->program_id)
                ->where('nta_level', $registration->nta_level)
                ->where('academic_year_id', $registration->academic_year_id)
                ->where('semester_id', $registration->semester_id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if (!$structure) {
                Log::warning("No active fee structure found for Registration ID: {$registration->id}");
                return null;
            }

            // 2. Check if invoice already exists for this context
            $existingInvoice = Invoice::where('student_id', $registration->student_id)
                ->where('academic_year_id', $registration->academic_year_id)
                ->where('semester_id', $registration->semester_id)
                ->where('status', '!=', 'voided')
                ->first();

            if ($existingInvoice) {
                Log::info("Invoice already exists for Registration ID: {$registration->id}");
                return $existingInvoice;
            }

            // 3. Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'student_id' => $registration->student_id,
                'program_id' => $registration->program_id,
                'nta_level' => $registration->nta_level,
                'academic_year_id' => $registration->academic_year_id,
                'semester_id' => $registration->semester_id,
                'issue_date' => now(),
                'due_date' => now()->addDays(30), // Default due date
                'status' => 'unpaid',
                'created_by' => Auth::id() ?? 1, // Fallback for system actions
            ]);

            // 4. Create Invoice Items from Structure Items
            foreach ($structure->items as $item) {
                // Determine amount: Use total_amount (sum of installments)
                $amount = $item->total_amount;

                if ($amount > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'fee_item_id' => $item->fee_item_id,
                        'description' => $item->feeItem->name ?? 'Fee Item',
                        'amount' => $amount,
                        'sort_order' => $item->sort_order,
                    ]);
                }
            }

            $invoice->recalculateTotals();

            FinanceAuditService::log('generate_invoice', Invoice::class, $invoice->id, [
                'registration_id' => $registration->id,
                'fee_structure_id' => $structure->id
            ], "Invoice generated from Fee Structure");

            return $invoice;
        });
    }

    private function generateInvoiceNumber()
    {
        // Format: INV-YYYYMMDD-XXXX
        $prefix = 'INV-' . date('Ymd') . '-';
        
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
            
        if ($lastInvoice) {
            $lastSequence = intval(substr($lastInvoice->invoice_number, -4));
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
