<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Services\FeeClearanceService;
use App\Services\FinanceSummaryService;
use App\Models\AcademicYear;
use App\Models\FeeStructure;

class FinanceController extends Controller
{
    protected $feeService;
    protected $financeSummaryService;

    public function __construct(FeeClearanceService $feeService, FinanceSummaryService $financeSummaryService)
    {
        $this->feeService = $feeService;
        $this->financeSummaryService = $financeSummaryService;
    }

    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student record not found.');
        }

        $summary = $this->financeSummaryService->getSummary($student);
        
        $invoices = $student->invoices()
            ->with(['academicYear', 'semester'])
            ->latest()
            ->get();

        $payments = $student->payments()
            ->with(['invoice'])
            ->latest()
            ->get();

        return view('student.finance.index', array_merge($summary, compact('invoices', 'payments')));
    }

    public function invoices()
    {
        $student = Auth::user()->student;
        
        $invoices = $student->invoices()
            ->with(['academicYear', 'semester', 'items'])
            ->latest()
            ->paginate(10);
            
        return view('student.finance.invoices', compact('invoices'));
    }

    public function payments()
    {
        $student = Auth::user()->student;
        
        $payments = $student->payments()
            ->with(['invoice'])
            ->latest()
            ->paginate(10);
            
        return view('student.finance.payments', compact('payments'));
    }

    public function receipt(Payment $payment)
    {
        // Ensure payment belongs to student
        if ($payment->student_id !== Auth::user()->student->id) {
            abort(403);
        }

        $payment->load(['student.program', 'allocations.invoiceItem.feeItem', 'receipt', 'receivedBy']);

        $pdf = Pdf::loadView('admin.finance.payments.receipt_pdf', compact('payment'));
        return $pdf->download("receipt-{$payment->payment_reference}.pdf");
    }

    public function printInstallment(Request $request)
    {
        $installmentKey = $request->query('installment');
        if (!in_array($installmentKey, ['oct', 'jan', 'apr'])) {
            abort(404);
        }

        $student = Auth::user()->student;
        
        // 1. Get Fee Structure
        $activeYear = $student->currentAcademicYear;
        $activeSemester = $student->currentSemester;

        $feeStructure = FeeStructure::where('program_id', $student->program_id)
            ->where('academic_year_id', $activeYear->id)
            ->where('semester_id', $activeSemester->id)
            ->where('nta_level', $student->current_nta_level)
            ->active()
            ->with('items.feeItem')
            ->first();

        if (!$feeStructure) {
            // Fallback for annual structure
            $feeStructure = FeeStructure::where('program_id', $student->program_id)
                ->where('academic_year_id', $activeYear->id)
                ->whereNull('semester_id')
                ->where('nta_level', $student->current_nta_level)
                ->active()
                ->with('items.feeItem')
                ->first();
        }

        if (!$feeStructure) {
            return redirect()->back()->with('error', 'Fee structure not found.');
        }

        // 2. Build Virtual Invoice Items
        $items = collect();
        $totalAmount = 0;

        foreach ($feeStructure->items as $item) {
            $amount = 0;
            if ($installmentKey === 'oct') $amount = $item->amount_oct;
            elseif ($installmentKey === 'jan') $amount = $item->amount_jan;
            elseif ($installmentKey === 'apr') $amount = $item->amount_apr;

            if ($amount > 0) {
                // Create a virtual item object
                $virtualItem = new \stdClass();
                $virtualItem->amount = $amount;
                $virtualItem->description = null;
                $virtualItem->feeItem = $item->feeItem;
                $items->push($virtualItem);
                $totalAmount += $amount;
            }
        }

        // 3. Create Virtual Invoice Object
        $invoice = new \stdClass();
        $invoice->invoice_number = strtoupper($installmentKey) . '-' . $student->registration_number;
        $invoice->created_at = now();
        $invoice->due_date = \Carbon\Carbon::now()->addDays(30); 
        
        // Get status from summary service
        $summary = $this->financeSummaryService->getSummary($student);
        $instStatus = $summary['installments'][$installmentKey]['status'] ?? 'pending';
        $instPaid = $summary['installments'][$installmentKey]['paid'] ?? 0;
        
        $invoice->status = ucfirst($instStatus);
        $invoice->items = $items;
        $invoice->subtotal = $totalAmount;
        $invoice->total_paid = $instPaid;
        $invoice->balance = max(0, $totalAmount - $instPaid);

        $pdf = Pdf::loadView('student.finance.invoice_pdf', compact('invoice', 'student'));
        return $pdf->download("bill-{$installmentKey}.pdf");
    }

    public function statement()
    {
        $student = Auth::user()->student;
        $summary = $this->financeSummaryService->getSummary($student);
        
        $invoices = $student->invoices()->where('status', '!=', 'voided')->get()->map(function($inv) {
            return [
                'date' => $inv->created_at,
                'description' => "Invoice #" . $inv->invoice_number,
                'reference' => $inv->invoice_number,
                'amount' => $inv->subtotal,
                'type' => 'invoice'
            ];
        });
        
        $payments = $student->payments()->where('status', 'posted')->get()->map(function($pmt) {
            return [
                'date' => $pmt->payment_date,
                'description' => "Payment via " . $pmt->payment_method,
                'reference' => $pmt->transaction_reference,
                'amount' => $pmt->amount,
                'type' => 'payment'
            ];
        });
        
        $transactions = $invoices->merge($payments)->sortBy('date');
        
        $pdf = Pdf::loadView('student.finance.statement_pdf', [
            'student' => $student,
            'totals' => $summary['totals'],
            'transactions' => $transactions
        ]);
        
        return $pdf->download('financial_statement.pdf');
    }

    public function paymentInfo()
    {
        $student = Auth::user()->student;
        $invoices = $student->invoices()
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'voided')
            ->latest()
            ->get();

        return view('student.finance.payment_info', compact('invoices'));
    }

    public function showInvoice(Invoice $invoice)
    {
        if ($invoice->student_id !== Auth::user()->student->id) {
            abort(403);
        }
        
        $student = Auth::user()->student;
        $invoice->load(['items.feeItem', 'academicYear', 'semester']);
        
        $pdf = Pdf::loadView('student.finance.invoice_pdf', compact('invoice', 'student'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function clearanceRequired()
    {
        $student = Auth::user()->student;
        
        // Determine context (best effort to match Middleware logic)
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = $student->currentAcademicYear;
        }

        $semester = null;
        if ($academicYear) {
             // Try to find latest registration for this AY
             $latestReg = $student->semesterRegistrations()
                ->where('academic_year_id', $academicYear->id)
                ->latest()
                ->first();
             if ($latestReg) {
                 $semester = $latestReg->semester;
             }
        }
        
        if (!$semester && $student->current_semester_id) {
            $semester = $student->currentSemester;
        }

        $breakdown = [];
        $status = null;
        $totals = [
            'total_invoiced' => 0,
            'total_paid' => 0,
            'total_balance' => 0
        ];

        if ($academicYear && $semester) {
            $totals = $this->feeService->calculateForStudent($student, $academicYear, $semester);
            $breakdown = $this->feeService->getOutstandingBreakdown($student, $academicYear, $semester);
            
            // Check if overridden
            if ($this->feeService->isCleared($student, $academicYear, $semester) && $totals['total_balance'] > 0) {
                 // Cleared despite balance (override)
                 // We might want to show a message "You have an override active".
            }
        } else {
            // Fallback: Show global outstanding
            // This happens if we can't pin down a specific semester.
            // We can sum up all unpaid invoices.
             $totals['total_balance'] = $student->invoices()->where('status', '!=', 'voided')->sum('balance');
             // fetch all unpaid items... simplified for fallback
        }

        return view('student.finance.clearance_required', compact('student', 'totals', 'breakdown', 'academicYear', 'semester'));
    }
}
