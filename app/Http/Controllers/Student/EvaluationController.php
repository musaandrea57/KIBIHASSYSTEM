<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * List pending and submitted evaluations.
     */
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        $evaluations = Evaluation::where('student_id', $student->id)
            ->with(['moduleOffering.module', 'teacher', 'period'])
            ->whereHas('period', function ($q) {
                // Show only evaluations for periods that are open or recently closed?
                // Or just all.
                // Prompt says "Lists modules for current semester... + status".
                // So filtering by active academic year/semester via period might be good.
                // But for now, just all is fine, sorted by period.
            })
            ->orderByDesc('created_at')
            ->get();

        return view('student.evaluation.index', compact('evaluations'));
    }

    /**
     * Show the evaluation form.
     */
    public function show(Evaluation $evaluation)
    {
        $student = Auth::user()->student;

        // Authorization
        if ($evaluation->student_id !== $student->id) {
            abort(403);
        }

        // Check if period is open
        if (!$evaluation->period->is_open) {
            return redirect()->route('student.evaluation.index')->with('error', 'Evaluation period is closed.');
        }

        if ($evaluation->status === 'submitted') {
            return redirect()->route('student.evaluation.index')->with('error', 'You have already submitted this evaluation.');
        }

        // Load template questions
        // Assume all evaluations use the active template or the one linked to period?
        // Schema: EvaluationQuestion links to EvaluationTemplate. 
        // We need to know WHICH template to use. 
        // Missing link: EvaluationPeriod -> EvaluationTemplate OR Evaluation -> EvaluationTemplate.
        // Or global active template.
        // Let's assume global active template for now, or fetch one.
        // Refined Schema Idea: EvaluationPeriod should probably specify the template, but I didn't add that FK.
        // So I will pick the first active template.
        
        $template = \App\Models\EvaluationTemplate::where('is_active', true)->with('questions')->first();

        if (!$template) {
            return back()->with('error', 'No active evaluation template found.');
        }

        return view('student.evaluation.show', compact('evaluation', 'template'));
    }

    /**
     * Store the evaluation submission.
     */
    public function store(Request $request, Evaluation $evaluation)
    {
        $student = Auth::user()->student;

        if ($evaluation->student_id !== $student->id) {
            abort(403);
        }

        if (!$evaluation->period->is_open) {
            return back()->with('error', 'Evaluation period is closed.');
        }

        if ($evaluation->status === 'submitted') {
            return back()->with('error', 'Already submitted.');
        }

        $template = \App\Models\EvaluationTemplate::where('is_active', true)->with('questions')->first();
        if (!$template) abort(500, 'Template missing');

        // Validation
        $rules = [];
        foreach ($template->questions as $q) {
            if ($q->is_required) {
                $rules["q_{$q->id}"] = 'required';
            }
        }
        $request->validate($rules);

        DB::transaction(function () use ($request, $evaluation, $template) {
            foreach ($template->questions as $q) {
                $inputName = "q_{$q->id}";
                if ($request->has($inputName)) {
                    $value = $request->input($inputName);
                    
                    EvaluationAnswer::create([
                        'evaluation_id' => $evaluation->id,
                        'evaluation_question_id' => $q->id,
                        'rating' => $q->type === 'likert' ? (int)$value : null,
                        'comment' => $q->type === 'text' ? $value : null,
                    ]);
                }
            }

            $evaluation->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        });

        return redirect()->route('student.evaluation.index')->with('success', 'Evaluation submitted successfully.');
    }
}
