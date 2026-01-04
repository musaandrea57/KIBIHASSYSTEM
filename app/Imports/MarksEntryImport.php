<?php

namespace App\Imports;

use App\Models\ModuleOffering;
use App\Models\ModuleResult;
use App\Models\ContinuousAssessment;
use App\Models\Student;
use App\Models\AssessmentType;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MarksEntryImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $offeringId;
    protected $offering;
    protected $assessmentTypes;

    public function __construct($offeringId)
    {
        $this->offeringId = $offeringId;
        $this->offering = ModuleOffering::findOrFail($offeringId);
        $this->assessmentTypes = AssessmentType::whereIn('code', ['TEST1', 'TEST2', 'ASSIGN1', 'ASSIGN2'])
            ->pluck('id', 'code');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $regNo = $row['registration_number'];
            if (!$regNo) continue;
            
            $student = Student::where('registration_number', $regNo)->first();

            if (!$student) continue;

            // Find or create ModuleResult
            $result = ModuleResult::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'module_offering_id' => $this->offeringId,
                ],
                [
                    'academic_year_id' => $this->offering->academic_year_id,
                    'semester_id' => $this->offering->semester_id,
                    'status' => 'draft',
                    'uploaded_by' => Auth::id(),
                ]
            );

            // Update CA Marks
            // Headings are slugified by Maatwebsite: 'Test 1 (100)' -> 'test_1_100'
            $this->updateAssessment($result, 'TEST1', $row['test_1_100'] ?? null);
            $this->updateAssessment($result, 'TEST2', $row['test_2_100'] ?? null);
            $this->updateAssessment($result, 'ASSIGN1', $row['assignment_1_100'] ?? null);
            $this->updateAssessment($result, 'ASSIGN2', $row['assignment_2_100'] ?? null);

            // Update SE Mark
            if (isset($row['written_exam_100']) && is_numeric($row['written_exam_100'])) {
                $seRaw = floatval($row['written_exam_100']);
                $result->se_mark = round($seRaw * 0.6, 1);
            }
            
            $this->recalculateResult($result);
        }
    }

    private function updateAssessment($result, $typeCode, $mark)
    {
        if (!isset($mark) || !is_numeric($mark)) return;

        $typeId = $this->assessmentTypes[$typeCode] ?? null;
        if (!$typeId) return;

        ContinuousAssessment::updateOrCreate(
            [
                'module_result_id' => $result->id,
                'assessment_type_id' => $typeId,
            ],
            [
                'mark' => floatval($mark), 
                'max_mark' => 100,
                'recorded_at' => now(),
            ]
        );
    }

    private function recalculateResult($result)
    {
        // Fetch all assessments (refresh to get latest)
        $assessments = $result->continuousAssessments()->get();
        
        $t1 = $assessments->where('assessment_type_id', $this->assessmentTypes['TEST1'] ?? 0)->first()?->mark ?? 0;
        $t2 = $assessments->where('assessment_type_id', $this->assessmentTypes['TEST2'] ?? 0)->first()?->mark ?? 0;
        $a1 = $assessments->where('assessment_type_id', $this->assessmentTypes['ASSIGN1'] ?? 0)->first()?->mark ?? 0;
        $a2 = $assessments->where('assessment_type_id', $this->assessmentTypes['ASSIGN2'] ?? 0)->first()?->mark ?? 0;

        $avgTests = $this->avg($t1, $t2);
        $avgAssigns = $this->avg($a1, $a2);
        
        $caMark = ($avgTests * 0.2) + ($avgAssigns * 0.2); // 40% total
        
        $result->cw_mark = round($caMark, 1);
        
        if ($result->se_mark !== null) {
            $result->total_mark = $result->cw_mark + $result->se_mark;
        }
        
        $result->save();
    }
    
    private function avg($v1, $v2) {
        if ($v1 == 0 && $v2 == 0) return 0;
        return ($v1 + $v2) / 2;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|exists:students,registration_number',
            'test_1_100' => 'nullable|numeric|min:0|max:100',
            'test_2_100' => 'nullable|numeric|min:0|max:100',
            'assignment_1_100' => 'nullable|numeric|min:0|max:100',
            'assignment_2_100' => 'nullable|numeric|min:0|max:100',
            'written_exam_100' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
