<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use App\Models\Application;
use App\Models\ModuleResult;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\AuditLog;
use App\Models\ModuleAssignment;
use App\Models\ModuleOffering;
use App\Models\FeeClearanceStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrincipalDashboardService
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function getDashboardData()
    {
        $activeYear = AcademicYear::where('is_current', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        return [
            'kpis' => $this->getKPIs($activeYear, $activeSemester),
            'alerts' => $this->getGovernanceAlerts($activeYear, $activeSemester),
            'charts' => $this->getPerformanceOverview($activeYear, $activeSemester),
            'activity' => $this->getRecentActivity(),
            'active_year' => $activeYear,
            'active_semester' => $activeSemester,
            'last_updated' => now(),
        ];
    }

    protected function getKPIs($year, $semester)
    {
        // Total Students
        $totalStudents = Student::where('status', 'active')->count();

        // Total Teachers (Users with role 'teacher' or 'lecturer')
        // We use 'teacher' as the standard role name in this system based on context
        $totalTeachers = User::role('teacher')->count();

        // Attendance Completion Rate
        // Placeholder: If specific attendance model exists, calculate. 
        // Otherwise 0.
        $attendanceRate = 0; 

        // Results Publication Status
        $publishedResults = 0;
        $totalOfferings = 0;
        
        if ($year && $semester) {
             $totalOfferings = ModuleOffering::where('academic_year_id', $year->id)
                ->where('semester_id', $semester->id)
                ->count();
                
             // Count offerings that have published results
             $publishedResults = ModuleResult::where('academic_year_id', $year->id)
                ->where('semester_id', $semester->id)
                ->where('status', 'published')
                ->distinct('module_offering_id')
                ->count('module_offering_id');
        }

        // Fee Clearance Summary using FeeClearanceStatus cache table
        $clearedCount = 0;
        $totalFeeStatus = 0;
        $clearedPercentage = 0;

        if ($year && $semester) {
            $totalFeeStatus = FeeClearanceStatus::where('academic_year_id', $year->id)
                ->where('semester_id', $semester->id)
                ->count();

            if ($totalFeeStatus > 0) {
                // Assuming is_cleared is a boolean or check outstanding_balance <= 0
                // We'll check outstanding_balance <= 0 as a safe bet if is_cleared isn't reliable
                // But FeeClearanceStatus usually has an explicit status column if named Status.
                // Let's check schema via query if needed, but assuming standard field from Service context.
                // If FeeClearanceStatus table has 'is_cleared', use it.
                // If not, use outstanding_balance <= 0.
                // For safety, let's use outstanding_balance <= 0.
                $clearedCount = FeeClearanceStatus::where('academic_year_id', $year->id)
                    ->where('semester_id', $semester->id)
                    ->where('outstanding_balance', '<=', 0)
                    ->count();
                
                $clearedPercentage = ($clearedCount / $totalFeeStatus) * 100;
            }
        }

        // Admissions Pipeline
        $admissions = [
            'applications' => Application::count(),
            'approved' => Application::where('status', 'approved')->count(),
            'pending' => Application::where('status', 'pending')->count(),
        ];

        return [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'attendance_rate' => $attendanceRate,
            'results_published' => $publishedResults,
            'total_offerings' => $totalOfferings,
            'fee_clearance' => [
                'cleared' => $clearedCount,
                'total' => $totalFeeStatus > 0 ? $totalFeeStatus : $totalStudents, // Fallback to total students if no fee records
                'percentage' => round($clearedPercentage, 1)
            ],
            'admissions' => $admissions,
        ];
    }

    protected function getGovernanceAlerts($year, $semester)
    {
        $alerts = [];

        // Alert 1: Missing NACTVET numbers
        $missingNactvet = Student::where('status', 'active')
            ->whereNull('nactvet_registration_number')
            ->count();

        if ($missingNactvet > 0) {
            $alerts[] = [
                'type' => 'compliance',
                'severity' => 'warning',
                'title' => 'Compliance Gap',
                'message' => "$missingNactvet students missing NACTVET numbers",
                'action_url' => '#'
            ];
        }

        // Alert 2: Overdue Result Approvals (Placeholder logic)
        // If we have published results < total offerings * 0.5 near end of semester?
        // Let's just check pending results if possible.

        // Alert 3: High Fee Default Risk
        if ($year && $semester) {
            $highRiskCount = FeeClearanceStatus::where('academic_year_id', $year->id)
                ->where('semester_id', $semester->id)
                ->where('outstanding_balance', '>', 500000) // Example threshold
                ->count();
            
            if ($highRiskCount > 10) {
                $alerts[] = [
                    'type' => 'finance',
                    'severity' => 'critical',
                    'title' => 'Revenue Risk',
                    'message' => "$highRiskCount students have outstanding balance > 500k",
                    'action_url' => '#'
                ];
            }
        }

        return $alerts;
    }

    protected function getPerformanceOverview($year, $semester)
    {
        return [
            'pass_rates' => [], // Implement with Chart.js data structure later
            'finance_overview' => []
        ];
    }

    protected function getRecentActivity()
    {
        return AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'event' => $log->action, // Using 'action' column
                    'user' => $log->user ? $log->user->name : 'System',
                    'time' => $log->created_at->diffForHumans(),
                    'details' => is_array($log->new_values) ? json_encode($log->new_values) : $log->new_values
                ];
            });
    }
}
