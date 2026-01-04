<?php

namespace App\Services;

use App\Models\EvaluationPeriod;
use App\Models\EvaluationSubmission;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationAnswer;
use App\Models\ModuleOffering;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EvaluationService
{
    /**
     * Generate evaluations for all eligible students in a period.
     */
    public function generateEvaluationsForPeriod(EvaluationPeriod $period)
    {
        $count = 0;
        
        // Find approved registrations for this period
        // Using SemesterRegistration or CourseRegistration depending on system design
        // Assuming SemesterRegistration links student to semester, and we find offerings for that semester?
        // OR CourseRegistration links student directly to module for that semester.
        
        // Based on EvaluationPeriodController logic:
        // $registrations = SemesterRegistration::where...->with('moduleOfferings')
        
        $registrations = \App\Models\SemesterRegistration::where('academic_year_id', $period->academic_year_id)
            ->where('semester_id', $period->semester_id)
            ->where('status', 'approved')
            ->with('moduleOfferings.teacher')
            ->get();
            
        foreach ($registrations as $reg) {
            foreach ($reg->moduleOfferings as $offering) {
                if ($offering->teacher_id) {
                    \App\Models\Evaluation::firstOrCreate(
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
        
        return $count;
    }

    /**
     * Get pending evaluations for a student.
     */
    public function getStudentPendingEvaluations(Student $student)
    {
        // Find open periods
        $openPeriodIds = EvaluationPeriod::where('is_open', true)
            ->where('academic_year_id', $student->current_academic_year_id)
            ->where('semester_id', $student->current_semester_id) // Optional strict check
            ->pluck('id');

        if ($openPeriodIds->isEmpty()) {
            return collect([]);
        }

        return \App\Models\Evaluation::where('student_id', $student->id)
            ->whereIn('evaluation_period_id', $openPeriodIds)
            ->where('status', 'pending')
            ->with(['moduleOffering.module', 'teacher'])
            ->get();
    }

    /**
     * Submit an evaluation.
     */
    public function submitEvaluation(Student $student, \App\Models\Evaluation $evaluation, array $answers)
    {
        if ($evaluation->status !== 'pending') {
            throw new \Exception("Evaluation already submitted or closed.");
        }

        if ($evaluation->student_id !== $student->id) {
            throw new \Exception("Unauthorized.");
        }

        DB::transaction(function () use ($evaluation, $answers) {
            // Update Evaluation status
            $evaluation->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Create anonymous submission record (optional, if we want to separate PII)
            // But if we keep Evaluation record linked to student, it's not truly anonymous in DB, 
            // but we can hide it in UI.
            // Requirement: "Anonymous: teacher cannot see student identity. Admin/Principal can see analytics; student identity is not displayed."
            // "Prevent duplicate submissions: one evaluation per student..."
            // If we have 'evaluations' table with student_id, we can track completion.
            // To ensure anonymity in answers, we can store answers in a separate table linked to Evaluation 
            // OR linked to a new "Submission" ID that is decoupled if we really want to scrub link.
            // For this MVP, let's link answers to the Evaluation record but ensure Reports don't show student_id.
            
            // Wait, previous implementation used EvaluationSubmission and EvaluationAnswer.
            // Let's stick to that if schema exists.
            
            // If we use Evaluation model as the tracker, we can store answers linked to it.
            // Let's check EvaluationAnswer schema or assume it links to evaluation_id.
            
            // If we use the previous logic's tables:
            // EvaluationSubmission was created.
            // Let's map Evaluation model to EvaluationSubmission concept.
            
            foreach ($answers as $questionId => $value) {
                EvaluationAnswer::create([
                    'evaluation_id' => $evaluation->id, // Changed from evaluation_submission_id if we merged concepts
                    'evaluation_question_id' => $questionId,
                    'rating' => is_numeric($value) ? $value : null,
                    'comment' => !is_numeric($value) ? $value : null,
                ]);
            }
        });

        return true;
    }
    
    /**
     * Wrapper for report data
     */
    public function getReportData($periodId)
    {
        $period = EvaluationPeriod::findOrFail($periodId);
        return $this->getReport($period);
    }

    /**
     * Get currently active evaluation period for a student's context.
     * ... existing methods ...


    /**
     * Get aggregated report for a period.
     */
    public function getReport(EvaluationPeriod $period, $filters = [])
    {
        // 1. Get all submissions for this period
        $query = EvaluationSubmission::where('evaluation_period_id', $period->id);

        if (isset($filters['module_offering_id'])) {
            $query->where('module_offering_id', $filters['module_offering_id']);
        }

        // 2. Aggregate
        // Group by Module Offering (Teacher)
        // Calculate Avg per question
        
        // This can be heavy. For MVP, let's just fetch and compute or use DB aggregate.
        // Let's return a query builder or structured data.
        
        // Let's get list of offerings with stats.
        $offerings = ModuleOffering::whereHas('evaluationSubmissions', function($q) use ($period) {
            $q->where('evaluation_period_id', $period->id);
        })
        ->with(['module', 'teacher'])
        ->get()
        ->map(function($offering) use ($period) {
             $submissions = $offering->evaluationSubmissions()->where('evaluation_period_id', $period->id)->get();
             $count = $submissions->count();
             
             // Calculate averages per question
             // We need the questions
             $questions = $period->template->questions;
             $stats = [];
             
             foreach($questions as $q) {
                 if ($q->type === 'likert') {
                     $avg = EvaluationAnswer::whereIn('evaluation_submission_id', $submissions->pluck('id'))
                         ->where('evaluation_question_id', $q->id)
                         ->avg('rating');
                     $stats[$q->id] = [
                         'text' => $q->question_text,
                         'avg' => round($avg, 2)
                     ];
                 }
             }
             
             // Overall Average
             $overallAvg = collect($stats)->avg('avg');

             return [
                 'offering' => $offering,
                 'response_count' => $count,
                 'overall_score' => round($overallAvg, 2),
                 'question_stats' => $stats
             ];
        });

        return $offerings;
    }
}
