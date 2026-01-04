<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Dashboard
        $totalInvoiced = Invoice::where('status', '!=', 'voided')->sum('subtotal');
        $totalCollected = Payment::where('status', 'posted')->sum('amount');
        $outstanding = Invoice::where('status', '!=', 'voided')->sum('balance');

        $counts = [
            'unpaid' => Invoice::where('status', 'unpaid')->count(),
            'partial' => Invoice::where('status', 'partial')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'voided' => Invoice::where('status', 'voided')->count(),
        ];

        $recentPayments = Payment::with('student')->where('status', 'posted')->latest()->take(5)->get();
        $recentInvoices = Invoice::with('student')->where('status', '!=', 'voided')->latest()->take(5)->get();

        return view('admin.finance.reports.index', compact('totalInvoiced', 'totalCollected', 'outstanding', 'counts', 'recentPayments', 'recentInvoices'));
    }

    public function collections(Request $request)
    {
        $query = Payment::with(['student', 'invoice.program', 'invoice.academicYear'])
            ->where('status', 'posted');

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        if ($request->filled('program_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        $payments = $query->latest()->paginate(50);
        $total = $query->sum('amount');
        
        $programs = Program::all();

        return view('admin.finance.reports.collections', compact('payments', 'total', 'programs'));
    }

    public function outstanding(Request $request)
    {
        $query = Invoice::with(['student', 'program', 'academicYear', 'semester'])
            ->where('status', '!=', 'voided')
            ->where('balance', '>', 0);

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('nta_level')) {
            $query->where('nta_level', $request->nta_level);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $invoices = $query->orderBy('balance', 'desc')->paginate(50);
        $totalOutstanding = $query->sum('balance');
        
        $programs = Program::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('admin.finance.reports.outstanding', compact('invoices', 'totalOutstanding', 'programs', 'academicYears', 'semesters'));
    }
}
