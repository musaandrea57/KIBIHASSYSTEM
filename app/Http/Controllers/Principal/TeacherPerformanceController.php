<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ModuleOffering;
use App\Models\EvaluationAnswer;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use App\Models\Department;
use App\Services\TeacherPerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class TeacherPerformanceController extends Controller
{
    protected $performanceService;

    public function __construct(TeacherPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Overview Dashboard
     */
    public function index(Request $request)
    {
        $filters = $request->only(['academic_year_id', 'department_id', 'semester_id', 'program_id', 'search', 'start_date', 'end_date']);
        $data = $this->performanceService->getOverview($filters);
        
        return view('principal.teacher-performance.index', array_merge($data, [
            'filters' => $filters,
            'options' => $this->getFilterOptions()
        ]));
    }

    /**
     * Teacher Scorecard
     */
    public function show(Request $request, User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
             // Optional: abort(404);
        }

        $filters = $request->only(['academic_year_id', 'semester_id', 'start_date', 'end_date']);
        $data = $this->performanceService->getTeacherScorecard($teacher->id, $filters);
        
        return view('principal.teacher-performance.show', array_merge($data, [
            'filters' => $filters,
            'options' => $this->getFilterOptions()
        ]));
    }

    /**
     * Module Performance View
     */
    public function module(Request $request, ModuleOffering $moduleOffering)
    {
        $moduleOffering->load(['module', 'academicYear', 'semester', 'teacher.staffProfile', 'moduleResults', 'evaluations']);
        
        // Calculate module specific stats
        $stats = [
            'avg_mark' => $moduleOffering->moduleResults->avg('total_mark') ?? 0,
            'pass_rate' => $moduleOffering->moduleResults->count() > 0 
                ? ($moduleOffering->moduleResults->where('total_mark', '>=', 40)->count() / $moduleOffering->moduleResults->count() * 100) 
                : 0,
            'evaluation_score' => EvaluationAnswer::whereHas('evaluation', function($q) use ($moduleOffering) {
                $q->where('module_offering_id', $moduleOffering->id);
            })->whereNotNull('rating')->avg('rating') ?? 0,
        ];

        return view('principal.teacher-performance.module', compact('moduleOffering', 'stats'));
    }

    /**
     * Exceptions & Alerts Queue
     */
    public function alerts(Request $request)
    {
        $filters = $request->only(['academic_year_id', 'semester_id', 'department_id', 'search']);
        $alerts = $this->performanceService->getSystemAlerts($filters);
        
        // Ensure options are available for the filter bar
        $options = $this->getFilterOptions();

        return view('principal.teacher-performance.alerts', compact('alerts', 'filters', 'options'));
    }

    /**
     * Reports/Exports Center
     */
    public function reports(Request $request)
    {
        return view('principal.teacher-performance.reports');
    }

    /**
     * Handle Exports
     */
    public function export(Request $request)
    {
        $filters = $request->all();
        $type = $request->get('type', 'excel'); // pdf or excel
        $report = $request->get('report', 'overview'); // overview or teacher
        
        $data = [];
        $view = '';
        $filename = "teacher-performance-{$report}-" . date('Y-m-d');

        if ($report === 'teacher' && $request->has('teacher')) {
            $data = $this->performanceService->getTeacherScorecard($request->get('teacher'), $filters);
            $view = 'principal.teacher-performance.exports.teacher';
            $filename = "teacher-scorecard-" . ($data['teacher']->name ?? 'unknown') . "-" . date('Y-m-d');
        } else {
            $data = $this->performanceService->getOverview($filters);
            $view = 'principal.teacher-performance.exports.overview';
        }

        // Add metadata
        $data['filters'] = $filters;
        $data['generated_at'] = now();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView($view, $data);
            return $pdf->download("{$filename}.pdf");
        } else {
            return Excel::download(new class($view, $data) implements \Maatwebsite\Excel\Concerns\FromView {
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
            }, "{$filename}.xlsx");
        }
    }

    private function getFilterOptions()
    {
        return [
            'academic_years' => AcademicYear::orderBy('name', 'desc')->get(),
            'semesters' => Semester::all(),
            'programs' => Program::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ];
    }
}
