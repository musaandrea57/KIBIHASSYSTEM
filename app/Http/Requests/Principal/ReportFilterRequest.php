<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('principal'); // Ensure only principal can use this
    }

    public function rules()
    {
        return [
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'program_id' => 'nullable|exists:programs,id',
            'department_id' => 'nullable|exists:departments,id',
            'nta_level' => 'nullable|integer|min:4|max:9',
            'intake' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|string',
            'format' => 'nullable|in:pdf,excel,csv',
            'report' => 'nullable|string',
        ];
    }
}
