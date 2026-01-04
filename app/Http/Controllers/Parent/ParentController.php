<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Result;

class ParentController extends Controller
{
    public function dashboard()
    {
        $children = Auth::user()->children()->with(['program', 'currentAcademicYear', 'currentSemester', 'user'])->get();
        return view('parent.dashboard', compact('children'));
    }

    public function childDetails(Student $student)
    {
        // Ensure the authenticated user is a guardian of this student
        // Using exists() is more efficient than loading all children
        $isGuardian = Auth::user()->children()->where('students.id', $student->id)->exists();

        if (!$isGuardian) {
            abort(403, 'Unauthorized access to student record.');
        }
        
        $student->load(['program', 'registrations.module', 'user', 'currentAcademicYear', 'currentSemester']);
        
        $results = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->with(['module', 'academicYear', 'semester'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->academicYear->year . ' - ' . $item->semester->name;
            });

        return view('parent.child-details', compact('student', 'results'));
    }
}
