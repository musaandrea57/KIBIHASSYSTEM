<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Semester;

class StudentPerformanceFilterDTO
{
    public ?int $academic_year_id;
    public ?int $semester_id;
    public ?int $program_id;
    public ?int $department_id;
    public ?int $nta_level;
    public ?int $intake_year;
    public ?int $year_of_study;
    public ?string $search;
    public ?string $date_from;
    public ?string $date_to;

    public function __construct(array $data)
    {
        $this->academic_year_id = $data['academic_year_id'] ?? null;
        $this->semester_id = $data['semester_id'] ?? null;
        $this->program_id = $data['program_id'] ?? null;
        $this->department_id = $data['department_id'] ?? null;
        $this->nta_level = $data['nta_level'] ?? null;
        $this->intake_year = $data['intake_year'] ?? null;
        $this->year_of_study = $data['year_of_study'] ?? null;
        $this->search = $data['search'] ?? null;
        $this->date_from = $data['date_from'] ?? null;
        $this->date_to = $data['date_to'] ?? null;

        $this->ensureDefaults();
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), function ($value) {
            return !is_null($value);
        });
    }

    private function ensureDefaults(): void
    {
        if (empty($this->academic_year_id)) {
            $currentYear = AcademicYear::where('is_active', true)->first();
            $this->academic_year_id = $currentYear ? $currentYear->id : AcademicYear::max('id');
        }

        if (empty($this->semester_id)) {
            $currentSemester = Semester::where('is_active', true)->first();
            $this->semester_id = $currentSemester ? $currentSemester->id : 1;
        }
    }
}
