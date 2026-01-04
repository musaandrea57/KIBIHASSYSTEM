<?php

namespace App\Exports;

use App\Models\ModuleOffering;
use App\Models\CourseRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MarksEntryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $offeringId;

    public function __construct($offeringId)
    {
        $this->offeringId = $offeringId;
    }

    public function collection()
    {
        $offering = ModuleOffering::findOrFail($this->offeringId);
        
        return CourseRegistration::where('module_id', $offering->module_id)
            ->where('academic_year_id', $offering->academic_year_id)
            ->where('semester_id', $offering->semester_id)
            ->with('student')
            ->get()
            ->sortBy(function($reg) {
                return $reg->student->registration_number;
            });
    }

    public function map($registration): array
    {
        return [
            $registration->student->registration_number,
            $registration->student->first_name . ' ' . $registration->student->last_name,
            '', // Test 1
            '', // Test 2
            '', // Assign 1
            '', // Assign 2
            '', // Written Exam
        ];
    }

    public function headings(): array
    {
        return [
            'Registration Number',
            'Student Name',
            'Test 1 (100)',
            'Test 2 (100)',
            'Assignment 1 (100)',
            'Assignment 2 (100)',
            'Written Exam (100)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
