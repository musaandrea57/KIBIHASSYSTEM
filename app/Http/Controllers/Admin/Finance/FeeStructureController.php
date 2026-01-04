<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BankAccount;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\FinanceAuditLog;
use App\Models\Invoice;
use App\Models\Program;
use App\Models\Semester;
use App\Services\FinanceAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['program', 'academicYear', 'semester', 'items', 'createdBy'])
            ->latest();

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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $structures = $query->paginate(15);
        
        $programs = Program::all();
        $academicYears = AcademicYear::all(); // Should probably filter active/upcoming
        $semesters = Semester::all();

        return view('admin.finance.fee_structures.index', compact('structures', 'programs', 'academicYears', 'semesters'));
    }

    public function create()
    {
        $programs = Program::all();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::all();
        $feeItems = FeeItem::where('is_active', true)->orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('admin.finance.fee_structures.create', compact('programs', 'academicYears', 'semesters', 'feeItems', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateFeeStructure($request);

        // Check if a DRAFT already exists for this context
        $draftExists = FeeStructure::where('program_id', $validated['program_id'])
            ->where('nta_level', $validated['nta_level'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('status', 'draft')
            ->exists();

        if ($draftExists) {
            return back()->withErrors(['error' => 'A draft fee structure already exists for this context. Please edit it instead of creating a new one.']);
        }

        // Get latest version number for this context
        $latestVersion = FeeStructure::where('program_id', $validated['program_id'])
            ->where('nta_level', $validated['nta_level'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('semester_id', $validated['semester_id'])
            ->max('version') ?? 0;

        $structure = DB::transaction(function () use ($validated, $latestVersion) {
            $structure = FeeStructure::create([
                'program_id' => $validated['program_id'],
                'nta_level' => $validated['nta_level'],
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
                'name' => $validated['name'],
                'status' => 'draft',
                'version' => $latestVersion + 1,
                'created_by' => Auth::id(),
            ]);

            $this->saveItems($structure, $validated['items']);
            
            return $structure;
        });

        FinanceAuditService::log('create_fee_structure', FeeStructure::class, $structure->id, $validated);

        if ($request->has('save_and_publish')) {
            return $this->publish($structure);
        }

        return redirect()->route('admin.finance.fee-structures.index')->with('success', 'Fee Structure draft created successfully.');
    }

    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load(['items.feeItem', 'items.bankAccount', 'program', 'academicYear', 'semester', 'createdBy', 'updatedBy']);
        return view('admin.finance.fee_structures.show', compact('feeStructure'));
    }

    public function edit(FeeStructure $feeStructure)
    {
        // If structure is ACTIVE or ARCHIVED, we should probably warn them that saving will create a NEW DRAFT version.
        // We allow editing, but the update method will handle the branching.
        
        $feeStructure->load(['items.feeItem', 'items.bankAccount']);

        $programs = Program::all();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::all();
        $feeItems = FeeItem::where('is_active', true)->orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $auditLogs = FinanceAuditLog::where('model_type', FeeStructure::class)
            ->where('model_id', $feeStructure->id)
            ->with('user')
            ->latest()
            ->get();

        return view('admin.finance.fee_structures.edit', compact('feeStructure', 'programs', 'academicYears', 'semesters', 'feeItems', 'bankAccounts', 'auditLogs'));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $validated = $this->validateFeeStructure($request);

        // If structure is NOT draft, we must create a new version
        if ($feeStructure->status !== 'draft') {
             // Create new version
             $newStructure = DB::transaction(function () use ($validated, $feeStructure) {
                // Get next version
                $nextVersion = FeeStructure::where('program_id', $validated['program_id'])
                    ->where('nta_level', $validated['nta_level'])
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->where('semester_id', $validated['semester_id'])
                    ->max('version') + 1;

                $structure = FeeStructure::create([
                    'program_id' => $validated['program_id'],
                    'nta_level' => $validated['nta_level'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'semester_id' => $validated['semester_id'],
                    'name' => $validated['name'], // Name might change
                    'status' => 'draft',
                    'version' => $nextVersion,
                    'created_by' => Auth::id(),
                ]);

                $this->saveItems($structure, $validated['items']);
                return $structure;
            });

            FinanceAuditService::log('version_fee_structure', FeeStructure::class, $newStructure->id, ['original_id' => $feeStructure->id]);

            if ($request->has('save_and_publish')) {
                return $this->publish($newStructure);
            }

            return redirect()->route('admin.finance.fee-structures.index')->with('success', 'New draft version created from existing structure.');

        } else {
            // Update existing draft
            DB::transaction(function () use ($feeStructure, $validated) {
                $feeStructure->update([
                    'name' => $validated['name'],
                    'updated_by' => Auth::id(),
                    // Context fields usually shouldn't change for an existing draft unless we allow re-purposing
                    // But for now, let's assume they might fix mistakes in draft
                    'program_id' => $validated['program_id'],
                    'nta_level' => $validated['nta_level'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'semester_id' => $validated['semester_id'],
                ]);

                $feeStructure->items()->delete();
                $this->saveItems($feeStructure, $validated['items']);
            });

            FinanceAuditService::log('update_fee_structure', FeeStructure::class, $feeStructure->id, $validated);

            if ($request->has('save_and_publish')) {
                return $this->publish($feeStructure);
            }

            return redirect()->route('admin.finance.fee-structures.index')->with('success', 'Fee Structure draft updated.');
        }
    }

    public function publish(FeeStructure $feeStructure)
    {
        // 1. Archive any currently ACTIVE structure for this context
        $activeStructure = FeeStructure::where('program_id', $feeStructure->program_id)
            ->where('nta_level', $feeStructure->nta_level)
            ->where('academic_year_id', $feeStructure->academic_year_id)
            ->where('semester_id', $feeStructure->semester_id)
            ->where('status', 'active')
            ->where('id', '!=', $feeStructure->id)
            ->first();

        DB::transaction(function() use ($feeStructure, $activeStructure) {
            if ($activeStructure) {
                $activeStructure->update(['status' => 'archived']);
            }

            $feeStructure->update([
                'status' => 'active',
                'published_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]);
        });

        FinanceAuditService::log('publish_fee_structure', FeeStructure::class, $feeStructure->id);

        return redirect()->route('admin.finance.fee-structures.index')->with('success', 'Fee Structure published successfully.');
    }

    public function archive(FeeStructure $feeStructure)
    {
        $feeStructure->update(['status' => 'archived']);
        FinanceAuditService::log('archive_fee_structure', FeeStructure::class, $feeStructure->id);

        return back()->with('success', 'Fee Structure archived.');
    }
    
    public function duplicate(FeeStructure $feeStructure)
    {
        // Duplicate as new draft
        $newStructure = DB::transaction(function() use ($feeStructure) {
            // Get next version
            $nextVersion = FeeStructure::where('program_id', $feeStructure->program_id)
                ->where('nta_level', $feeStructure->nta_level)
                ->where('academic_year_id', $feeStructure->academic_year_id)
                ->where('semester_id', $feeStructure->semester_id)
                ->max('version') + 1;

            $newStructure = $feeStructure->replicate(['created_at', 'updated_at', 'status', 'published_at', 'version']);
            $newStructure->name = $feeStructure->name . ' (Copy)';
            $newStructure->status = 'draft';
            $newStructure->version = $nextVersion;
            $newStructure->created_by = Auth::id();
            $newStructure->save();
            
            foreach ($feeStructure->items as $item) {
                $newItem = $item->replicate(['created_at', 'updated_at', 'fee_structure_id']);
                $newItem->fee_structure_id = $newStructure->id;
                $newItem->save();
            }
            return $newStructure;
        });
        
        FinanceAuditService::log('duplicate_fee_structure', FeeStructure::class, $newStructure->id, ['source_id' => $feeStructure->id]);
        
        return redirect()->route('admin.finance.fee-structures.edit', $newStructure)->with('success', 'Fee Structure duplicated as draft.');
    }

    private function validateFeeStructure(Request $request)
    {
        return $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nta_level' => 'required|integer|in:4,5,6',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.fee_item_id' => 'required|exists:fee_items,id',
            'items.*.amount_oct' => 'required|numeric|min:0',
            'items.*.amount_jan' => 'required|numeric|min:0',
            'items.*.amount_apr' => 'required|numeric|min:0',
            'items.*.is_mandatory' => 'boolean',
            'items.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);
    }

    private function saveItems(FeeStructure $structure, array $items)
    {
        foreach ($items as $index => $item) {
            $amountOct = $item['amount_oct'] ?? 0;
            $amountJan = $item['amount_jan'] ?? 0;
            $amountApr = $item['amount_apr'] ?? 0;
            $total = $amountOct + $amountJan + $amountApr;

            FeeStructureItem::create([
                'fee_structure_id' => $structure->id,
                'fee_item_id' => $item['fee_item_id'],
                'amount' => $total, // Legacy total
                'amount_oct' => $amountOct,
                'amount_jan' => $amountJan,
                'amount_apr' => $amountApr,
                'is_mandatory' => $item['is_mandatory'] ?? true,
                'bank_account_id' => $item['bank_account_id'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }
}
