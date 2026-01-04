<?php

namespace App\Services;

use App\Models\User;
use App\Models\ModuleAssignment;
use App\Models\ModuleOffering;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\ModuleResult;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TeacherPerformanceService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('performance');
    }

    /**
     * Get Executive Overview Metrics (KPIs, Trends, Ranked Teachers)
     */
    public function getOverview($filters = [])
    {
        $teachers = $this->getFilteredTeachers($filters);
        
        // Compute KPIs
        $kpis = [
            'avg_delivery_rate' => $teachers->avg('metrics.delivery_rate') ?? 0,
            'attendance_completion' => $teachers->avg('metrics.attendance_completion') ?? 0,
            'on_time_coursework' => $teachers->avg('metrics.upload_timeliness') ?? 0,
            'results_compliance' => $teachers->avg('metrics.results_compliance') ?? 0,
            'avg_evaluation' => $teachers->avg('metrics.evaluation_rating') ?? 0,
            'evaluation_count' => $teachers->sum('metrics.evaluation_count'),
            'needing_attention' => $teachers->where('performance_status', 'Needs Attention')->count(),
        ];

        // Trends (Mock implementation for now as historical snapshots might not exist)
        $trends = [
            'delivery_rate' => $this->calculateTrend('delivery_rate', $teachers),
            'upload_timeliness' => $this->calculateTrend('upload_timeliness', $teachers),
            'evaluation_rating' => $this->calculateTrend('evaluation_rating', $teachers),
        ];

        return [
            'kpis' => $kpis,
            'trends' => $trends,
            'teachers' => $teachers->sortByDesc('performance_index')->values(),
        ];
    }

    /**
     * Get Detailed Scorecard for a Teacher
     */
    public function getTeacherScorecard($teacherId, $filters = [])
    {
        $teacher = User::with(['staffProfile.department'])->findOrFail($teacherId);
        $assignments = $this->getTeacherAssignments($teacherId, $filters);
        
        $metrics = $this->calculateTeacherMetrics($teacher, $assignments);
        $peers = $this->getPeerComparison($teacher, $metrics, $filters);
        $alerts = $this->getTeacherAlerts($teacher, $metrics);

        return [
            'teacher' => $teacher,
            'metrics' => $metrics,
            'assignments' => $assignments,
            'peers' => $peers,
            'alerts' => $alerts,
            'evidence' => [
                'evaluations' => $this->getEvaluationEvidence($teacherId, $filters),
                'results' => $this->getResultsEvidence($teacherId, $filters),
                // Add attendance evidence if model existed
            ]
        ];
    }

    /**
     * Get System-wide Alerts
     */
    public function getSystemAlerts($filters = [])
    {
        $teachers = $this->getFilteredTeachers($filters);
        $alerts = collect();

        foreach ($teachers as $teacher) {
            $teacherAlerts = $this->getTeacherAlerts($teacher, $teacher->metrics);
            foreach ($teacherAlerts as $alert) {
                $alerts->push([
                    'teacher' => $teacher,
                    'type' => $alert['type'],
                    'message' => $alert['message'],
                    'metric' => $alert['metric'],
                    'value' => $alert['value'],
                    'threshold' => $alert['threshold'],
                ]);
            }
        }

        return $alerts;
    }

    // --- Private Helpers ---

    private function getFilteredTeachers($filters)
    {
        // 1. Base Query: Teachers with active assignments in relevant period
        // Optimized to avoid N+1
        $query = User::role('teacher') // Assuming Spatie Permission
            ->with(['staffProfile.department', 'teacherAssignments' => function($q) use ($filters) {
                // Filter assignments if necessary (e.g. by academic year via ModuleOffering)
                if (isset($filters['academic_year_id'])) {
                    $q->whereHas('moduleOffering', function($sq) use ($filters) {
                        $sq->where('academic_year_id', $filters['academic_year_id']);
                    });
                }
            }])
            ->whereHas('teacherAssignments', function($q) use ($filters) {
                if (isset($filters['academic_year_id'])) {
                    $q->whereHas('moduleOffering', function($sq) use ($filters) {
                        $sq->where('academic_year_id', $filters['academic_year_id']);
                    });
                }
                // Filter by Programme/NTA Level
                if (isset($filters['program_id']) || isset($filters['nta_level'])) {
                    $q->whereHas('moduleOffering.module.program', function($sq) use ($filters) {
                        if (isset($filters['program_id'])) {
                            $sq->where('id', $filters['program_id']);
                        }
                        if (isset($filters['nta_level'])) {
                            $sq->where('nta_level', $filters['nta_level']);
                        }
                    });
                }
            });

        if (!empty($filters['department_id'])) {
            $query->whereHas('staffProfile', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        
        // Filter by name if searching
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $teachers = $query->get();

        // Calculate metrics for each teacher
        return $teachers->map(function ($teacher) use ($filters) {
            $assignments = $this->getTeacherAssignments($teacher->id, $filters);
            $metrics = $this->calculateTeacherMetrics($teacher, $assignments, $filters);
            
            $teacher->metrics = $metrics;
            $teacher->performance_index = $metrics['performance_index'];
            $teacher->performance_status = $this->getStatus($metrics['performance_index']);
            
            return $teacher;
        });
    }

    private function getTeacherAssignments($teacherId, $filters)
    {
        $query = ModuleAssignment::where('teacher_user_id', $teacherId)
            ->with(['moduleOffering.module.program', 'moduleOffering.academicYear', 'moduleOffering.semester']);

        if (!empty($filters['academic_year_id'])) {
            $query->whereHas('moduleOffering', function($q) use ($filters) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            });
        }

        if (!empty($filters['semester_id'])) {
            $query->whereHas('moduleOffering', function($q) use ($filters) {
                $q->where('semester_id', $filters['semester_id']);
            });
        }
        
        return $query->get();
    }

    private function calculateTeacherMetrics($teacher, $assignments, $filters = [])
    {
        // 1. Delivery Rate & Attendance
        // Mocking logic - returning null for "Missing Data" scenarios or rand for demo
        // In production, link to AttendanceSession model
        $deliveryRate = null; 
        $attendanceCompletion = null;
        
        // 2. Assessment Timeliness (Coursework)
        // Mocking logic
        $timeliness = 85; // Default mock
        
        // 3. Evaluations
        // Calculate average from answers linked to evaluations for this teacher
        $evalQuery = EvaluationAnswer::whereHas('evaluation', function($q) use ($teacher, $filters) {
            $q->where('teacher_id', $teacher->id);
            
            if (!empty($filters['start_date'])) {
                $q->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $q->whereDate('created_at', '<=', $filters['end_date']);
            }
        })->whereNotNull('rating');
        
        $evalRating = $evalQuery->avg('rating') ?? 0;

        $evalCountQuery = Evaluation::where('teacher_id', $teacher->id);
        if (!empty($filters['start_date'])) {
            $evalCountQuery->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $evalCountQuery->whereDate('created_at', '<=', $filters['end_date']);
        }
        $evalCount = $evalCountQuery->count();

        // 4. Results Compliance
        $compliance = 90; // Default mock
        
        // --- Calculate Index ---
        // Using config weights
        $weights = $this->config['weights'];
        
        // Treat nulls as 0 for calculation but keep them null for UI
        $deliveryScore = ($deliveryRate ?? 0);
        $attendanceScore = ($attendanceCompletion ?? 0);
        
        $index = 
            ($deliveryScore * ($weights['delivery_rate'] / 100)) +
            ($attendanceScore * ($weights['attendance_completion'] / 100)) +
            ($timeliness * ($weights['assessment_timeliness'] / 100)) +
            (($evalRating / 5 * 100) * ($weights['evaluation_rating'] / 100)) +
            ($compliance * ($weights['results_compliance'] / 100));

        return [
            'delivery_rate' => $deliveryRate, // null means "No Data"
            'attendance_completion' => $attendanceCompletion,
            'upload_timeliness' => $timeliness,
            'evaluation_rating' => round($evalRating, 1),
            'evaluation_count' => $evalCount,
            'results_compliance' => $compliance,
            'performance_index' => round($index, 1),
        ];
    }

    private function getPeerComparison($teacher, $metrics, $filters)
    {
        $deptId = $teacher->staffProfile->department_id ?? null;
        if (!$deptId) return null;

        // Note: In a high-traffic system, these averages should be cached daily
        // For now, we'll calculate on the fly or use static values to save resources
        
        // Mocking averages for demonstration
        return [
            'department_avg_index' => 75, 
            'institution_avg_index' => 72,
            'department_avg_delivery' => 82,
            'institution_avg_delivery' => 78,
        ];
    }

    private function getTeacherAlerts($teacher, $metrics)
    {
        $alerts = [];
        $thresholds = $this->config['thresholds'];

        // 1. Delivery Rate
        if ($metrics['delivery_rate'] !== null && $metrics['delivery_rate'] < $thresholds['delivery_rate']) {
            $alerts[] = ['type' => 'critical', 'message' => 'Low Delivery Rate', 'metric' => 'delivery_rate', 'value' => $metrics['delivery_rate'] . '%', 'threshold' => $thresholds['delivery_rate'] . '%'];
        }
        
        // 2. Attendance Completion
        if ($metrics['attendance_completion'] !== null && $metrics['attendance_completion'] < $thresholds['attendance_completion']) {
            $alerts[] = ['type' => 'warning', 'message' => 'Low Attendance Completion', 'metric' => 'attendance_completion', 'value' => $metrics['attendance_completion'] . '%', 'threshold' => $thresholds['attendance_completion'] . '%'];
        }

        // 3. Evaluations
        if ($metrics['evaluation_rating'] > 0 && $metrics['evaluation_rating'] < $thresholds['min_evaluation_rating']) {
            $alerts[] = ['type' => 'warning', 'message' => 'Low Student Ratings', 'metric' => 'evaluation_rating', 'value' => $metrics['evaluation_rating'], 'threshold' => $thresholds['min_evaluation_rating']];
        }

        // 4. Missing Data
        if ($metrics['delivery_rate'] === null) {
            $alerts[] = ['type' => 'info', 'message' => 'Missing Attendance Data', 'metric' => 'delivery_rate', 'value' => 'N/A', 'threshold' => 'N/A'];
        }

        // 5. Mock Alerts for Uploads & Results (since we don't have real dates yet)
        // In real app: check if (now - deadline) > threshold
        
        return $alerts;
    }

    private function getStatus($index)
    {
        $scale = $this->config['rating_scale'];
        if ($index >= $scale['excellent']) return 'Excellent';
        if ($index >= $scale['good']) return 'Good';
        return 'Needs Attention';
    }

    private function calculateTrend($metric, $teachers)
    {
        // Placeholder for trend calculation
        return rand(-5, 5); 
    }

    private function getEvaluationEvidence($teacherId, $filters)
    {
        $query = Evaluation::where('teacher_id', $teacherId)
            ->with(['moduleOffering.module', 'answers']);
            
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $evals = $query->limit(10)->get();
            
        return $evals->map(function($eval) {
            $count = $eval->answers->count(); // Assuming one answer per student per eval session, or logic needs adjustment
            // If Evaluation is a session, count how many students participated. 
            // Often 'Evaluation' is the parent, 'EvaluationAnswer' are the rows.
            // Let's assume EvaluationAnswer has 'student_id'
            
            $confidence = 'Low';
            if ($count >= 30) $confidence = 'High';
            elseif ($count >= 10) $confidence = 'Medium';
            
            $eval->confidence = $confidence;
            $eval->response_count = $count;
            return $eval;
        });
    }

    private function getResultsEvidence($teacherId, $filters)
    {
        // Find module offerings taught by this teacher
        $offeringIds = ModuleAssignment::where('teacher_user_id', $teacherId)->pluck('module_offering_id');
        
        // Get results summary
        return ModuleResult::whereIn('module_offering_id', $offeringIds)
            ->select('module_offering_id', DB::raw('count(*) as total_students'), DB::raw('avg(total_mark) as avg_mark'))
            ->groupBy('module_offering_id')
            ->with('moduleOffering.module')
            ->get();
    }
}
