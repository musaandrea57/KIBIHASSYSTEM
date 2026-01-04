<?php

namespace App\Services;

use App\Models\FeeClearanceStatus;
use App\Models\FeeClearanceOverride;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeeClearanceService
{
    /**
     * Get outstanding balance for a specific period.
     */
    public function outstandingBalance(Student $student, AcademicYear $academicYear, Semester $semester): float
    {
        $calculation = $this->calculateForStudent($student, $academicYear, $semester);
        return $calculation['total_balance'];
    }

    /**
     * Calculate and return fee clearance status for a student in a specific period.
     * This method calculates fresh values but does NOT save them unless refreshSnapshot is called.
     * 
     * @return array
     */
    public function calculateForStudent(Student $student, AcademicYear $academicYear, Semester $semester)
    {
        // 1. Get all active invoices for this student, AY, Semester
        $invoices = Invoice::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->where('status', '!=', 'voided')
            ->with(['items.feeItem'])
            ->get();

        $totalInvoiced = 0;
        $totalPaid = 0;
        $totalBalance = 0;
        $mandatoryItemsPaid = true;

        foreach ($invoices as $invoice) {
            $totalInvoiced += $invoice->subtotal;
            $totalPaid += $invoice->total_paid;
            $totalBalance += $invoice->balance;

            // Check mandatory items
            foreach ($invoice->items as $item) {
                // Determine if mandatory
                // Logic: If FeeItem category is 'tuition' or 'mandatory', or if we fallback to all being mandatory
                // For now, let's assume 'tuition' and 'mandatory' are strictly mandatory. 
                // If category is 'other', we treat it as optional? 
                // User prompt: "AND all mandatory invoice items are fully paid"
                // Let's implement a check based on FeeItem category if available.
                
                $isMandatory = true; // Default to strict
                if ($item->feeItem) {
                    // If category is explicitly 'optional' (if that exists) or 'other' maybe?
                    // Let's assume everything is mandatory unless proven otherwise for safety.
                    // But wait, if I have an optional item and I haven't paid it, I shouldn't be blocked?
                    // Let's look at the migration comment: "// tuition, mandatory, other"
                    // Maybe 'other' is optional? 
                    // Let's stick to: All items are mandatory unless we find a specific "optional" flag.
                    // But to be safe and "production-grade", let's assume all are mandatory for now.
                }

                if ($isMandatory) {
                    // Check if item is fully paid
                    // InvoiceItem has 'amount' and 'paid_amount'
                    // We allow a small float epsilon difference?
                    if (($item->amount - $item->paid_amount) > 0.01) {
                        $mandatoryItemsPaid = false;
                    }
                }
            }
        }

        // Status logic
        // "Total balance across required invoices... is 0.00 AND all mandatory invoice items are fully paid"
        // If total balance is 0, then all items (mandatory or not) must be paid (assuming no overpayment on one covering another, but allocations handle that).
        // Actually, if balance is 0, everything is paid.
        // The edge case is if we have Optional items that are UNPAID, does that mean Balance > 0?
        // Yes, Invoice Balance includes all items.
        // So if I have an Unpaid Optional Item, my Invoice Balance is > 0.
        // If the rule is "Total balance ... is 0.00", then I must pay Optional items too?
        // That contradicts "AND all mandatory...". 
        // If "Total balance is 0" is the rule, then "all mandatory items are paid" is redundant (implied).
        // UNLESS: The "Total balance" refers to "Total Mandatory Balance".
        // The prompt says: "Total balance across required invoices ... is 0.00"
        // "Required invoices" might mean invoices that contain mandatory items?
        // Or maybe it means "Total Balance of Mandatory Items"?
        
        // Let's interpret "Total balance across required invoices" as "The Sum of Balances of Invoices that are considered Required".
        // Usually all invoices for a semester are required.
        // If I have an invoice with ONLY optional items, maybe it's not "required"?
        // But if I have an invoice with mixed items, the Invoice Balance is the sum.
        // If I pay only mandatory items, the Invoice Balance will still be positive (due to unpaid optional).
        // So "Invoice Balance == 0" is a very strict rule that forces paying optional items too if they are on the same invoice.
        
        // Let's assume the user wants strict enforcement: You must have 0 balance on the invoice.
        // The "AND all mandatory items" might be for cases where we allow partial payment but only if mandatory are covered? 
        // No, "Total balance ... is 0.00" is strict.
        
        // However, if we want to support "Optional" items not blocking clearance, we should calculate "Mandatory Balance".
        // Let's calculate "Mandatory Balance" and use THAT.
        // "Student is fee-cleared ... if: Total balance across required invoices ... is 0.00" -> This might be the user's way of saying "You owe nothing".
        // Let's stick to the STRICT interpretation: You must pay EVERYTHING on the invoice.
        // If they wanted to allow unpaid optional items, they would say "Total Mandatory Balance is 0".
        // But wait, "AND all mandatory invoice items are fully paid".
        // This second clause suggests the first clause might be looser?
        // Or maybe the first clause refers to the Account Balance?
        
        // Let's go with:
        // cleared = (Total Balance <= 0)
        // If they have unpaid optional items, they are NOT cleared.
        // This is the safest default.
        
        $isCleared = ($totalBalance <= 0.01); 

        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_balance' => $totalBalance,
            'is_cleared' => $isCleared,
            'mandatory_items_paid' => $mandatoryItemsPaid // Diagnostic
        ];
    }

    /**
     * Check if student is cleared (considering overrides).
     */
    public function isCleared(Student $student, AcademicYear $academicYear, Semester $semester): bool
    {
        // 1. Check Overrides
        $override = FeeClearanceOverride::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->exists();

        if ($override) {
            return true;
        }

        // 2. Check Snapshot
        $status = FeeClearanceStatus::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->first();

        if (!$status) {
            // No snapshot? Create one.
            $status = $this->refreshSnapshot($student, $academicYear, $semester);
        }

        return $status->status === 'cleared';
    }

    /**
     * Recalculate and save the snapshot.
     */
    public function refreshSnapshot(Student $student, AcademicYear $academicYear, Semester $semester)
    {
        $calculation = $this->calculateForStudent($student, $academicYear, $semester);

        $statusEnum = $calculation['is_cleared'] ? 'cleared' : 'not_cleared';

        // Check for active override to update status enum if needed?
        // The 'status' column in DB is 'cleared'|'not_cleared'|'overridden'.
        // If overridden, we might want to store that.
        // But 'isCleared' method checks the override table dynamically.
        // The snapshot 'status' field might be for quick reporting.
        // Let's check override here too to set the status string correctly.
        
        $hasOverride = FeeClearanceOverride::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->exists();
            
        if ($hasOverride) {
            $statusEnum = 'overridden';
        }

        $snapshot = FeeClearanceStatus::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
            ],
            [
                'status' => $statusEnum,
                'total_invoiced' => $calculation['total_invoiced'],
                'total_paid' => $calculation['total_paid'],
                'outstanding_balance' => $calculation['total_balance'],
                'last_calculated_at' => now(),
            ]
        );

        return $snapshot;
    }

    /**
     * Get breakdown of outstanding items.
     */
    public function getOutstandingBreakdown(Student $student, AcademicYear $academicYear, Semester $semester)
    {
        $invoices = Invoice::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->where('status', '!=', 'voided')
            ->with(['items.feeItem'])
            ->get();

        $outstanding = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $balance = $item->amount - $item->paid_amount;
                if ($balance > 0.01) {
                    $outstanding[] = [
                        'invoice_number' => $invoice->invoice_number,
                        'item_description' => $item->description, // or feeItem->name
                        'amount' => $item->amount,
                        'paid' => $item->paid_amount,
                        'balance' => $balance,
                        'due_date' => $invoice->due_date,
                    ];
                }
            }
        }

        return $outstanding;
    }
}
