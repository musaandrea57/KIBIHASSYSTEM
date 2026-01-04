<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SemesterRegistration;
use App\Models\SemesterRegistrationItem;
use App\Models\SemesterRegistrationDeadline;
use App\Models\ModuleOffering;
use App\Models\ProgrammeLevelRule;
use Illuminate\Support\Facades\Auth;

class StudentRegistrationController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        // 1. Check for active deadline
        $deadline = SemesterRegistrationDeadline::active()->first();
        
        $currentRegistration = null;
        if ($deadline) {
            $currentRegistration = SemesterRegistration::where('student_id', $student->id)
                ->where('academic_year_id', $deadline->academic_year_id)
                ->where('semester_id', $deadline->semester_id)
                ->first();
        }

        // 2. Get History
        $history = SemesterRegistration::where('student_id', $student->id)
            ->with(['academicYear', 'semester'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.registration.index', compact('deadline', 'currentRegistration', 'history'));
    }

    public function create()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        // Check deadline
        $deadline = SemesterRegistrationDeadline::active()->first();
        if (!$deadline) {
            return redirect()->route('student.registration.index')->with('error', 'No active registration period.');
        }

        // Check existing
        $existing = SemesterRegistration::where('student_id', $student->id)
            ->where('academic_year_id', $deadline->academic_year_id)
            ->where('semester_id', $deadline->semester_id)
            ->first();

        if ($existing) {
             if ($existing->status === 'draft' || $existing->status === 'rejected') {
                 // We can allow editing. For now, let's redirect to show which might have an "Edit" button if implemented,
                 // or we can just render the create view with pre-filled data.
                 // Let's keep it simple: if submitted/approved, cannot create. If draft/rejected, can edit (which is effectively create/update).
             } else {
                 return redirect()->route('student.registration.show', $existing->id);
             }
        }

        // Get Available Modules
        $targetLevel = $student->current_nta_level;

        $offerings = ModuleOffering::where('academic_year_id', $deadline->academic_year_id)
            ->where('semester_id', $deadline->semester_id)
            ->where('nta_level', $targetLevel)
            ->whereHas('module', function($q) use ($student) {
                $q->where('program_id', $student->program_id);
            })
            ->with('module')
            ->get();
            
        // Get Rules
        $rules = ProgrammeLevelRule::where('program_id', $student->program_id)
            ->where('nta_level', $targetLevel)
            ->first();

        // If editing existing draft, get selected IDs
        $selectedIds = [];
        if ($existing) {
            $selectedIds = $existing->items->pluck('module_offering_id')->toArray();
        }

        return view('student.registration.create', compact('deadline', 'offerings', 'rules', 'student', 'existing', 'selectedIds'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        $deadline = SemesterRegistrationDeadline::active()->first();
        
        if (!$deadline) {
            return redirect()->back()->with('error', 'Registration period closed.');
        }

        $validated = $request->validate([
            'offerings' => 'required|array',
            'offerings.*' => 'exists:module_offerings,id',
            'action' => 'required|in:save_draft,submit',
        ]);

        // Validate Credits
        $selectedOfferings = ModuleOffering::with('module')->whereIn('id', $validated['offerings'])->get();
        $totalCredits = $selectedOfferings->sum('module.credits');
        
        $targetLevel = $student->current_nta_level;
        $rules = ProgrammeLevelRule::where('program_id', $student->program_id)
            ->where('nta_level', $targetLevel)
            ->first();

        if ($rules && $validated['action'] === 'submit') {
            if ($totalCredits < $rules->min_credits) {
                return redirect()->back()->with('error', "Minimum credits required: {$rules->min_credits}. Selected: {$totalCredits}")->withInput();
            }
            if ($totalCredits > $rules->max_credits) {
                return redirect()->back()->with('error', "Maximum credits allowed: {$rules->max_credits}. Selected: {$totalCredits}")->withInput();
            }
        }

        // Create/Update Registration
        $registration = SemesterRegistration::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $deadline->academic_year_id,
                'semester_id' => $deadline->semester_id,
            ],
            [
                'program_id' => $student->program_id,
                'nta_level' => $targetLevel,
                'status' => $validated['action'] === 'submit' ? 'submitted' : 'draft',
                'submitted_at' => $validated['action'] === 'submit' ? now() : null,
            ]
        );

        // Sync Items
        $registration->items()->delete();
        
        foreach ($selectedOfferings as $offering) {
            SemesterRegistrationItem::create([
                'semester_registration_id' => $registration->id,
                'module_offering_id' => $offering->id,
                'credits_snapshot' => $offering->module->credits,
            ]);
        }

        $message = $validated['action'] === 'submit' 
            ? 'Registration submitted successfully waiting for approval.' 
            : 'Draft saved successfully.';

        return redirect()->route('student.registration.index')->with('success', $message);
    }

    public function show(SemesterRegistration $registration)
    {
        // Authorize
        if ($registration->student_id !== Auth::user()->student->id) {
            abort(403);
        }

        $registration->load(['items.moduleOffering.module', 'academicYear', 'semester']);
        
        return view('student.registration.show', compact('registration'));
    }
    
    public function history()
    {
         $student = Auth::user()->student;
         $history = SemesterRegistration::where('student_id', $student->id)
            ->with(['academicYear', 'semester', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();
            
         return view('student.registration.history', compact('history'));
    }
}
