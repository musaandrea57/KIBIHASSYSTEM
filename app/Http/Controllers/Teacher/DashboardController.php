<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();
        
        // Load active assignments with offering details
        $assignments = $teacher->activeTeacherAssignments()
            ->with(['moduleOffering.module', 'moduleOffering.academicYear', 'moduleOffering.semester'])
            ->latest()
            ->get();
            
        return view('teacher.dashboard', compact('assignments'));
    }

    public function show($assignmentId)
    {
        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();
        // Ensure assignment belongs to teacher
        $assignment = $teacher->activeTeacherAssignments()
            ->where('id', $assignmentId)
            ->with(['moduleOffering.module', 'moduleOffering.academicYear', 'moduleOffering.semester'])
            ->firstOrFail();
            
        $offering = $assignment->moduleOffering;
        
        // Fetch Students with APPROVED registration for this offering
        $students = \App\Models\Student::whereHas('semesterRegistrations', function($q) use ($offering) {
            $q->where('status', 'approved')
              ->whereHas('items', function($sq) use ($offering) {
                  $sq->where('module_offering_id', $offering->id);
              });
        })->with('user')->orderBy('registration_number')->get();
        
        return view('teacher.roster', compact('assignment', 'students'));
    }
}
