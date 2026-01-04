<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\ModuleResult;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class ResultsCenterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student; 
        
        if (!$student) {
             abort(403, 'Student profile not found.');
        }

        // Get Current Context
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        $isCleared = true;
        
        if ($currentYear) {
             $clearance = DB::table('fee_clearance_statuses')
                ->where('student_id', $student->id)
                ->where('academic_year_id', $currentYear->id)
                ->orderByDesc('id')
                ->first();
                
             if ($clearance && $clearance->status === 'not_cleared') {
                 $isCleared = false;
             }
        }
        
        if (!$isCleared) {
            return view('student.results.clearance_blocked', compact('student'));
        }

        // Fetch Semester I Results (Internal) - Current Year Only
        $resultsSem1 = collect();
        if ($currentYear) {
            $resultsSem1 = ModuleResult::where('student_id', $student->id)
                ->where('academic_year_id', $currentYear->id)
                ->whereHas('semester', function($q) {
                    $q->where('name', 'like', '%Semester I%')
                      ->orWhere('name', 'like', '%Semester 1%');
                })
                ->with(['moduleOffering.module', 'semester', 'academicYear'])
                ->get();
        }
            
        // Fetch Semester II Official Uploads
        $officialResults = DB::table('official_result_uploads')
            ->where('program_id', $student->program_id)
            ->where('nta_level', $student->nta_level) // Assuming student has nta_level column
            ->orderByDesc('created_at')
            ->get();

        // Fetch History (Prior Years)
        $historyResults = ModuleResult::where('student_id', $student->id)
            ->when($currentYear, function($q) use ($currentYear) {
                $q->where('academic_year_id', '!=', $currentYear->id);
            })
            ->with(['moduleOffering.module', 'semester', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester_id', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->academicYear->name . ' - ' . $item->semester->name;
            });

        // Fetch Transcripts
        $transcripts = DB::table('transcripts')
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->get();
            
        return view('student.results.index', compact('student', 'resultsSem1', 'officialResults', 'historyResults', 'transcripts', 'currentYear'));
    }
}
