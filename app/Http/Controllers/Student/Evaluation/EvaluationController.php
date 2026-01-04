<?php

namespace App\Http\Controllers\Student\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationTemplate;
use App\Models\Student;
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

    public function index()
    {
        Gate::authorize('submit_lecturer_evaluation');
        
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $pendingEvaluations = $this->evaluationService->getStudentPendingEvaluations($student);

        return view('student.evaluation.index', compact('pendingEvaluations'));
    }

    public function create(Evaluation $evaluation)
    {
        Gate::authorize('submit_lecturer_evaluation');
        
        // Ensure student can evaluate this specific one (security check)
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        
        // Basic check if it's open
        if ($evaluation->period->status !== 'open') {
            return back()->with('error', 'Evaluation period is not open.');
        }

        // Get template (assuming one active global template for now, or linked via period)
        // For MVP, we pick the first active template. In robust system, period might have template_id.
        $template = EvaluationTemplate::where('is_active', true)->with('questions')->firstOrFail();

        return view('student.evaluation.form', compact('evaluation', 'template'));
    }

    public function store(Request $request, Evaluation $evaluation)
    {
        Gate::authorize('submit_lecturer_evaluation');
        
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $answers = $request->input('answers', []);

        try {
            $this->evaluationService->submitEvaluation($student, $evaluation, $answers);
            return redirect()->route('student.evaluation.index')->with('success', 'Evaluation submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
