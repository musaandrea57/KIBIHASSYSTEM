<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ModuleResult;
use App\Models\ContinuousAssessment;
use App\Models\GradeScale;
use App\Models\ModuleOffering;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ResultsService
{
    /**
     * Helper to log changes
     */
    protected function logChange(User $user, $action, $model, $oldValues, $newValues)
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get Grade Scale for a mark.
     */
    public function getGradeForMark($mark)
    {
        // Round mark to nearest whole number or 1 decimal? 
        // Usually grading is on rounded marks.
        // Let's assume standard rounding.
        $roundedMark = round($mark);

        $gradeScale = GradeScale::where('min_mark', '<=', $roundedMark)
            ->where('max_mark', '>=', $roundedMark)
            ->where('is_active', true)
            ->first();

        return $gradeScale;
    }

    /**
     * Update or Create CA mark.
     */
    public function upsertCA(Student $student, ModuleOffering $offering, $caData, User $teacher)
    {
        // $caData can be a single value or array of components
        // For simplicity, let's assume we are updating the total CA mark on ModuleResult
        // OR creating detailed ContinuousAssessment entries.
        
        // Ensure ModuleResult exists
        $result = ModuleResult::firstOrCreate(
            [
                'student_id' => $student->id,
                'module_offering_id' => $offering->id,
            ],
            [
                'academic_year_id' => $offering->academic_year_id,
                'semester_id' => $offering->semester_id,
                'credits_snapshot' => $offering->module->credits,
                'status' => 'draft',
                'uploaded_by' => $teacher->id,
            ]
        );

        // Update CA Mark
        $result->cw_mark = $caData['mark']; // Assuming total CA for now
        $result->save();

        // Recalculate totals
        $this->computeModuleResult($result);

        return $result;
    }

    /**
     * Update or Create Exam mark.
     */
    public function upsertExam(Student $student, ModuleOffering $offering, $mark, User $teacher)
    {
        $result = ModuleResult::firstOrCreate(
            [
                'student_id' => $student->id,
                'module_offering_id' => $offering->id,
            ],
            [
                'academic_year_id' => $offering->academic_year_id,
                'semester_id' => $offering->semester_id,
                'credits_snapshot' => $offering->module->credits,
                'status' => 'draft',
                'uploaded_by' => $teacher->id,
            ]
        );

        $result->se_mark = $mark;
        $result->save();

        $this->computeModuleResult($result);

        return $result;
    }

    /**
     * Compute CW Mark from components.
     * Logic: (Avg Tests + Avg Assignments) / 2? Or Weighted?
     * Using simple logic: (Avg Tests * 0.5) + (Avg Assignments * 0.5) scaled to 100?
     * Or as per controller: (AvgTests * 0.2) + (AvgAssigns * 0.2) -> This assumes final CW is out of 40.
     * Let's stick to the controller logic which seemed to aim for a 40 mark max CW.
     */
    public function calculateCW($t1, $t2, $a1, $a2)
    {
        $avgTests = $this->avg($t1, $t2);
        $avgAssigns = $this->avg($a1, $a2);
        
        // Controller logic was: ($avgTests * 0.2) + ($avgAssigns * 0.2)
        // If Tests are out of 100, Avg is 100. 100 * 0.2 = 20.
        // Total = 20 + 20 = 40.
        // So this produces a CW mark out of 40.
        
        return round(($avgTests * 0.2) + ($avgAssigns * 0.2), 1);
    }
    
    private function avg($v1, $v2) {
        if ($v1 === null && $v2 === null) return 0;
        if ($v1 === null) return $v2;
        if ($v2 === null) return $v1;
        return ($v1 + $v2) / 2;
    }

    /**
     * Compute totals, grade, points.
     */
    public function computeModuleResult(ModuleResult $result)
    {
        $cw = $result->cw_mark ?? 0;
        $se = $result->se_mark ?? 0;
        
        // CW is already out of 40 (calculated above).
        // SE is out of 60 (calculated in controller or here).
        // If SE is entered as raw 100, we need to scale it here or before saving.
        // Let's assume SE on model is stored as the WEIGHTED value (out of 60) for consistency with CW (out of 40).
        
        $total = $cw + $se;
        
        $result->total_mark = round($total, 1);
        
        // Determine Grade
        $gradeScale = $this->getGradeForMark($total);
        
        if ($gradeScale) {
            $result->grade = $gradeScale->grade;
            $result->grade_point = round($gradeScale->grade_point, 1);
            
            // Remark Logic
            if ($gradeScale->grade === 'F') {
                $result->remark = 'Fail';
            } else {
                $result->remark = 'Pass';
            }
            
            // Calculate Points
            $credits = $result->credits_snapshot > 0 ? $result->credits_snapshot : ($result->moduleOffering->module->credits ?? 10);
            $result->points = round($gradeScale->grade_point * $credits, 1);
            
        } else {
            // Fallback
            $result->grade = 'I'; // Incomplete
            $result->grade_point = 0.0;
            $result->points = 0.0;
            $result->remark = 'Incomplete';
        }
        
        $result->save();
        
        return $result;
    }

    /**
     * Compute Semester Summary (GPA).
     */
    public function computeSemesterSummary(Student $student, $academicYearId, $semesterId)
    {
        $results = ModuleResult::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->get();
            
        $totalCredits = 0;
        $totalPoints = 0;
        
        foreach ($results as $result) {
            // Only count if complete?
            if ($result->grade) {
                $totalCredits += $result->credits_snapshot;
                $totalPoints += $result->points;
            }
        }
        
        $gpa = $totalCredits > 0 ? ($totalPoints / $totalCredits) : 0;
        
        // Classification
        $classification = 'Fail';
        if ($gpa >= 3.5) $classification = 'Distinction';
        elseif ($gpa >= 3.0) $classification = 'Credit';
        elseif ($gpa >= 2.0) $classification = 'Pass';
        
        return [
            'total_credits' => $totalCredits,
            'total_points' => $totalPoints,
            'gpa' => round($gpa, 2), // Standard 1 or 2 decimals
            'classification' => $classification
        ];
    }
    
    public function publishResultsForOffering(ModuleOffering $offering, User $admin)
    {
        // Update all results for this offering to published
        $results = ModuleResult::where('module_offering_id', $offering->id)
            ->where('status', '!=', 'draft') // Only publish submitted ones? Or force publish all?
            ->get();
            
        foreach ($results as $result) {
            $oldStatus = $result->status;
            
            $result->update([
                'status' => 'published',
                'published_by' => $admin->id,
                'published_at' => now(),
                'approved_by' => $admin->id, // Auto approve on publish
            ]);
            
            $this->logChange($admin, 'publish_result', $result, ['status' => $oldStatus], ['status' => 'published']);
        }
    }
}
