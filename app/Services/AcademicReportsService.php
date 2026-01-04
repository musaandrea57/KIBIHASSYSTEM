<?php

namespace App\Services;

use App\Models\Student;
use App\Models\SemesterRegistration;
use App\Models\ModuleResult;
use App\Models\ModuleOffering;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AcademicReportsService
{
    protected $cacheTtl = 900; // 15 minutes

    /**
     * Get Executive Overview KPIs
     */
    public function getKpis(array $filters)
    {
        $key = 'reports_kpis_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // 1. Total Enrolled Students (Active)
            $enrolledQuery = Student::where('status', 'active');
            $this->applyFilters($enrolledQuery, $filters, 'students');
            $totalEnrolled = $enrolledQuery->count();

            // 2. Semester Registered Students (for selected or current semester)
            $registeredQuery = SemesterRegistration::where('status', 'approved');
            $this->applyFilters($registeredQuery, $filters, 'semester_registrations');
            $semesterRegistered = $registeredQuery->count();

            // Calculate %
            $registrationRate = $totalEnrolled > 0 ? ($semesterRegistered / $totalEnrolled) * 100 : 0;

            // 3. Results Published
            $resultsQuery = ModuleResult::query();
            $this->applyModuleResultFilters($resultsQuery, $filters);
            $resultsPublished = (clone $resultsQuery)->whereNotNull('published_at')->count();
            $totalResults = $resultsQuery->count();
            $resultsPublishedRate = $totalResults > 0 ? ($resultsPublished / $totalResults) * 100 : 0;

            // 4. Pass Rate (Overall)
            $passedResults = (clone $resultsQuery)->where('total_mark', '>=', 40)->count();
            $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : 0;

            // 5. Carry/Fail Rate
            $failRate = $totalResults > 0 ? (100 - $passRate) : 0;

            // 6. Average GPA (Overall)
            $avgGpa = (clone $resultsQuery)->avg('grade_point') ?? 0;

            return [
                'total_enrolled' => $totalEnrolled,
                'semester_registered' => $semesterRegistered,
                'registration_rate' => round($registrationRate, 1),
                'results_published' => $resultsPublished,
                'results_published_rate' => round($resultsPublishedRate, 1),
                'pass_rate' => round($passRate, 1),
                'fail_rate' => round($failRate, 1),
                'avg_gpa' => round($avgGpa, 2),
            ];
        });
    }

    /**
     * Get High-Signal Alerts
     */
    public function getAlerts(array $filters)
    {
        // Alerts should be real-time or very short cache
        $key = 'reports_alerts_' . md5(serialize($filters));
        
        return Cache::remember($key, 300, function () use ($filters) {
            $alerts = [];

            // 1. Pending Result Approvals
            $pendingResults = ModuleResult::whereNotNull('uploaded_by')
                ->whereNull('published_at')
                ->select('module_offering_id', DB::raw('count(*) as count'))
                ->groupBy('module_offering_id')
                ->with('moduleOffering.module')
                ->get();

            if ($pendingResults->count() > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Pending Result Approvals',
                    'message' => $pendingResults->count() . ' modules have results waiting for publication.',
                    'link' => route('principal.reports.workflow', ['status' => 'pending']),
                ];
            }

            return $alerts;
        });
    }

    /**
     * Performance Reports Data
     */
    public function getPerformanceReports(array $filters)
    {
        $key = 'reports_perf_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Programme Performance Summary
            $query = ModuleResult::query();
            $this->applyModuleResultFilters($query, $filters);

            $summary = $query->join('students', 'module_results.student_id', '=', 'students.id')
                ->join('programs', 'students.program_id', '=', 'programs.id')
                ->select(
                    'students.program_id',
                    'programs.name as program_name',
                    'programs.code as program_code',
                    'students.current_nta_level',
                    DB::raw('count(*) as total_results'),
                    DB::raw('sum(case when module_results.total_mark >= 40 then 1 else 0 end) as passed'),
                    DB::raw('avg(module_results.total_mark) as avg_mark'),
                    DB::raw('avg(module_results.grade_point) as avg_gpa')
                )
                ->groupBy('students.program_id', 'programs.name', 'programs.code', 'students.current_nta_level')
                ->get();

            $summary->transform(function($item) {
                $item->pass_rate = $item->total_results > 0 ? ($item->passed / $item->total_results) * 100 : 0;
                return $item;
            });

            return $summary;
        });
    }

    /**
     * Registration Reports Data
     */
    public function getRegistrationReports(array $filters)
    {
        $key = 'reports_reg_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Summary: Registered vs Not Registered by Programme
            $query = Student::where('status', 'active')
                ->join('programs', 'students.program_id', '=', 'programs.id')
                ->select(
                    'students.program_id',
                    'programs.name as program_name',
                    'programs.code as program_code',
                    'students.current_nta_level',
                    DB::raw('count(*) as total_students')
                );

            $this->applyFilters($query, $filters, 'students');
            
            // Subquery or left join to get registration counts
            // For efficiency, we might fetch all and then count, or use withCount if relationship exists
            // Let's assume there is a relationship semesterRegistrations() on Student
            
            // We need to count students who HAVE a semester_registration for the selected filters
            
            $data = $query->groupBy('students.program_id', 'programs.name', 'programs.code', 'students.current_nta_level')
                ->get();

            // Enrich with registration counts
            foreach ($data as $row) {
                $regQuery = SemesterRegistration::whereHas('student', function($q) use ($row) {
                    $q->where('program_id', $row->program_id)
                      ->where('current_nta_level', $row->current_nta_level);
                })->where('status', 'approved');
                
                if (!empty($filters['academic_year_id'])) {
                    $regQuery->where('academic_year_id', $filters['academic_year_id']);
                }
                if (!empty($filters['semester_id'])) {
                    $regQuery->where('semester_id', $filters['semester_id']);
                }
                
                $row->registered_count = $regQuery->count();
                $row->unregistered_count = $row->total_students - $row->registered_count;
                $row->registration_rate = $row->total_students > 0 ? ($row->registered_count / $row->total_students) * 100 : 0;
            }

            return $data;
        });
    }

    /**
     * Progression Reports Data
     */
    public function getProgressionReports(array $filters)
    {
        $key = 'reports_prog_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Simple count of students by status (Active, Discontinued, etc.) per programme
            $query = Student::join('programs', 'students.program_id', '=', 'programs.id')
                 ->select(
                    'programs.name as program_name',
                    'students.status',
                    DB::raw('count(*) as count')
                 );
            
            $this->applyFilters($query, $filters, 'students');

            return $query->groupBy('programs.name', 'students.status')->get();
        });
    }

    /**
     * Workflow Reports Data
     */
    public function getWorkflowReports(array $filters)
    {
        // Real-time needed for workflow
        $query = ModuleOffering::with(['module', 'moduleResults'])
            ->select('module_offerings.*');
            
        // Add logic to determine status based on results
        // This is complex, usually we'd aggregate results status
        // For now, list modules and their result upload status
        
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }

        $modules = $query->get();
        
        $report = $modules->map(function($offering) {
            $results = $offering->moduleResults;
            $total = $results->count();
            $published = $results->whereNotNull('published_at')->count();
            
            $status = 'Pending Upload';
            if ($total > 0) {
                if ($published == $total) {
                    $status = 'Published';
                } elseif ($published > 0) {
                    $status = 'Partial';
                } else {
                    $status = 'Pending Approval';
                }
            }

            return [
                'module_code' => $offering->module->code,
                'module_name' => $offering->module->name,
                'total_students' => $total, // Approximate
                'status' => $status,
                'last_updated' => $offering->updated_at,
            ];
        });

        return $report;
    }

    /**
     * Compliance Reports Data
     */
    public function getComplianceReports(array $filters)
    {
        $key = 'reports_comp_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Base query for active students
            $query = Student::where('status', 'active');
            $this->applyFilters($query, $filters, 'students');

            // 1. Overall Summary
            $total = $query->count();
            $missingNactvet = (clone $query)->whereNull('nactvet_registration_number')->count();
            
            // 2. Breakdown by Programme
            $breakdownQuery = Student::where('status', 'active')
                ->join('programs', 'students.program_id', '=', 'programs.id')
                ->select(
                    'programs.name as program_name',
                    'programs.code as program_code',
                    DB::raw('count(*) as total_students'),
                    DB::raw('sum(case when students.nactvet_registration_number is null then 1 else 0 end) as missing_nactvet')
                );
            
            $this->applyFilters($breakdownQuery, $filters, 'students');
            
            $breakdownData = $breakdownQuery->groupBy('programs.name', 'programs.code')
                ->orderBy('programs.name')
                ->get()
                ->map(function($row) {
                    $row->compliance_rate = $row->total_students > 0 
                        ? (($row->total_students - $row->missing_nactvet) / $row->total_students) * 100 
                        : 0;
                    return $row;
                });

            return [
                'summary' => [
                    'total_students' => $total,
                    'missing_nactvet' => $missingNactvet,
                    'nactvet_compliance_rate' => $total > 0 ? (($total - $missingNactvet) / $total) * 100 : 0,
                ],
                'breakdown' => $breakdownData
            ];
        });
    }

    /**
     * Helper to apply filters to Student query
     */
    protected function applyFilters($query, $filters, $table = 'students')
    {
        if (!empty($filters['program_id'])) {
            $query->where($table.'.program_id', $filters['program_id']);
        }
        if (!empty($filters['nta_level'])) {
            $column = $table === 'semester_registrations' ? 'nta_level' : 'current_nta_level';
            $query->where($table.'.'.$column, $filters['nta_level']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereHas('program', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        
        // Date range if applicable to created_at
        if (!empty($filters['date_from'])) {
            $query->whereDate($table.'.created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate($table.'.created_at', '<=', $filters['date_to']);
        }

        // For SemesterRegistration, handle academic year / semester
        if ($table === 'semester_registrations') {
            if (!empty($filters['academic_year_id'])) {
                $query->where('academic_year_id', $filters['academic_year_id']);
            }
            if (!empty($filters['semester_id'])) {
                $query->where('semester_id', $filters['semester_id']);
            }
        }
    }

    /**
     * Helper to apply filters to ModuleResult query
     */
    protected function applyModuleResultFilters($query, $filters)
    {
        // Join relationships if necessary or use whereHas
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        if (!empty($filters['program_id'])) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('program_id', $filters['program_id']);
            });
        }
        if (!empty($filters['department_id'])) {
             $query->whereHas('moduleOffering.module.program', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
    }
}
