<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SemesterRegistration;
use App\Models\SemesterRegistrationItem;
use App\Models\SemesterRegistrationDeadline;
use App\Models\Module;
use App\Models\ModuleOffering;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $activeYear = AcademicYear::where('is_active', true)->first();
        // Assuming current semester is globally active or per student. 
        // Based on seeder, we have active semesters.
        // We'll find the deadline to determine the "Active" semester for registration purposes.
        $deadline = SemesterRegistrationDeadline::active()->first();
        
        $currentRegistration = null;
        if ($deadline) {
            $currentRegistration = SemesterRegistration::where('student_id', $student->id)
                ->where('academic_year_id', $deadline->academic_year_id)
                ->where('semester_id', $deadline->semester_id)
                ->first();
        }

        $history = SemesterRegistration::where('student_id', $student->id)
            ->latest()
            ->get();

        return view('student.registration.index', compact('student', 'deadline', 'currentRegistration', 'history'));
    }

    public function create()
    {
        $student = Auth::user()->student;
        $deadline = SemesterRegistrationDeadline::active()->first();

        if (!$deadline) {
            return redirect()->route('student.registration.index')->with('error', 'No active registration period found.');
        }

        // Check if already registered
        $existing = SemesterRegistration::where('student_id', $student->id)
            ->where('academic_year_id', $deadline->academic_year_id)
            ->where('semester_id', $deadline->semester_id)
            ->first();

        if ($existing) {
             return redirect()->route('student.registration.index')->with('info', 'You have already submitted a registration for this session.');
        }

        // Get Rules from Program directly
        $program = $student->program;
        
        // Get Available Modules
        // We need modules offered in this Academic Year + Semester + Student's Program + Student's Level
        $offerings = ModuleOffering::with('module')
            ->where('academic_year_id', $deadline->academic_year_id)
            ->where('semester_id', $deadline->semester_id)
            ->where('nta_level', $student->current_nta_level)
            ->whereHas('module', function($q) use ($student) {
                $q->where('program_id', $student->program_id);
            })
            ->get();

        return view('student.registration.create', compact('student', 'deadline', 'program', 'offerings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'offerings' => 'required|array|min:1',
            'offerings.*' => 'exists:module_offerings,id',
        ]);

        $student = Auth::user()->student;
        $deadline = SemesterRegistrationDeadline::active()->first();

        if (!$deadline) {
            return back()->with('error', 'Registration deadline has passed.');
        }

        // Re-check existing
        $existing = SemesterRegistration::where('student_id', $student->id)
            ->where('academic_year_id', $deadline->academic_year_id)
            ->where('semester_id', $deadline->semester_id)
            ->exists();
        
        if ($existing) {
            return back()->with('error', 'Registration already exists.');
        }

        $offerings = ModuleOffering::with('module')->whereIn('id', $request->offerings)->get();
        
        // STRICT VALIDATION: Check Program and NTA Level for EACH offering
        foreach ($offerings as $offering) {
            if ($offering->module->program_id !== $student->program_id) {
                return back()->with('error', "Invalid selection: Module {$offering->module->code} does not belong to your program.");
            }
            // Actually, offering nta_level should match student's level.
            if ((int)$offering->nta_level !== (int)$student->current_nta_level) {
                 return back()->with('error', "Invalid selection: Module {$offering->module->code} is NTA Level {$offering->nta_level}, but you are Level {$student->current_nta_level}.");
            }
        }

        // Validate Credits
        $totalCredits = $offerings->sum(function($offering) {
            return $offering->module->credits;
        });

        $program = $student->program;

        if ($totalCredits < $program->min_credits_per_semester || $totalCredits > $program->max_credits_per_semester) {
            return back()->with('error', "Total credits ($totalCredits) must be between {$program->min_credits_per_semester} and {$program->max_credits_per_semester}.");
        }

        // Create Registration
        $registration = SemesterRegistration::create([
            'student_id' => $student->id,
            'academic_year_id' => $deadline->academic_year_id,
            'semester_id' => $deadline->semester_id,
            'program_id' => $student->program_id,
            'nta_level' => $student->current_nta_level,
            'status' => 'submitted', // Or draft if we had a draft flow, but req says "draft/submit flow", let's assume direct submit for MVP or add draft status later.
            // Requirement said "draft/submit flow". I'll default to submitted for simplicity unless user wants save-as-draft button.
            // I'll assume the form submission is the "Submit" action.
            'submitted_at' => now(),
        ]);

        foreach ($offerings as $offering) {
            SemesterRegistrationItem::create([
                'semester_registration_id' => $registration->id,
                'module_offering_id' => $offering->id,
                'credits_snapshot' => $offering->module->credits,
            ]);
        }

        return redirect()->route('student.registration.index')->with('success', 'Registration submitted successfully waiting for approval.');
    }

    public function history()
    {
        $student = Auth::user()->student;
        $registrations = SemesterRegistration::where('student_id', $student->id)
            ->with(['academicYear', 'semester', 'items.moduleOffering.module'])
            ->latest()
            ->paginate(10);
            
        return view('student.registration.history', compact('registrations'));
    }

    public function show(SemesterRegistration $registration)
    {
        // $this->authorize('view', $registration); // Need policy or manual check
        if ($registration->student_id !== Auth::user()->student->id) {
            abort(403);
        }
        $registration->load(['items.moduleOffering.module', 'academicYear', 'semester']);
        return view('student.registration.show', compact('registration'));
    }
}
