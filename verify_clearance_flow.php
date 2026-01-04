<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Payment;
use App\Models\FeeClearanceStatus;
use App\Services\FeeClearanceService;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Fee Clearance Verification Flow...\n";

// --- RESET DATA FOR REPEATABILITY ---
echo "--- Resetting Data ---\n";
// Reset Student B (Cleared)
$userB = User::where('email', 'student.cleared@kibihas.ac.tz')->first();
$studentB = $userB->student;
$invoiceB = $studentB->invoices()->first();
$paymentB = Payment::where('student_id', $studentB->id)->first(); // Get any payment
if ($paymentB) {
    $paymentB->update(['status' => 'posted', 'reversed_at' => null]);
    $invoiceB->update(['total_paid' => $invoiceB->subtotal, 'balance' => 0, 'status' => 'paid']);
}
// Reset Student A (Not Cleared)
$userA = User::where('email', 'student.notcleared@kibihas.ac.tz')->first();
$studentA = $userA->student;
$invoiceA = $studentA->invoices()->first();
// Delete any payments for A
Payment::where('student_id', $studentA->id)->delete();
$invoiceA->update(['total_paid' => 0, 'balance' => $invoiceA->subtotal, 'status' => 'unpaid']);

// Force Refresh Statuses
$service = new FeeClearanceService();
$service->refreshSnapshot($studentB, $invoiceB->academicYear, $invoiceB->semester);
$service->refreshSnapshot($studentA, $invoiceA->academicYear, $invoiceA->semester);
echo "Data Reset Complete.\n\n";
// -------------------------------------

// 1. Get Cleared Student
$userB = User::where('email', 'student.cleared@kibihas.ac.tz')->first();
if (!$userB) {
    echo "Error: Cleared student not found. Run seeders first.\n";
    exit(1);
}
$studentB = $userB->student;
echo "Found Student B: {$studentB->first_name} {$studentB->last_name} (Should be CLEARED)\n";

// Check Status
$statusB = FeeClearanceStatus::where('student_id', $studentB->id)->latest()->first();
echo "Initial Status: " . ($statusB ? $statusB->status : 'NULL') . "\n";
echo "Initial Outstanding: " . ($statusB ? $statusB->outstanding_balance : 'NULL') . "\n";

if (!$statusB || $statusB->status !== 'cleared') {
    echo "WARNING: Initial status is not cleared. Attempting to refresh.\n";
    $service = new FeeClearanceService();
    // Assuming context from seeder (AY 1, Sem 1 - or fetch from invoice)
    // Let's get context from an invoice
    $invoice = $studentB->invoices()->first();
    if ($invoice) {
        $service->refreshSnapshot($studentB, $invoice->academicYear, $invoice->semester);
        $statusB = FeeClearanceStatus::where('student_id', $studentB->id)->latest()->first();
        echo "Refreshed Status: " . ($statusB ? $statusB->status : 'NULL') . "\n";
    }
}

