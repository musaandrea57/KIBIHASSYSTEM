<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Services\AcademicReportsService;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use App\Models\Department;
use App\Http\Requests\Principal\ReportFilterRequest;
use App\Exports\AcademicReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AcademicReportsController extends Controller
{
    protected $reportsService;

    public function __construct(AcademicReportsService $reportsService)
    {
        $this->reportsService = $reportsService;
    }

    /**
     * Reports Dashboard (Overview)
     */
    public function index(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        
        $data = [
            'kpis' => $this->reportsService->getKpis($filters),
            'alerts' => $this->reportsService->getAlerts($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
        ];

        return view('principal.reports.index', $data);
    }

    /**
     * Performance Reports
     */
    public function performance(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $reportType = $request->get('type', 'programme_summary');

        $data = [
            'reportData' => $this->reportsService->getPerformanceReports($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
            'reportType' => $reportType,
        ];

        return view('principal.reports.performance', $data);
    }

    /**
     * Registration Reports
     */
    public function registration(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        
        $data = [
            'reportData' => $this->reportsService->getRegistrationReports($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
        ];

        return view('principal.reports.registration', $data);
    }

    /**
     * Progression Reports
     */
    public function progression(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        
        $data = [
            'reportData' => $this->reportsService->getProgressionReports($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
        ];

        return view('principal.reports.progression', $data);
    }

    /**
     * Workflow / Assessments
     */
    public function workflow(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        
        $data = [
            'reportData' => $this->reportsService->getWorkflowReports($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
        ];

        return view('principal.reports.workflow', $data);
    }

    /**
     * Compliance
     */
    public function compliance(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        
        $data = [
            'reportData' => $this->reportsService->getComplianceReports($filters),
            'filterOptions' => $this->getFilterOptions(),
            'filters' => $filters,
        ];

        return view('principal.reports.compliance', $data);
    }

    /**
     * Export Handler
     */
    public function export(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $format = $request->get('format', 'pdf');
        $report = $request->get('report', 'overview');
        
        // Enrich filters with names for display
        if (!empty($filters['academic_year_id'])) {
            $filters['academic_year_name'] = AcademicYear::find($filters['academic_year_id'])->name ?? 'Unknown';
        }
        if (!empty($filters['semester_id'])) {
            $filters['semester_name'] = Semester::find($filters['semester_id'])->name ?? 'Unknown';
        }

        // Generate data based on report type
        $data = [];
        $view = 'principal.reports.exports.generic';

        switch($report) {
            case 'performance':
                $data = $this->reportsService->getPerformanceReports($filters);
                $view = 'principal.reports.exports.performance';
                break;
            case 'registration':
                $data = $this->reportsService->getRegistrationReports($filters);
                $view = 'principal.reports.exports.registration';
                break;
            case 'progression':
                $data = $this->reportsService->getProgressionReports($filters);
                $view = 'principal.reports.exports.progression';
                break;
             case 'workflow':
                $data = $this->reportsService->getWorkflowReports($filters);
                $view = 'principal.reports.exports.workflow';
                break;
             case 'compliance':
                $data = $this->reportsService->getComplianceReports($filters);
                $view = 'principal.reports.exports.compliance';
                break;
             default:
                // Fallback for 'overview' or undefined reports
                $data = $this->reportsService->getComplianceReports($filters); // Or a generic overview data method
                $view = 'principal.reports.exports.compliance'; // Reuse compliance as a generic template for now, or create 'overview'
                break;
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView($view, ['data' => $data, 'filters' => $filters]);
            return $pdf->download("report-{$report}.pdf");
        } else {
            // Excel Export
            return Excel::download(new class($view, ['data' => $data, 'filters' => $filters]) implements \Maatwebsite\Excel\Concerns\FromView {
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
            }, "report-{$report}.xlsx");
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
