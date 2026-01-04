<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep5Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'intake' => 'required|string',
            'study_mode' => 'required|string',
            'sponsorship' => 'required|string',
        ];
    }
}
