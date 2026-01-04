<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\FinanceAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'academicYear', 'semester'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            })->orWhere('invoice_number', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $invoices = $query->paginate(20);
        
        $academicYears = AcademicYear::all();
        
        return view('admin.finance.invoices.index', compact('invoices', 'academicYears'));
    }

    public function create()
    {
        return view('admin.finance.invoices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'program_id' => 'required|exists:programs,id',
            'nta_level' => 'required|integer',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.fee_item_id' => 'nullable|exists:fee_items,id',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            // Check uniqueness?
            // Allow multiple invoices for same context, but warn?
            // We'll allow it.

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'student_id' => $validated['student_id'],
                'program_id' => $validated['program_id'],
                'nta_level' => $validated['nta_level'],
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => 'unpaid',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_item_id' => $item['fee_item_id'] ?? null,
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'sort_order' => $index,
                ]);
            }

            $invoice->recalculateTotals();
            return $invoice;
        });

        FinanceAuditService::log('create_invoice', Invoice::class, $invoice->id, $validated);

        return redirect()->route('admin.finance.invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'payments', 'student', 'program', 'academicYear', 'semester']);
        return view('admin.finance.invoices.show', compact('invoice'));
    }
    
    public function void(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        if ($invoice->payments()->where('status', 'posted')->exists()) {
            return back()->withErrors(['error' => 'Cannot void invoice with posted payments. Please reverse payments first.']);
        }

        $invoice->update([
            'status' => 'voided',
            'voided_by' => Auth::id(),
            'void_reason' => $request->reason,
            'voided_at' => now(),
            'balance' => 0, // Voided invoice has no balance
        ]);

        FinanceAuditService::log('void_invoice', Invoice::class, $invoice->id, ['reason' => $request->reason]);

        return back()->with('success', 'Invoice voided successfully.');
    }

    // AJAX Methods
    public function searchStudents(Request $request)
    {
        $term = $request->term;
        
        $students = Student::with(['program', 'academicYear', 'semester'])
            ->where(function($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('admission_number', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'text' => "{$student->first_name} {$student->last_name} ({$student->admission_number})",
                    'program_id' => $student->program_id,
                    // Assume students table has these or we infer from registration
                    // For now, let's assume we might need to look up their current registration
                    // But if student model has them directly (as per seeding instructions), use them.
                    'program_name' => $student->program->name ?? 'N/A',
                    // Fallback or use what's on student record
                ];
            });

        return response()->json($students);
    }
    
    public function getStudentContext(Request $request, Student $student)
    {
        // Ideally we fetch their current active registration
        // But for now, we'll try to guess from student record or latest registration
        // Assuming student has current registration
        
        // Find latest registration
        $registration = $student->registrations()->latest()->first();
        
        if ($registration) {
            return response()->json([
                'program_id' => $student->program_id,
                'program_name' => $student->program->name,
                'nta_level' => $registration->nta_level ?? 4, // Default to 4 if missing
                'academic_year_id' => $registration->academic_year_id,
                'semester_id' => $registration->semester_id,
            ]);
        }
        
        // Fallback to student default info if no registration found (e.g. new student)
        // We need defaults.
        return response()->json([
            'program_id' => $student->program_id,
            'program_name' => $student->program->name ?? '',
            'nta_level' => 4,
            'academic_year_id' => AcademicYear::where('is_active', true)->value('id'),
            'semester_id' => Semester::where('is_active', true)->value('id'),
        ]);
    }

    public function getFeeStructure(Request $request)
    {
        $request->validate([
            'program_id' => 'required',
            'nta_level' => 'required',
            'academic_year_id' => 'required',
            'semester_id' => 'required',
        ]);

        $structure = FeeStructure::where('program_id', $request->program_id)
            ->where('nta_level', $request->nta_level)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('semester_id', $request->semester_id)
            ->where('status', 'active')
            ->with('items.feeItem')
            ->latest() // Get latest active version
            ->first();

        if (!$structure) {
            return response()->json(['error' => 'No active fee structure found for this context.'], 404);
        }

        return response()->json($structure);
    }

    private function generateInvoiceNumber()
    {
        // Format: INV-YYYYMMDD-XXXX
        $prefix = 'INV-' . date('Ymd') . '-';
        
        // Find last invoice with this prefix
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
