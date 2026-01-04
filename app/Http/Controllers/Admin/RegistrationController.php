<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SemesterRegistration;
use App\Models\SemesterRegistrationDeadline;
use App\Models\ProgrammeLevelRule;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\AuditLog;
use App\Services\FeeStructureBillingService;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    // Registrations Management
    public function index(Request $request)
    {
        $query = SemesterRegistration::with(['student', 'academicYear', 'semester', 'program']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $programs = Program::all();

        return view('portal.admin.registration.index', compact('registrations', 'academicYears', 'semesters', 'programs'));
    }

    public function show($id)
    {
        $registration = SemesterRegistration::with(['items.moduleOffering.module', 'student', 'academicYear', 'semester'])->findOrFail($id);
        return view('portal.admin.registration.show', compact('registration'));
    }

    public function update(Request $request, $id, FeeStructureBillingService $billingService)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        $registration = SemesterRegistration::findOrFail($id);
        
        $registration->update([
            'status' => $request->status,
            'approved_at' => $request->status === 'approved' ? now() : null,
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($request->status === 'approved') {
            $billingService->generateInvoiceForRegistration($registration);
        }

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Registration ' . ucfirst($request->status),
            'details' => json_encode(['registration_id' => $id, 'status' => $request->status]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.registrations.show', $id)->with('success', 'Registration updated successfully.');
    }

    // Rules Management
    public function rulesIndex()
    {
        $rules = ProgrammeLevelRule::with('program')->get();
        $programs = Program::all();
        return view('portal.admin.registration.rules', compact('rules', 'programs'));
    }

    public function rulesStore(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nta_level' => 'required|integer|min:4|max:8',
            'min_credits' => 'required|integer|min:0',
            'max_credits' => 'required|integer|gte:min_credits',
        ]);

        ProgrammeLevelRule::updateOrCreate(
            ['program_id' => $request->program_id, 'nta_level' => $request->nta_level],
            ['min_credits' => $request->min_credits, 'max_credits' => $request->max_credits]
        );

        return back()->with('success', 'Rule saved successfully.');
    }

    // Deadlines Management
    public function deadlinesIndex()
    {
        $deadlines = SemesterRegistrationDeadline::with(['academicYear', 'semester', 'createdBy'])->orderBy('created_at', 'desc')->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        return view('portal.admin.registration.deadlines', compact('deadlines', 'academicYears', 'semesters'));
    }

    public function deadlinesStore(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        SemesterRegistrationDeadline::updateOrCreate(
            ['academic_year_id' => $request->academic_year_id, 'semester_id' => $request->semester_id],
            [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Deadline saved successfully.');
    }
}
