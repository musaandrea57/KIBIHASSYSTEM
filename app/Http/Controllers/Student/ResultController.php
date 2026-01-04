<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ModuleResult;
use App\Models\Semester;
use App\Services\ResultsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    protected $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        // Default to current year/semester or request
        $academicYearId = $request->academic_year_id ?? $student->current_academic_year_id;
        $semesterId = $request->semester_id ?? $student->current_semester_id;
        
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        // Fetch Published Results ONLY
        $results = ModuleResult::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('status', 'published')
            ->with(['moduleOffering.module'])
            ->get();
            
        // Compute Summary
        $summary = $this->resultsService->computeSemesterSummary($student, $academicYearId, $semesterId);

        return view('student.results.index', compact('student', 'results', 'summary', 'academicYears', 'semesters', 'academicYearId', 'semesterId'));
    }

    public function downloadPdf(Request $request)
    {
        $student = Auth::user()->student;
        $academicYearId = $request->academic_year_id ?? $student->current_academic_year_id;
        $semesterId = $request->semester_id ?? $student->current_semester_id;
        
        $results = ModuleResult::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('status', 'published')
            ->with(['moduleOffering.module'])
            ->get();
            
        $summary = $this->resultsService->computeSemesterSummary($student, $academicYearId, $semesterId);
        $academicYear = AcademicYear::find($academicYearId);
        $semester = Semester::find($semesterId);
        
        $data = compact('student', 'results', 'summary', 'academicYear', 'semester');
        
        $pdf = Pdf::loadView('pdfs.student_results', $data);
        return $pdf->download('Results_' . $student->registration_number . '.pdf');
    }

    public function downloadTranscript()
    {
        $student = Auth::user()->student;
        
        // Fetch ALL published results grouped by Year/Semester
        $results = ModuleResult::where('student_id', $student->id)
            ->where('status', 'published')
            ->with(['moduleOffering.module', 'academicYear', 'semester'])
            ->orderBy('academic_year_id')
            ->orderBy('semester_id')
            ->get()
            ->groupBy(['academic_year_id', 'semester_id']);
            
        // Compute summaries for each group?
        // For simplicity, let's pass the raw grouped results and compute in view or helper.
        
        $data = compact('student', 'results');
        
        $pdf = Pdf::loadView('pdfs.transcript', $data);
        return $pdf->download('Transcript_' . $student->registration_number . '.pdf');
    }
}
