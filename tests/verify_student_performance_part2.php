<?php

use App\Services\StudentPerformanceService;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing StudentPerformanceService Part 2...\n";
    $service = new StudentPerformanceService();
    
    // Context
    $academicYear = AcademicYear::where('is_current', true)->first();
    $semester = Semester::where('is_active', true)->first();
    if (!$semester) $semester = Semester::latest('id')->first();
    
    $filters = [];
    if ($academicYear) $filters['academic_year_id'] = $academicYear->id;
    if ($semester) $filters['semester_id'] = $semester->id;
    
    echo "Filters: " . json_encode($filters) . "\n";
    
    echo "Testing getProgrammeSummary...\n";
    $progSummary = $service->getProgrammeSummary($filters);
    echo "getProgrammeSummary successful. Count: " . count($progSummary) . "\n";
    
    echo "Testing getCohortSummary...\n";
    $cohortSummary = $service->getCohortSummary($filters);
    echo "getCohortSummary successful. Count: " . count($cohortSummary) . "\n";
    
    echo "Testing getStudentList...\n";
    $studentList = $service->getStudentList($filters);
    echo "getStudentList successful. Count: " . $studentList->count() . "\n";
    
    // Find a student ID to test profile
    $studentId = 1; // Default fallback
    if ($studentList->count() > 0) {
        $studentId = $studentList->first()->id;
    }
    
    echo "Testing getStudentProfile for ID $studentId...\n";
    $profile = $service->getStudentProfile($studentId);
    echo "getStudentProfile successful.\n";
    
    echo "Testing getStudentResults for ID $studentId...\n";
    $results = $service->getStudentResults($studentId);
    echo "getStudentResults successful. Count: " . count($results) . "\n";
    
    echo "Verification Part 2 passed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
