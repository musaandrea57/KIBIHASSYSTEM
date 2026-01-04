<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\DTOs\StudentPerformanceFilterDTO;
use App\Services\StudentPerformanceService;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use App\Models\Department;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StudentPerformanceController extends Controller
{
    protected StudentPerformanceService $performanceService;

    public function __construct(StudentPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    public function index(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();
        
        $kpis = $this->performanceService->getKpis($filters);
        $distributions = $this->performanceService->getDistributions($filters);
        $trends = $this->performanceService->getTrends($filters);
        
        return view('principal.student-performance.index', [
            'kpis' => $kpis,
            'distributions' => $distributions,
            'trends' => $trends,
            'filters' => $filters,
            'options' => $this->getFilterOptions(),
        ]);
    }

    public function programme(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();

        $data = $this->performanceService->getProgrammeSummary($filters);
        $kpis = $this->performanceService->getKpis($filters);

        return view('principal.student-performance.programme', [
            'data' => $data,
            'kpis' => $kpis,
            'filters' => $filters,
            'options' => $this->getFilterOptions(),
        ]);
    }

    public function cohort(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();

        $data = $this->performanceService->getCohortSummary($filters);
        $kpis = $this->performanceService->getKpis($filters);

        return view('principal.student-performance.cohort', [
            'data' => $data,
            'kpis' => $kpis,
            'filters' => $filters,
            'options' => $this->getFilterOptions(),
        ]);
    }

    public function atRisk(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();

        $students = $this->performanceService->getAtRiskStudents($filters);
        $kpis = $this->performanceService->getKpis($filters);

        return view('principal.student-performance.at-risk', [
            'students' => $students,
            'kpis' => $kpis,
            'filters' => $filters,
            'options' => $this->getFilterOptions(),
        ]);
    }

    public function list(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();

        $students = $this->performanceService->getStudentList($filters);
        
        return view('principal.student-performance.list', [
            'students' => $students,
            'filters' => $filters,
            'options' => $this->getFilterOptions(),
        ]);
    }

    public function show($studentId)
    {
        $student = $this->performanceService->getStudentProfile($studentId);
        $results = $this->performanceService->getStudentResults($studentId);

        return view('principal.student-performance.student.show', [
            'student' => $student,
            'results' => $results,
        ]);
    }

    public function export(Request $request)
    {
        $filterDTO = StudentPerformanceFilterDTO::fromRequest($request);
        $filters = $filterDTO->toArray();

        $reportType = $request->get('report', 'overview');
        $format = $request->get('format', 'pdf');
        
        $data = [];
        $view = 'principal.student-performance.exports.generic';
        $title = 'Student Performance Report';

        switch($reportType) {
            case 'programme':
                $data = $this->performanceService->getProgrammeSummary($filters);
                $view = 'principal.student-performance.exports.programme';
                $title = 'Programme Performance Report';
                break;
            case 'cohort':
                $data = $this->performanceService->getCohortSummary($filters);
                $view = 'principal.student-performance.exports.cohort';
                $title = 'Cohort Performance Report';
                break;
            case 'at-risk':
                $data = $this->performanceService->getAtRiskStudents($filters);
                $view = 'principal.student-performance.exports.at-risk';
                $title = 'At-Risk Students Report';
                break;
            case 'student':
                 $studentId = $request->get('student_id');
                 if ($studentId) {
                     $data['student'] = $this->performanceService->getStudentProfile($studentId);
                     $data['results'] = $this->performanceService->getStudentResults($studentId);
                     $view = 'principal.student-performance.exports.student-profile';
                     $title = 'Student Performance Profile: ' . $data['student']->registration_number;
                 }
                 break;
            default: // overview
                $data['kpis'] = $this->performanceService->getKpis($filters);
                $data['distributions'] = $this->performanceService->getDistributions($filters);
                $view = 'principal.student-performance.exports.overview';
                $title = 'Student Performance Overview';
                break;
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView($view, ['data' => $data, 'filters' => $filters, 'title' => $title]);
            return $pdf->download("performance-{$reportType}.pdf");
        } else {
            return Excel::download(new class($view, ['data' => $data, 'filters' => $filters, 'title' => $title]) implements \Maatwebsite\Excel\Concerns\FromView {
                private $view;
                private $data;

                public function __construct($view, $data)
                {
                    $this->view = $view;
                    $this->data = $data;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view($this->view, $this->data);
                }
            }, "performance-{$reportType}.xlsx");
        }
    }

    private function getFilterOptions()
    {
        return [
            'academic_years' => AcademicYear::orderBy('name', 'desc')->get(),
            'semesters' => Semester::all(),
            'programs' => Program::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'intakes' => Student::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year'),
        ];
    }
}
