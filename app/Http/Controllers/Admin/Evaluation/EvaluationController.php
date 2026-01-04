<?php

namespace App\Http\Controllers\Admin\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationTemplate;
use App\Models\Semester;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EvaluationController extends Controller
{
    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    // --- TEMPLATES ---

    public function indexTemplates()
    {
        Gate::authorize('manage_evaluation_templates');
        $templates = EvaluationTemplate::withCount('questions')->get();
        return view('admin.evaluation.templates.index', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        Gate::authorize('manage_evaluation_templates');
        $request->validate(['name' => 'required']);
        
        EvaluationTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);
        return back()->with('success', 'Template created');
    }

    public function showTemplate(EvaluationTemplate $template)
    {
        Gate::authorize('manage_evaluation_templates');
        $template->load('questions');
        return view('admin.evaluation.templates.show', compact('template'));
    }

    public function storeQuestion(Request $request, EvaluationTemplate $template)
    {
        Gate::authorize('manage_evaluation_templates');
        $request->validate([
            'question_text' => 'required',
            'question_type' => 'required|in:likert_1_5,text'
        ]);

        EvaluationQuestion::create([
            'evaluation_template_id' => $template->id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'sort_order' => $request->sort_order ?? 0,
            'is_required' => $request->has('is_required'),
        ]);

        return back()->with('success', 'Question added');
    }

    // --- PERIODS ---

    public function indexPeriods()
    {
        Gate::authorize('open_close_evaluations');
        $periods = EvaluationPeriod::with(['academicYear', 'semester'])->latest()->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        
        return view('admin.evaluation.periods.index', compact('periods', 'academicYears', 'semesters'));
    }

    public function storePeriod(Request $request)
    {
        Gate::authorize('open_close_evaluations');
        $request->validate([
            'academic_year_id' => 'required',
            'semester_id' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        $period = EvaluationPeriod::create([
            'academic_year_id' => $request->academic_year_id,
            'semester_id' => $request->semester_id,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'status' => 'scheduled',
            'created_by' => Auth::id(),
        ]);

        // Generate evaluations for this period immediately or queue it
        $count = $this->evaluationService->generateEvaluationsForPeriod($period);

        return back()->with('success', "Period created and $count evaluations generated.");
    }

    public function updatePeriodStatus(Request $request, EvaluationPeriod $period)
    {
        Gate::authorize('open_close_evaluations');
        $request->validate(['status' => 'required|in:scheduled,open,closed']);

        $period->update(['status' => $request->status]);
        return back()->with('success', 'Period status updated');
    }
}
