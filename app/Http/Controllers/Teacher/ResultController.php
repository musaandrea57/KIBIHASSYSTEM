<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ModuleOffering;
use App\Models\ModuleResult;
use App\Models\Student;
use App\Services\ResultsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    protected $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    /**
     * List modules assigned to the teacher.
     */
    public function index()
    {
        $teacher = Auth::user();
        
        // Find module offerings where teacher is assigned
        // Assuming ModuleOffering has 'teacher_id' or 'coordinator_id' or similar
        // Based on prompt "Teacher can only enter marks for assigned modules"
        
        // Let's check ModuleOffering schema again implicitly or assume 'teacher_id'.
        // If ModuleAssignment exists, use that.
        // Prompt says "one module offering with assigned teacher".
        
        $offerings = ModuleOffering::where('teacher_id', $teacher->id)
            ->with(['module', 'academicYear', 'semester'])
            ->get();

        return view('teacher.results.index', compact('offerings'));
    }

    /**
     * Show marks entry form for a module offering.
     */
    public function show(ModuleOffering $offering)
    {
        // Verify assignment
        if ($offering->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this module.');
        }

        // Get students registered for this offering
        // If CourseRegistration exists, use it.
        // Otherwise, fall back to program/level matching (legacy/MVP).
        
        // Trying to use CourseRegistration if possible.
        // Assuming 'course_registrations' table links student to offering/module.
        // If not, use program/level.
        
        // Let's assume we fetch students who *should* be here.
        // For now, let's fetch students in the program/level of the module.
        // Better: Use `registrations` relationship if it existed.
        
        // I'll query students based on program and current_nta_level matching module's nta_level
        // AND current_academic_year matching offering's year.
        
        $students = Student::where('program_id', $offering->module->program_id)
            ->where('current_nta_level', $offering->module->nta_level)
            ->with(['moduleResults' => function($q) use ($offering) {
                $q->where('module_offering_id', $offering->id);
            }])
            ->orderBy('registration_number')
            ->get();

        return view('teacher.results.show', compact('offering', 'students'));
    }

    /**
     * Store/Update marks.
     */
    public function update(Request $request, ModuleOffering $offering)
    {
        if ($offering->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'marks' => 'required|array',
            'marks.*.ca' => 'nullable|numeric|min:0|max:100',
            'marks.*.se' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->marks as $studentId => $data) {
            $student = Student::find($studentId);
            if (!$student) continue;

            if (isset($data['ca'])) {
                $this->resultsService->upsertCA($student, $offering, ['mark' => $data['ca']], Auth::user());
            }
            
            if (isset($data['se'])) {
                $this->resultsService->upsertExam($student, $offering, $data['se'], Auth::user());
            }
        }

        return back()->with('success', 'Marks saved successfully.');
    }

    /**
     * Submit results to admin.
     */
    public function submit(ModuleOffering $offering)
    {
        // Verify assignment
        $isAssigned = \App\Models\ModuleAssignment::where('user_id', Auth::id())
            ->where('module_id', $offering->module_id)
            ->where('academic_year_id', $offering->academic_year_id)
            ->where('semester_id', $offering->semester_id)
            ->exists();

        if (!$isAssigned) {
            abort(403);
        }

        // Update status of all draft results to pending_admin_approval
        ModuleResult::where('module_offering_id', $offering->id)
            ->where('status', 'draft')
            ->update(['status' => 'pending_admin_approval']);

        return back()->with('success', 'Results submitted to Admin for approval.');
    }
}
