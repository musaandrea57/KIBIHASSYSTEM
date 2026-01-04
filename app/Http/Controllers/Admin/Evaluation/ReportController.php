<?php

namespace App\Http\Controllers\Admin\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\EvaluationPeriod;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function index(Request $request)
    {
        Gate::authorize('view_evaluation_reports');

        $periods = EvaluationPeriod::with(['academicYear', 'semester'])->orderBy('id', 'desc')->get();
        $reportData = null;
        $selectedPeriod = null;

        if ($request->has('period_id')) {
            $selectedPeriod = EvaluationPeriod::findOrFail($request->period_id);
            $reportData = $this->evaluationService->getReportData($selectedPeriod->id);
        }

        return view('admin.evaluation.reports.index', compact('periods', 'reportData', 'selectedPeriod'));
    }
}
