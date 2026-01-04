<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ModuleAssignment;
use App\Models\ModuleOffering;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\CourseRegistration;
use App\Models\ModuleResult;
use App\Models\ContinuousAssessment;
use App\Models\AssessmentType;
use App\Models\Student;
use App\Services\ResultsService;
use App\Exports\MarksEntryExport;
use App\Imports\MarksEntryImport;
use Maatwebsite\Excel\Facades\Excel;

class MarksEntryController extends Controller
{
    protected $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function index()
    {
        $teacher = Auth::user();
        
        // Get current active academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            $currentYear = AcademicYear::latest()->first();
        }

        $offerings = collect();

        if ($currentYear) {
            $assignments = ModuleAssignment::where('user_id', $teacher->id)
                ->where('academic_year_id', $currentYear->id)
                ->with(['module', 'semester'])
                ->get();
                
            foreach ($assignments as $assignment) {
                $foundOfferings = ModuleOffering::where('module_id', $assignment->module_id)
                    ->where('academic_year_id', $assignment->academic_year_id)
                    ->where('semester_id', $assignment->semester_id)
                    ->with(['module', 'semester', 'academicYear']) // Eager load
                    ->get();
                    
                foreach ($foundOfferings as $offering) {
                    $enrolledCount = CourseRegistration::where('module_id', $offering->module_id)
                        ->where('academic_year_id', $offering->academic_year_id)
                        ->where('semester_id', $offering->semester_id)
                        ->count();
                        
                    $markedCount = ModuleResult::where('module_offering_id', $offering->id)
                        ->whereNotNull('total_mark')
                        ->count();
                        
                    $offerings->push((object)[
                        'id' => $offering->id,
                        'module_code' => $assignment->module->code,
                        'module_name' => $assignment->module->name,
                        'nta_level' => $offering->nta_level,
                        'enrolled_count' => $enrolledCount,
                        'marked_count' => $markedCount,
                        'status' => $offering->status,
                        'semester' => $assignment->semester->name,
                    ]);
                }
            }
        }

