<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Services\FeeClearanceService;
use App\Models\AcademicYear;

class FinanceController extends Controller
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student record not found.');
        }

        // Summary stats
        $totalInvoiced = $student->invoices()->sum('subtotal'); // or total if tax included
        // Actually Invoice has subtotal. Let's assume subtotal is final for now or calculate total.
        // Wait, invoice items sum up to subtotal.
        
        // Let's use recalculated totals to be safe, or just sum columns.
        // Ideally we should have a `total` column on invoice but we have `subtotal`.
        
        $totalPaid = $student->payments()->where('status', 'posted')->sum('amount');
        $outstandingBalance = $student->invoices()->where('status', '!=', 'voided')->sum('balance');
        
        $recentInvoices = $student->invoices()
            ->with(['academicYear', 'semester'])
            ->latest()
            ->take(5)
            ->get();
            
        $recentPayments = $student->payments()
            ->with(['invoice'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.finance.index', compact('totalInvoiced', 'totalPaid', 'outstandingBalance', 'recentInvoices', 'recentPayments'));
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
