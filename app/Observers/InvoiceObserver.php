<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\FeeClearanceService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class InvoiceObserver implements ShouldHandleEventsAfterCommit
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $this->refreshClearance($invoice);
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // Only refresh if relevant fields changed
        if ($invoice->isDirty(['subtotal', 'total_paid', 'balance', 'status', 'voided_at'])) {
            $this->refreshClearance($invoice);
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        $this->refreshClearance($invoice);
    }

    protected function refreshClearance(Invoice $invoice)
    {
        if ($invoice->student_id && $invoice->academic_year_id && $invoice->semester_id) {
             $this->feeService->refreshSnapshot(
                 $invoice->student,
                 $invoice->academicYear,
                 $invoice->semester
             );
        }
    }
}