        return view('teacher.marks.index', compact('offerings', 'currentYear', 'teacher'));
    }

    public function show($offeringId)
    {
        $offering = ModuleOffering::with(['module', 'semester', 'academicYear'])->findOrFail($offeringId);
        
        $isAssigned = ModuleAssignment::where('user_id', Auth::id())
            ->where('module_id', $offering->module_id)
            ->where('academic_year_id', $offering->academic_year_id)
            ->where('semester_id', $offering->semester_id)
            ->exists();
            
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$isAssigned && !$user->hasRole('admin')) {
             abort(403, 'You are not assigned to this module.');
        }

        $isSemesterTwo = str_contains(strtolower($offering->semester->name), 'semester ii') || 
                         str_contains(strtolower($offering->semester->name), 'semester 2');

        $registrations = CourseRegistration::where('module_id', $offering->module_id)
            ->where('academic_year_id', $offering->academic_year_id)
            ->where('semester_id', $offering->semester_id)
            ->with('student.user')
            ->get()
            ->sortBy(function($reg) {
                return $reg->student->registration_number;
            });
            
        $results = ModuleResult::where('module_offering_id', $offering->id)
            ->with('continuousAssessments.assessmentType')
            ->get()
            ->keyBy('student_id');

        $assessmentTypeIds = AssessmentType::whereIn('code', ['TEST1', 'TEST2', 'ASSIGN1', 'ASSIGN2'])
            ->pluck('id', 'code');

        return view('teacher.marks.show', compact('offering', 'registrations', 'results', 'isSemesterTwo', 'assessmentTypeIds'));
    }
    
    public function store(Request $request, $offeringId)
    {
        $offering = ModuleOffering::findOrFail($offeringId);
        
        // Strict Semester II Check
        $isSemesterTwo = str_contains(strtolower($offering->semester->name), 'semester ii') || 
                         str_contains(strtolower($offering->semester->name), 'semester 2');
                         
        if ($isSemesterTwo) {
            return back()->with('error', 'Semester II results cannot be entered manually. They are issued via official NACTVET uploads.');
        }
        
        $request->validate([
            'marks' => 'array',
            'marks.*.test1' => 'nullable|numeric|min:0|max:100',
            'marks.*.test2' => 'nullable|numeric|min:0|max:100',
            'marks.*.assign1' => 'nullable|numeric|min:0|max:100',
            'marks.*.assign2' => 'nullable|numeric|min:0|max:100',
            'marks.*.se' => 'nullable|numeric|min:0|max:100', 
        ]);
        
        DB::transaction(function() use ($request, $offering) {
            foreach ($request->marks as $studentId => $markData) {
                $result = ModuleResult::firstOrCreate(
                    [
                        'student_id' => $studentId, 
                        'module_offering_id' => $offering->id
                    ],
                    [
                        'academic_year_id' => $offering->academic_year_id,
                        'semester_id' => $offering->semester_id,
                        'uploaded_by' => Auth::id(),
                    ]
                );
                
                // Save CA Components
                $t1 = $this->saveAssessment($result, 'TEST1', 'Test 1', $markData['test1'] ?? null);
                $t2 = $this->saveAssessment($result, 'TEST2', 'Test 2', $markData['test2'] ?? null);
                $a1 = $this->saveAssessment($result, 'ASSIGN1', 'Assignment 1', $markData['assign1'] ?? null);
                $a2 = $this->saveAssessment($result, 'ASSIGN2', 'Assignment 2', $markData['assign2'] ?? null);
                
                // Compute CW via Service
                $result->cw_mark = $this->resultsService->calculateCW($t1, $t2, $a1, $a2);
                
                // Save SE
                if (isset($markData['se']) && $markData['se'] !== '') {
                    $seRaw = floatval($markData['se']);
                    // Assume SE entered out of 100, scale to 60%
                    $result->se_mark = round($seRaw * 0.6, 1);
                }
                
                $result->save();

                // Final Compute via Service
                $this->resultsService->computeModuleResult($result);
            }
        });
        
        return back()->with('success', 'Marks saved and computed successfully.');
    }
    
    private function saveAssessment($result, $code, $name, $mark) {
        if ($mark === null || $mark === '') return null;
        
        $type = AssessmentType::firstOrCreate(['code' => $code], ['name' => $name, 'max_mark' => 100]);
        
        ContinuousAssessment::updateOrCreate(
            [
                'module_result_id' => $result->id,
                'assessment_type_id' => $type->id,
            ],
            [
                'mark' => $mark,
                'max_mark' => 100,
                'recorded_by' => Auth::id(),
                'recorded_at' => now(),
            ]
        );
        
        return floatval($mark);
    }

    public function export($offeringId)
    {
        return Excel::download(new MarksEntryExport($offeringId), 'marks_entry_template.xlsx');
    }

    public function import(Request $request, $offeringId)
    {
        $offering = ModuleOffering::findOrFail($offeringId);

        // Strict Semester II Check
        $isSemesterTwo = str_contains(strtolower($offering->semester->name), 'semester ii') || 
                         str_contains(strtolower($offering->semester->name), 'semester 2');
                         
        if ($isSemesterTwo) {
            return back()->with('error', 'Semester II results cannot be imported manually. They are issued via official NACTVET uploads.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new MarksEntryImport($offeringId), $request->file('file'));
            return back()->with('success', 'Marks imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             return back()->with('import_failures', $failures)->with('error', 'Import failed due to validation errors. Please check the error report below.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function submit(Request $request, $offeringId)
    {
        $offering = ModuleOffering::findOrFail($offeringId);
        
        // Strict Semester II Check
        $isSemesterTwo = str_contains(strtolower($offering->semester->name), 'semester ii') || 
                         str_contains(strtolower($offering->semester->name), 'semester 2');
                         
        if ($isSemesterTwo) {
            return back()->with('error', 'Semester II results cannot be submitted manually.');
        }

        $offering->status = 'pending_approval';
        $offering->save();

        return back()->with('success', 'Marks submitted for approval successfully.');
    }
}
