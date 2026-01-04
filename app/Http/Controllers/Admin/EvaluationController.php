<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function dashboard()
    {
        // Active Period
        $activePeriod = EvaluationPeriod::where('is_open', true)->first();

        // Stats
        $totalEvaluations = Evaluation::count();
        $submittedEvaluations = Evaluation::where('status', 'submitted')->count();
        $pendingEvaluations = Evaluation::where('status', 'pending')->count();
        
        // Participation Rate
        $participationRate = $totalEvaluations > 0 ? round(($submittedEvaluations / $totalEvaluations) * 100, 1) : 0;

        // Recent Activity
        $recentSubmissions = Evaluation::where('status', 'submitted')
            ->with(['student', 'teacher', 'moduleOffering.module'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('admin.evaluation.dashboard', compact(
            'activePeriod', 
            'totalEvaluations', 
            'submittedEvaluations', 
            'pendingEvaluations', 
            'participationRate',
            'recentSubmissions'
        ));
    }

    public function reports(Request $request)
    {
        // Permission check for "view_own_evaluation_summary"
        $user = auth()->user();
        $canViewAll = $user->hasPermissionTo('view_evaluation_reports') || $user->hasRole('admin') || $user->hasRole('principal');
        $canViewOwn = $user->hasPermissionTo('view_own_evaluation_summary');

        if (!$canViewAll && !$canViewOwn) {
            abort(403, 'Unauthorized access to evaluation reports.');
        }

        // Filters
        $periods = EvaluationPeriod::orderByDesc('created_at')->get();
        $selectedPeriodId = $request->input('period_id', $periods->first()?->id);
        
        $results = collect();
        $questionStats = collect();

        if ($selectedPeriodId) {
            $query = DB::table('evaluation_answers as a')
                ->join('evaluations as e', 'a.evaluation_id', '=', 'e.id')
                ->join('evaluation_questions as q', 'a.evaluation_question_id', '=', 'q.id')
                ->join('users as t', 'e.teacher_id', '=', 't.id')
                ->join('module_offerings as mo', 'e.module_offering_id', '=', 'mo.id')
                ->join('modules as m', 'mo.module_id', '=', 'm.id')
                ->where('e.evaluation_period_id', $selectedPeriodId)
                ->where('e.status', 'submitted')
                ->where('q.type', 'likert');

            // Apply Teacher restriction if needed
            if (!$canViewAll && $canViewOwn) {
                $query->where('e.teacher_id', $user->id);
            }

            // 1. Overall Aggregates per Teacher/Module
            $results = $query->clone()
                ->select(
                    't.id as teacher_id',
                    't.name as teacher_name',
                    'm.id as module_id',
                    'm.name as module_name',
                    'm.code as module_code',
                    DB::raw('COUNT(DISTINCT e.id) as response_count'),
                    DB::raw('AVG(a.rating) as average_rating')
                )
                ->groupBy('t.id', 'm.id', 't.name', 'm.name', 'm.code')
                ->get();

            // 2. Per Question Stats (for detailed view)
            $questionStats = $query->clone()
                ->select(
                    't.id as teacher_id',
                    'm.id as module_id',
                    'q.id as question_id',
                    'q.question_text',
                    DB::raw('AVG(a.rating) as question_average')
                )
                ->groupBy('t.id', 'm.id', 'q.id', 'q.question_text')
                ->get()
                ->groupBy(function($item) {
                    return $item->teacher_id . '-' . $item->module_id;
                });
        }

        return view('admin.evaluation.reports', compact('periods', 'selectedPeriodId', 'results', 'questionStats'));
    }
}
