<?php

namespace App\Services;

use App\Models\Student;
use App\Models\HostelFeesConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SemesterRegistration;
use App\Models\FeeItem;
use Illuminate\Support\Facades\Log;
use App\Services\FinanceAuditService;

class HostelBillingService
{
    /**
     * Ensure a student is charged for hostel if configuration exists.
     * 
     * @param Student $student
     * @param int $academicYearId
     * @param int $semesterId
     * @param string $description Context description (e.g., hostel name)
     * @return InvoiceItem|null
     */
    public function ensureHostelCharge(Student $student, $academicYearId, $semesterId, $description = 'Hostel Fee')
    {
        // 1. Find applicable fee configuration
        $config = HostelFeesConfig::where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where(function($q) use ($student) {
                $q->where('program_id', $student->program_id)
                  ->orWhereNull('program_id');
            })
            ->orderByDesc('program_id') // Specific program config takes precedence
            ->first();

        if (!$config || $config->amount <= 0) {
            Log::info("No hostel fee config found for Student {$student->id}, Year {$academicYearId}, Sem {$semesterId}");
            return null;
        }

        // 2. Check for existing hostel charge in this semester's invoices
        $existingItem = InvoiceItem::whereHas('invoice', function($q) use ($student, $academicYearId, $semesterId) {
                $q->where('student_id', $student->id)
                  ->where('academic_year_id', $academicYearId)
                  ->where('semester_id', $semesterId)
                  ->where('status', '!=', 'void');
            })
            ->where('fee_item_id', $config->fee_item_id)
            ->first();

        if ($existingItem) {
            Log::info("Hostel fee already exists for Student {$student->id}. Item ID: {$existingItem->id}");
            return $existingItem;
        }

        // 3. Find or Create Invoice
        // Look for an existing unpaid invoice for this semester to append to
        $invoice = Invoice::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('status', 'unpaid')
            ->latest()
            ->first();

        if (!$invoice) {
            $invoice = $this->createInvoice($student, $academicYearId, $semesterId);
        }

        // 4. Add Invoice Item
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'fee_item_id' => $config->fee_item_id,
            'description' => $description,
            'amount' => $config->amount,
            'sort_order' => 10, // Append at end
        ]);

        $invoice->recalculateTotals();
        
        Log::info("Created hostel fee item {$item->id} for Student {$student->id} on Invoice {$invoice->invoice_number}");

        // Finance Audit Log
        FinanceAuditService::log('create_invoice_item', 'App\Models\InvoiceItem', $item->id, $item->toArray(), "Hostel Fee added automatically");

        return $item;
    }

    protected function createInvoice(Student $student, $academicYearId, $semesterId)
    {
        // Get NTA Level
        $registration = SemesterRegistration::where('student_id', $student->id)
            ->where('semester_id', $semesterId)
            ->first();
        
        $ntaLevel = $registration ? $registration->nta_level : ($student->current_nta_level ?? 4);

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'student_id' => $student->id,
            'program_id' => $student->program_id,
            'nta_level' => $ntaLevel,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'unpaid',
            'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1, // Fallback for seeders
        ]);

        FinanceAuditService::log('create_invoice', 'App\Models\Invoice', $invoice->id, $invoice->toArray(), "Hostel Invoice generated automatically");

        return $invoice;
    }

    private function generateInvoiceNumber()
    {
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
