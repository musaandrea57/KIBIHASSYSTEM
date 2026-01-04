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
use Illuminate\Support\Facades\Auth;

class AdminRegistrationController extends Controller
{
    // --- Overview ---
    public function index(Request $request)
    {
        $query = SemesterRegistration::with(['student', 'program', 'academicYear', 'semester'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $registrations = $query->paginate(15);
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.registrations.index', compact('registrations', 'years'));
    }

    public function show(SemesterRegistration $registration)
    {
        $registration->load(['items.moduleOffering.module', 'student', 'program']);
        return view('admin.registrations.show', compact('registration'));
    }

    public function approve(SemesterRegistration $registration)
    {
        $registration->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Registration approved.');
    }

    public function reject(Request $request, SemesterRegistration $registration)
    {
        $request->validate(['reason' => 'required|string']);

        $registration->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => Auth::id(), // Track who rejected
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Registration rejected.');
    }

    // --- Rules ---
    public function rules()
    {
        $rules = ProgrammeLevelRule::with('program')->get();
        $programs = Program::all();
        
        return view('admin.registrations.rules', compact('rules', 'programs'));
    }

    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nta_level' => 'required|integer|min:4|max:8',
            'min_credits' => 'required|integer|min:1',
            'max_credits' => 'required|integer|gte:min_credits',
        ]);

        ProgrammeLevelRule::updateOrCreate(
            [
                'program_id' => $validated['program_id'],
                'nta_level' => $validated['nta_level'],
            ],
            [
                'min_credits' => $validated['min_credits'],
                'max_credits' => $validated['max_credits'],
            ]
        );

        return redirect()->back()->with('success', 'Rule saved successfully.');
    }

    // --- Deadlines ---
    public function deadlines()
    {
        $deadlines = SemesterRegistrationDeadline::with(['academicYear', 'semester', 'creator'])->orderBy('end_date', 'desc')->get();
        $years = AcademicYear::where('is_active', true)->orWhere('end_date', '>', now())->get();
        $semesters = Semester::all(); // Assuming generic semesters like "Semester 1", "Semester 2"

        return view('admin.registrations.deadlines', compact('deadlines', 'years', 'semesters'));
    }

    public function storeDeadline(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        SemesterRegistrationDeadline::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
            ],
            [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'created_by' => Auth::id(),
            ]
        );

        return redirect()->back()->with('success', 'Deadline set successfully.');
    }
    
    public function deleteDeadline(SemesterRegistrationDeadline $deadline)
    {
        $deadline->delete();
        return redirect()->back()->with('success', 'Deadline removed.');
    }
}
