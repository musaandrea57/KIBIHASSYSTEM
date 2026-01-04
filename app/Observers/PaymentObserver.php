<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\FeeClearanceService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class PaymentObserver implements ShouldHandleEventsAfterCommit
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function created(Payment $payment): void
    {
        $this->refreshClearance($payment);
    }

    public function updated(Payment $payment): void
    {
        if ($payment->isDirty(['amount', 'reversed_at'])) {
            $this->refreshClearance($payment);
        }
    }

    protected function refreshClearance(Payment $payment)
    {
        // If payment is directly linked to an invoice
        if ($payment->invoice) {
            $invoice = $payment->invoice;
            $this->feeService->refreshSnapshot(
                $invoice->student,
                $invoice->academicYear,
                $invoice->semester
            );
        }

        // Also check allocations if any (for multi-invoice payments if supported)
        // Note: allocations relationship might not be loaded, but we can query it if needed.
        // For now, rely on direct invoice link or assume InvoiceObserver handles the heavy lifting 
        // when allocations update the invoice balance.
        // This Observer is primarily to catch top-level payment changes that might trigger broad updates.
    }
}
