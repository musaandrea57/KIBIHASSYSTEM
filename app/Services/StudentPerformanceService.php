<?php

namespace App\Services;

use App\DTOs\StudentPerformanceFilterDTO;
use App\Models\Student;
use App\Models\SemesterRegistration;
use App\Models\ModuleResult;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class StudentPerformanceService
{
    protected $cacheTtl = 900; // 15 minutes

    /**
     * Get Executive Overview KPIs
     * @param array $filters
     * @return array
     */
    public function getKpis(array $filters): array
    {
        // Use DTO for structure (logic handles array for compatibility)
        $key = 'perf_kpis_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Base query for students in the filtered context
            $studentsQuery = Student::query();
            $this->applyStudentFilters($studentsQuery, $filters);
            $totalStudents = $studentsQuery->count();

            // Results query for the selected period
            $resultsQuery = ModuleResult::query();
            $this->applyResultFilters($resultsQuery, $filters);
            
            // Aggregates
            $totalResults = $resultsQuery->count();
            
            // Pass/Fail (assuming 40 is pass mark - should be config driven ideally)
            $passedResults = (clone $resultsQuery)->where('total_mark', '>=', 40)->count();
            $failedResults = (clone $resultsQuery)->where('total_mark', '<', 40)->count();
            
            // GPA Calculation (Weighted: SUM(GP * Credits) / SUM(Credits))
            $avgGpa = $this->calculateWeightedGpa($resultsQuery);
            
            // Pass/Fail Rates
            $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : null;
            $failRate = $totalResults > 0 ? ($failedResults / $totalResults) * 100 : null;
            
            // Published Results Coverage
            // Count distinct students with at least one published result vs total active students
            // Note: applyResultFilters ensures we only look at published results
            $studentsWithResults = (clone $resultsQuery)
                ->distinct('student_id')
                ->count('student_id');
            
            // Data Quality Metrics
            // A. Missing NACTVET
            $missingNactvet = (clone $studentsQuery)->whereNull('nactvet_registration_number')->count();

            // B. Missing Registration (for selected period)
            $missingRegistration = 0;
            $registeredCount = 0;
            if (!empty($filters['academic_year_id']) && !empty($filters['semester_id'])) {
                $registeredQuery = (clone $studentsQuery)->whereHas('semesterRegistrations', function($q) use ($filters) {
                    $q->where('academic_year_id', $filters['academic_year_id'])
                      ->where('semester_id', $filters['semester_id']);
                });
                $registeredCount = $registeredQuery->count();
                $missingRegistration = $totalStudents - $registeredCount;
            } else {
                $registeredCount = $totalStudents;
            }

            // C. Missing Published Results (Registered but no results)
            $missingResults = $registeredCount - $studentsWithResults;
            if ($missingResults < 0) $missingResults = 0;

            $publishedCoverage = $registeredCount > 0 ? ($studentsWithResults / $registeredCount) * 100 : 0;

            // Carry/Incomplete (Simplified: Failed results are potential carries)
            $carryCount = $failedResults; 
            
            return [
                'total_students' => $totalStudents,
                'avg_gpa' => $avgGpa !== null ? round($avgGpa, 2) : null,
                'pass_rate' => $passRate !== null ? round($passRate, 1) : null,
                'fail_rate' => $failRate !== null ? round($failRate, 1) : null,
                'carry_count' => $carryCount,
                'published_coverage' => round($publishedCoverage, 1),
                'data_quality' => [
                    'missing_nactvet' => $missingNactvet,
                    'missing_registration' => $missingRegistration,
                    'missing_results' => $missingResults,
                ]
            ];
        });
    }

    /**
     * Get Distributions (Grades & GPA)
     * @param array $filters
     * @return array
     */
    public function getDistributions(array $filters): array
    {
        $key = 'perf_dist_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            // Grade Distribution
            $resultsQuery = ModuleResult::query();
            $this->applyResultFilters($resultsQuery, $filters);
            
            $gradeDist = (clone $resultsQuery)
                ->select('grade', DB::raw('count(*) as count'))
                ->whereNotNull('grade')
                ->groupBy('grade')
                ->orderBy('grade')
                ->pluck('count', 'grade')
                ->toArray();

            // GPA Band Distribution
            // Calculate Weighted GPA per student
            // Query: SELECT student_id, SUM(gp*credits)/SUM(credits) as gpa FROM ... GROUP BY student_id
            $studentGpas = (clone $resultsQuery)
                ->select('student_id', DB::raw('SUM(grade_point * credits_snapshot) / SUM(credits_snapshot) as gpa'))
                ->whereNotNull('grade_point')
                ->where('credits_snapshot', '>', 0)
                ->groupBy('student_id')
                ->get();

            $gpaBands = Config::get('performance.gpa_bands', [
                'First Class (4.4-5.0)' => [4.4, 5.0],
                'Upper Second (3.5-4.3)' => [3.5, 4.3],
                'Lower Second (2.7-3.4)' => [2.7, 3.4],
                'Pass (2.0-2.6)' => [2.0, 2.6],
                'Fail (0.0-1.9)' => [0.0, 1.9],
            ]);
            
            $gpaDist = array_fill_keys(array_keys($gpaBands), 0);

            foreach ($studentGpas as $record) {
                $gpa = $record->gpa;
                foreach ($gpaBands as $band => $range) {
                    if ($gpa >= $range[0] && $gpa <= $range[1]) {
                        $gpaDist[$band]++;
                        break;
                    }
                }
            }

            return [
                'grades' => $gradeDist,
                'gpa_bands' => $gpaDist,
            ];
        });
    }

    /**
     * Get Trends (Last 4 Semesters)
     * @param array $filters
     * @return array
     */
    public function getTrends(array $filters): array
    {
        $key = 'perf_trends_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            $endYearId = $filters['academic_year_id'] ?? AcademicYear::max('id');
            $endSemesterId = $filters['semester_id'] ?? Semester::max('id');

            // Iterate back to find last 4 periods
            $years = AcademicYear::where('id', '<=', $endYearId)
                ->orderBy('id', 'desc')
                ->take(3) 
                ->get();
            
            $semesters = Semester::all();
            
            $periods = [];
            foreach ($years as $year) {
                foreach ($semesters as $sem) {
                    $periods[] = ['year' => $year, 'semester' => $sem];
                }
            }
            
            usort($periods, function($a, $b) {
                if ($a['year']->id == $b['year']->id) {
                    return $a['semester']->id <=> $b['semester']->id;
                }
                return $a['year']->id <=> $b['year']->id;
            });

            $filteredPeriods = [];
            foreach ($periods as $p) {
                if ($p['year']->id < $endYearId || ($p['year']->id == $endYearId && $p['semester']->id <= $endSemesterId)) {
                    $filteredPeriods[] = $p;
                }
            }
            
            $targetPeriods = array_slice($filteredPeriods, -4);
            
            $trends = [
                'labels' => [],
                'pass_rates' => [],
                'avg_gpas' => [],
            ];

            foreach ($targetPeriods as $period) {
                $periodLabel = $period['year']->name . ' ' . $period['semester']->name;
                
                $periodFilters = $filters;
                $periodFilters['academic_year_id'] = $period['year']->id;
                $periodFilters['semester_id'] = $period['semester']->id;
                
                $resultsQuery = ModuleResult::query();
                $this->applyResultFilters($resultsQuery, $periodFilters);
                
                $totalResults = $resultsQuery->count();
                $passedResults = (clone $resultsQuery)->where('total_mark', '>=', 40)->count();
                $avgGpa = $this->calculateWeightedGpa($resultsQuery) ?? 0;
                $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : 0;
                
                $trends['labels'][] = $periodLabel;
                $trends['pass_rates'][] = round($passRate, 1);
                $trends['avg_gpas'][] = round($avgGpa, 2);
            }
            
            return $trends;
        });
    }

    /**
     * Get Programme Performance Summary
     * @param array $filters
     * @return array
     */
    public function getProgrammeSummary(array $filters): array
    {
        $key = 'perf_prog_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            $programs = Program::with('department')->get();
            $summary = [];

            foreach ($programs as $program) {
                $progFilters = array_merge($filters, ['program_id' => $program->id]);
                
                $studentsQuery = Student::where('program_id', $program->id);
                if (!empty($filters['academic_year_id'])) {
                    $studentsQuery->whereHas('semesterRegistrations', function($q) use ($filters) {
                        $q->where('academic_year_id', $filters['academic_year_id']);
                    });
                }
                $studentCount = $studentsQuery->count();
                
                if ($studentCount == 0) continue;

                $resultsQuery = ModuleResult::query();
                $this->applyResultFilters($resultsQuery, $progFilters);
                
                $totalResults = $resultsQuery->count();
                $passedResults = (clone $resultsQuery)->where('total_mark', '>=', 40)->count();
                $avgGpa = $this->calculateWeightedGpa($resultsQuery) ?? 0;
                
                $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : 0;

                // Strongest/Weakest Modules
                $moduleStats = (clone $resultsQuery)
                    ->select('module_offering_id', 
                        DB::raw('count(*) as total'), 
                        DB::raw('sum(case when total_mark >= 40 then 1 else 0 end) as passed'))
                    ->groupBy('module_offering_id')
                    ->with('moduleOffering.module')
                    ->get();
                
                $strongest = $moduleStats->sortByDesc(function($s) { return $s->total > 0 ? $s->passed/$s->total : 0; })->first();
                $weakest = $moduleStats->sortBy(function($s) { return $s->total > 0 ? $s->passed/$s->total : 0; })->first();

                $summary[] = [
                    'program_id' => $program->id,
                    'program_name' => $program->name,
                    'program_code' => $program->code,
                    'student_count' => $studentCount,
                    'avg_gpa' => round($avgGpa, 2),
                    'pass_rate' => round($passRate, 1),
                    'strongest_module' => $strongest ? $strongest->moduleOffering->module->code : '-',
                    'weakest_module' => $weakest ? $weakest->moduleOffering->module->code : '-',
                ];
            }

            return $summary;
        });
    }

    /**
     * Get Cohort Performance Summary
     * @param array $filters
     * @return array
     */
    public function getCohortSummary(array $filters): array
    {
        $key = 'perf_cohort_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            $cohorts = Student::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
                
            $summary = [];

            foreach ($cohorts as $year) {
                $cohortFilters = $filters;
                $cohortFilters['intake_year'] = $year;
                
                $studentsQuery = Student::query();
                $this->applyStudentFilters($studentsQuery, $cohortFilters);
                $totalStudents = $studentsQuery->count();
                
                if ($totalStudents == 0) continue;
                
                $activeStudents = (clone $studentsQuery)->where('status', 'active')->count();
                $retentionRate = ($activeStudents / $totalStudents) * 100;
                
                $resultsQuery = ModuleResult::query();
                $this->applyResultFilters($resultsQuery, $cohortFilters);
                
                $totalResults = $resultsQuery->count();
                $passedResults = (clone $resultsQuery)->where('total_mark', '>=', 40)->count();
                $avgGpa = $this->calculateWeightedGpa($resultsQuery);
                $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : null;
                
                $summary[] = [
                    'cohort_year' => $year,
                    'total_students' => $totalStudents,
                    'active_students' => $activeStudents,
                    'retention_rate' => round($retentionRate, 1),
                    'avg_gpa' => $avgGpa !== null ? round($avgGpa, 2) : null,
                    'pass_rate' => $passRate !== null ? round($passRate, 1) : null,
                ];
            }
            
            return $summary;
        });
    }

    /**
     * Get At-Risk Students
     * @param array $filters
     * @return array
     */
    public function getAtRiskStudents(array $filters): array
    {
        $key = 'perf_risk_' . md5(serialize($filters));

        return Cache::remember($key, $this->cacheTtl, function () use ($filters) {
            $thresholds = Config::get('performance.at_risk', [
                'gpa_warning_threshold' => 2.0,
                'fails_warning_threshold' => 2,
            ]);
            
            $students = [];

            // 1. Base Student Query
            $baseStudentQuery = Student::query();
            $this->applyStudentFilters($baseStudentQuery, $filters);
            
            // 2. Result Query
            $baseResultsQuery = ModuleResult::query();
            $this->applyResultFilters($baseResultsQuery, $filters);

            // 3. Find IDs
            
            // Low GPA (Weighted)
            // HAVING clause with complex calculation
            $lowGpaStudentIds = (clone $baseResultsQuery)
                ->select('student_id', DB::raw('SUM(grade_point * credits_snapshot) / SUM(credits_snapshot) as gpa'))
                ->where('credits_snapshot', '>', 0)
                ->groupBy('student_id')
                ->havingRaw('gpa < ?', [$thresholds['gpa_warning_threshold']])
                ->pluck('student_id')
                ->toArray();

            // Multiple Fails
            $failedStudentIds = (clone $baseResultsQuery)
                ->select('student_id', DB::raw('count(*) as fails'))
                ->where('total_mark', '<', 40)
                ->groupBy('student_id')
                ->having('fails', '>=', $thresholds['fails_warning_threshold'])
                ->pluck('student_id')
                ->toArray();
            
            // Fee Balance
            $feeBalanceIds = (clone $baseStudentQuery)
                ->whereHas('invoices', function($q) {
                    $q->where('balance', '>', 0);
                })
                ->pluck('id')
                ->toArray();

            // Missing Results
            $missingResultsIds = [];
            if (!empty($filters['academic_year_id']) && !empty($filters['semester_id'])) {
                 $registeredStudentIds = SemesterRegistration::where('academic_year_id', $filters['academic_year_id'])
                    ->where('semester_id', $filters['semester_id'])
                    ->whereIn('student_id', (clone $baseStudentQuery)->pluck('id'))
                    ->pluck('student_id')
                    ->toArray();
                 
                 $studentsWithResultsIds = (clone $baseResultsQuery)
                    ->distinct('student_id')
                    ->pluck('student_id')
                    ->toArray();
                 
                 $missingResultsIds = array_diff($registeredStudentIds, $studentsWithResultsIds);
            }

            $atRiskIds = array_unique(array_merge($lowGpaStudentIds, $failedStudentIds, $missingResultsIds, $feeBalanceIds));

            if (empty($atRiskIds)) return [];

            $atRiskStudents = Student::whereIn('id', $atRiskIds)
                ->with(['program', 'user'])
                ->get();

            foreach ($atRiskStudents as $student) {
                // Calculate precise metrics for this student
                $studentResults = (clone $baseResultsQuery)->where('student_id', $student->id)->get();
                
                // Calculate weighted GPA
                $totalPoints = 0;
                $totalCredits = 0;
                foreach($studentResults as $res) {
                    if ($res->grade_point !== null && $res->credits_snapshot > 0) {
                        $totalPoints += $res->grade_point * $res->credits_snapshot;
                        $totalCredits += $res->credits_snapshot;
                    }
                }
                $gpa = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;
                
                $fails = $studentResults->where('total_mark', '<', 40)->count();
                
                $reasons = [];
                if ($gpa < $thresholds['gpa_warning_threshold']) $reasons[] = 'Low GPA';
                if ($fails >= $thresholds['fails_warning_threshold']) $reasons[] = 'Multiple Fails';
                
                if (in_array($student->id, $missingResultsIds)) {
                    $reasons[] = 'Missing Results';
                }
                
                if ($student->balance > 0) {
                    $reasons[] = 'Fee Balance';
                }

                if (empty($reasons)) continue;

                $students[] = [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'registration_number' => $student->registration_number,
                    'program_code' => $student->program->code,
                    'nta_level' => $student->current_nta_level,
                    'gpa' => round($gpa, 2),
                    'fails' => $fails,
                    'reasons' => $reasons,
                ];
            }

            return $students;
        });
    }
    
    public function getStudentList(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        $studentsQuery = Student::with(['program', 'user']);
        $this->applyStudentFilters($studentsQuery, $filters);
        
        $students = $studentsQuery->paginate(Config::get('performance.pagination.per_page', 20));
        
        $students->getCollection()->transform(function($student) use ($filters) {
             $resultsQuery = ModuleResult::where('student_id', $student->id);
             $this->applyResultFilters($resultsQuery, $filters);
             $results = $resultsQuery->get();
             
             // Weighted GPA
             $totalPoints = 0;
             $totalCredits = 0;
             foreach($results as $res) {
                 if ($res->grade_point !== null && $res->credits_snapshot > 0) {
                     $totalPoints += $res->grade_point * $res->credits_snapshot;
                     $totalCredits += $res->credits_snapshot;
                 }
             }
             $student->period_gpa = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;
             
             $student->results_count = $results->count();
             $student->passed_count = $results->where('total_mark', '>=', 40)->count();
             $student->failed_count = $results->where('total_mark', '<', 40)->count();
             
             return $student;
        });
        
        return $students;
    }

    public function getStudentProfile($studentId): Student
    {
         return Student::with(['program', 'user', 'semesterRegistrations.academicYear', 'semesterRegistrations.semester'])
            ->findOrFail($studentId);
    }
    
    public function getStudentResults($studentId): \Illuminate\Support\Collection
    {
        // Only published results
        return ModuleResult::where('student_id', $studentId)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['moduleOffering.module', 'academicYear', 'semester'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->academicYear->name . ' - ' . $item->semester->name;
            });
    }

    // --- Helpers ---

    protected function calculateWeightedGpa($query)
    {
        // Weighted Average: SUM(GP * Credits) / SUM(Credits)
        $data = (clone $query)
            ->whereNotNull('grade_point')
            ->select(DB::raw('SUM(grade_point * credits_snapshot) as total_points'), DB::raw('SUM(credits_snapshot) as total_credits'))
            ->first();
            
        if (!$data || $data->total_credits <= 0) {
            return null;
        }
        
        return $data->total_points / $data->total_credits;
    }

    protected function applyStudentFilters($query, $filters)
    {
        if (!empty($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }
        if (!empty($filters['nta_level'])) {
            $query->where('current_nta_level', $filters['nta_level']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereHas('program', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if (!empty($filters['intake_year'])) {
             $query->whereYear('created_at', $filters['intake_year']);
        }
        if (!empty($filters['year_of_study'])) {
            $academicYearId = $filters['academic_year_id'] ?? AcademicYear::max('id');
            $academicYear = AcademicYear::find($academicYearId);
            
            if ($academicYear) {
                $currentYear = Carbon::parse($academicYear->start_date)->year;
                $targetIntakeYear = $currentYear - $filters['year_of_study'] + 1;
                $query->whereYear('created_at', $targetIntakeYear);
            }
        }
    }

    protected function applyResultFilters($query, $filters)
    {
        // STRICT RULE: Only Published Results
        $query->where('status', 'published')
              ->whereNotNull('published_at');

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        
        $query->whereHas('student', function($q) use ($filters) {
            $this->applyStudentFilters($q, $filters);
        });

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }
}