// 2. Reverse Payment
echo "\n--- Reversing Payment ---\n";
$payment = Payment::where('student_id', $studentB->id)->whereNull('reversed_at')->first();
if ($payment) {
    echo "Found Payment: {$payment->payment_reference} ({$payment->amount})\n";
    
    // Simulate Reversal Logic (Controller usually does this: update payment, update invoice, trigger observers)
    // We need to simulate what the PaymentService/Controller would do.
    // Usually: 
    // 1. Update Payment status/reversed_at
    // 2. Update Invoice balance (PaymentObserver doesn't do this automatically, logic usually in Service)
    // Wait, the Observer only listens to Payment changes. Does it update the Invoice?
    // Let's check PaymentObserver again. It just calls refreshClearance.
    // It assumes the INVOICE balance is already correct.
    // If I just update Payment, the Invoice Balance won't update automatically unless there's logic for that.
    // Typically, reversing a payment means we have to manually update the invoice balance too.
    
    DB::transaction(function() use ($payment) {
        // 1. Mark Payment Reversed
        $payment->update([
            'status' => 'reversed',
            'reversed_at' => now(),
        ]);
        
        // 2. Update Invoice Balance (Critical step often missed in manual scripts)
        $invoice = $payment->invoice;
        $invoice->total_paid -= $payment->amount;
        $invoice->balance += $payment->amount;
        
        if ($invoice->balance > 0) {
            $invoice->status = 'partial'; // or unpaid
            if ($invoice->total_paid == 0) $invoice->status = 'unpaid';
        }
        
        $invoice->save(); // Triggers InvoiceObserver -> refreshClearance
        
        echo "Payment Reversed and Invoice Updated.\n";
    });

    $invCheck = \App\Models\Invoice::find($payment->invoice_id);
    echo "DEBUG: Invoice Balance in DB after Reversal: " . $invCheck->balance . "\n";

    // Check Status again
    // Wait a moment for observers (though they should be sync)
    // Note: ShouldHandleEventsAfterCommit might need explicit transaction commit trigger if not in HTTP cycle?
    // In a script, the transaction commits when the closure returns.
    
    // Manually trigger refresh to be sure logic works (Observer testing is secondary to logic testing here)
    // If Observer fails, we can fix Observer later. Let's verify Logic first.
    // $service = new FeeClearanceService();
    // $invoice = $payment->invoice; // Get invoice for context
    // $service->refreshSnapshot($studentB, $invoice->academicYear, $invoice->semester);

    $statusB = FeeClearanceStatus::where('student_id', $studentB->id)->latest()->first();
    echo "New Status: " . ($statusB ? $statusB->status : 'NULL') . "\n";
    echo "New Outstanding: " . ($statusB ? $statusB->outstanding_balance : 'NULL') . "\n";
    
    if ($statusB->status === 'not_cleared') {
        echo "SUCCESS: Status updated to not_cleared.\n";
    } else {
        echo "FAILURE: Status did not update.\n";
    }

} else {
    echo "No active payment found to reverse.\n";
}

// 3. Pay for Not Cleared Student
echo "\n--- Paying for Not Cleared Student ---\n";
$userA = User::where('email', 'student.notcleared@kibihas.ac.tz')->first();
$studentA = $userA->student;
echo "Found Student A: {$studentA->first_name} {$studentA->last_name}\n";

$invoiceA = $studentA->invoices()->first();
if ($invoiceA) {
    echo "Found Invoice: {$invoiceA->invoice_number} Balance: {$invoiceA->balance}\n";
    
    DB::transaction(function() use ($studentA, $invoiceA) {
        $amountToPay = $invoiceA->balance;
        
        // Create Payment
        $payment = Payment::create([
            'payment_reference' => 'PAY-TEST-' . time(),
            'student_id' => $studentA->id,
            'invoice_id' => $invoiceA->id,
            'amount' => $amountToPay,
            'payment_date' => now(),
            'method' => 'cash', // Fixed column name
            'status' => 'posted',
            'received_by' => 1,
        ]);
        
        // Update Invoice
        $invoiceA->total_paid += $amountToPay;
        $invoiceA->balance -= $amountToPay;
        $invoiceA->status = 'paid';
        $invoiceA->save(); // Triggers Observer
        
        echo "Payment Created and Invoice Updated.\n";
    });
    
    // Manually trigger refresh
    $service = new FeeClearanceService();
    $service->refreshSnapshot($studentA, $invoiceA->academicYear, $invoiceA->semester);

    // Check Status
    $statusA = FeeClearanceStatus::where('student_id', $studentA->id)->latest()->first();
    echo "New Status: " . ($statusA ? $statusA->status : 'NULL') . "\n";
    echo "New Outstanding: " . ($statusA ? $statusA->outstanding_balance : 'NULL') . "\n";
    
    if ($statusA && $statusA->status === 'cleared') {
        echo "SUCCESS: Status updated to cleared.\n";
    } else {
        echo "FAILURE: Status did not update.\n";
    }
    
} else {
    echo "No invoice found for Student A.\n";
}

echo "\nVerification Complete.\n";
