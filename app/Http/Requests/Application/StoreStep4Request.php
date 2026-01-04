<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep4Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'highest_education' => 'required|string',
            'secondary_school' => 'required|string',
            'exam_body' => 'required|string',
            'index_number' => 'required|string',
            'completion_year' => 'required|integer|digits:4',
            'previous_institution' => 'nullable|string',
        ];
    }
}
