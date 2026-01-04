<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\ModuleAssignment;
use App\Models\ModuleOffering;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ModuleAssignment::with(['moduleOffering.module', 'moduleOffering.academicYear', 'moduleOffering.semester', 'teacher', 'assignedBy'])
            ->where('status', 'active');

        if ($request->has('academic_year_id') && $request->academic_year_id) {
            $query->whereHas('moduleOffering', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->has('teacher_id') && $request->teacher_id) {
            $query->where('teacher_user_id', $request->teacher_id);
        }

        if ($request->has('program_id') && $request->program_id) {
            $query->whereHas('moduleOffering.module', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        $assignments = $query->latest()->paginate(15);
        $academicYears = AcademicYear::all();
        $programs = Program::all();

        return view('admin.assignments.index', compact('assignments', 'academicYears', 'programs'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::all();
        $programs = Program::where('is_active', true)->get();
        $teachers = User::role('teacher')->whereHas('staffProfile', function ($q) {
            $q->where('status', 'active');
        })->get();
        
        // Load offerings via AJAX ideally, but for now we'll pass active current offerings or just let the user filter
        // Actually, let's just pass empty and let the view handle logic or pass all
        // To make it simpler without AJAX for now, we can pass offerings grouped? No, too many.
        // We'll rely on a simple selection flow or just listing them all if not too many.
        // Let's pass all active offerings for the current year.
        $currentYear = AcademicYear::where('is_current', true)->first();
        $offerings = $currentYear ? ModuleOffering::with('module')->where('academic_year_id', $currentYear->id)->get() : collect();

        return view('admin.assignments.create', compact('academicYears', 'semesters', 'programs', 'teachers', 'offerings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'module_offering_id' => 'required|exists:module_offerings,id',
            'teacher_user_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_user_id);
        if (!$teacher->hasRole('teacher')) {
            return back()->withErrors(['teacher_user_id' => 'Selected user is not a teacher.']);
        }
        
        $offering = ModuleOffering::findOrFail($request->module_offering_id);

        DB::transaction(function () use ($request, $offering) {
            // Remove existing assignment for this module/session to enforce uniqueness if needed
            // The unique constraint will fail if we don't delete first.
            // Or we can use updateOrCreate
            
            ModuleAssignment::updateOrCreate(
                [
                    'module_offering_id' => $offering->id,
                ],
                [
                    'teacher_user_id' => $request->teacher_user_id,
                    'assigned_by_user_id' => Auth::id(),
                    'status' => 'active',
                ]
            );
        });

        return redirect()->route('admin.assignments.index')->with('success', 'Teacher assigned successfully.');
    }

    public function destroy(ModuleAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Assignment removed.');
    }
}
