<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationPeriod;
use App\Models\Evaluation;
use App\Models\ModuleOffering;
use App\Models\SemesterRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EvaluationPeriodController extends Controller
{
    public function index()
    {
        $periods = EvaluationPeriod::orderByDesc('created_at')->withCount('evaluations')->get();
        return view('admin.evaluation.periods.index', compact('periods'));
    }

    public function create()
    {
        $academicYears = \App\Models\AcademicYear::all();
        $semesters = \App\Models\Semester::all();
        return view('admin.evaluation.periods.create', compact('academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        EvaluationPeriod::create($request->all() + ['is_open' => false]);
        return redirect()->route('admin.evaluation.periods.index')->with('success', 'Period created.');
    }
    
    public function edit(EvaluationPeriod $period)
    {
        return view('admin.evaluation.periods.edit', compact('period'));
    }

    public function update(Request $request, EvaluationPeriod $period)
    {
         $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_open' => 'boolean'
        ]);
        
        $period->fill($request->only(['start_date', 'end_date', 'is_open']));

        if ($period->isDirty('end_date')) {
            \Illuminate\Support\Facades\Log::info("AUDIT: Evaluation Period {$period->id} end_date changed from {$period->getOriginal('end_date')} to {$period->end_date} by User " . Auth::id());
        }

        if ($period->isDirty('is_open')) {
            \Illuminate\Support\Facades\Log::info("AUDIT: Evaluation Period {$period->id} status changed to " . ($period->is_open ? 'OPEN' : 'CLOSED') . " by User " . Auth::id());
        }

        $period->save();
        return redirect()->route('admin.evaluation.periods.index')->with('success', 'Period updated.');
    }

    public function generateEvaluations(Request $request)
    {
        $request->validate(['period_id' => 'required|exists:evaluation_periods,id']);
        $period = EvaluationPeriod::findOrFail($request->period_id);
        
        $count = 0;
        
        // Find approved registrations for this period
        $registrations = SemesterRegistration::where('academic_year_id', $period->academic_year_id)
            ->where('semester_id', $period->semester_id)
            ->where('status', 'approved') // Or whatever status means registered
            ->with('moduleOfferings.teacher')
            ->get();
            
        foreach ($registrations as $reg) {
            foreach ($reg->moduleOfferings as $offering) {
                if ($offering->teacher_id) {
                    Evaluation::firstOrCreate(
                        [
                            'evaluation_period_id' => $period->id,
                            'student_id' => $reg->student_id,
                            'module_offering_id' => $offering->id,
                        ],
                        [
                            'teacher_id' => $offering->teacher_id,
                            'status' => 'pending'
                        ]
                    );
                    $count++;
                }
            }
        }
        
        return redirect()->back()->with('success', "Evaluations generated/updated for {$count} records.");
    }
    
    public function destroy(EvaluationPeriod $period)
    {
        if ($period->evaluations()->where('status', 'submitted')->exists()) {
             return back()->with('error', 'Cannot delete period with submitted evaluations.');
        }
        $period->delete();
        return back()->with('success', 'Period deleted.');
    }
}
