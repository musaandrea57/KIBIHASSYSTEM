<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Receipt;
use App\Models\Student;
use App\Services\FinanceAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'invoice'])
            ->latest();
            
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            })->orWhere('payment_reference', 'like', "%{$search}%");
        }

        $payments = $query->paginate(20);
        return view('admin.finance.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        return view('admin.finance.payments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,bank,mobile_money,card,cheque',
            'transaction_ref' => 'nullable|string',
        ]);

        $invoice = Invoice::with('items')->findOrFail($validated['invoice_id']);

        if ($invoice->status === 'voided') {
            return back()->withErrors(['error' => 'Cannot pay against a voided invoice.']);
        }
        
        if ($invoice->student_id != $validated['student_id']) {
            return back()->withErrors(['error' => 'Invoice does not belong to the selected student.']);
        }

        $payment = DB::transaction(function () use ($validated, $invoice) {
            // 1. Create Payment
            $payment = Payment::create([
                'payment_reference' => $this->generatePaymentReference(),
                'student_id' => $invoice->student_id,
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'transaction_ref' => $validated['transaction_ref'],
                'amount' => $validated['amount'],
                'received_by' => Auth::id(),
                'status' => 'posted',
            ]);

            // 2. Allocate Amount to Items (By sort_order)
            $remainingPayment = $validated['amount'];
            
            // Sort items by sort_order
            $sortedItems = $invoice->items->sortBy('sort_order');

            foreach ($sortedItems as $item) {
                if ($remainingPayment <= 0) break;

                $balance = $item->amount - $item->paid_amount;
                if ($balance <= 0) continue;

                $toAllocate = min($balance, $remainingPayment);
                
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'invoice_item_id' => $item->id,
                    'amount' => $toAllocate,
                ]);

                $item->paid_amount += $toAllocate;
                $item->save();

                $remainingPayment -= $toAllocate;
            }

            // 3. Recalculate Invoice
            $invoice->recalculateTotals();

            // 4. Generate Receipt Record
            Receipt::create([
                'receipt_number' => $this->generateReceiptNumber(),
                'payment_id' => $payment->id,
                'student_id' => $invoice->student_id,
                'invoice_id' => $invoice->id,
                'issued_at' => now(),
                'issued_by' => Auth::id(),
            ]);
            
            return $payment;
        });

        FinanceAuditService::log('record_payment', Payment::class, $payment->id, $validated);

        return redirect()->route('admin.finance.payments.show', $payment)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['allocations.invoiceItem', 'student', 'invoice', 'receipt']);
        return view('admin.finance.payments.show', compact('payment'));
    }
    
    public function reverse(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);
        
        if ($payment->status === 'reversed') {
            return back()->withErrors(['error' => 'Payment is already reversed.']);
        }
        
        DB::transaction(function () use ($payment, $request) {
            // 1. Reverse Allocations
            foreach ($payment->allocations as $allocation) {
                $item = $allocation->invoiceItem;
                $item->paid_amount -= $allocation->amount;
                $item->save();
            }
            
            // 2. Mark Payment Reversed
            $payment->update([
                'status' => 'reversed',
                'reversed_by' => Auth::id(),
                'reversed_reason' => $request->reason,
                'reversed_at' => now(),
            ]);
            
            // 3. Recalculate Invoice
            $payment->invoice->recalculateTotals();
        });
        
        FinanceAuditService::log('reverse_payment', Payment::class, $payment->id, ['reason' => $request->reason]);
        
        return back()->with('success', 'Payment reversed successfully.');
    }
    
    public function downloadReceipt(Payment $payment)
    {
        $payment->load(['student', 'invoice', 'receipt', 'allocations.invoiceItem.feeItem']);

        if (!$payment->receipt) {
             // Generate if missing (legacy or error)
             $payment->receipt()->create([
                'receipt_number' => $this->generateReceiptNumber(),
                'student_id' => $payment->student_id,
                'invoice_id' => $payment->invoice_id,
                'issued_at' => now(),
                'issued_by' => Auth::id(),
             ]);
             $payment->refresh();
        }

        $pdf = Pdf::loadView('admin.finance.payments.receipt_pdf', compact('payment'));
        return $pdf->download('receipt-' . $payment->payment_reference . '.pdf');
    }

    // AJAX Methods
    public function getStudentInvoices(Request $request, Student $student)
    {
        $invoices = Invoice::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'voided')
            ->with(['academicYear', 'semester'])
            ->orderBy('due_date')
            ->get()
            ->map(function($inv) {
                return [
                    'id' => $inv->id,
                    'text' => "{$inv->invoice_number} - Bal: " . number_format((float) $inv->balance, 2) . " ({$inv->semester->name} {$inv->academicYear->name})",
                    'balance' => $inv->balance
                ];
            });
            
        return response()->json($invoices);
    }
    
    private function generatePaymentReference()
    {
        // KIB-PAY-YYYYMMDD-XXXX
        $prefix = 'KIB-PAY-' . date('Ymd') . '-';
        $last = Payment::where('payment_reference', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? intval(substr($last->payment_reference, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function generateReceiptNumber()
    {
        // KIB-RCPT-YYYYMMDD-XXXX
        $prefix = 'KIB-RCPT-' . date('Ymd') . '-';
        $last = Receipt::where('receipt_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? intval(substr($last->receipt_number, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
