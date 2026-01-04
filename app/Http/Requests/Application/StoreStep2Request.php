<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStep2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender' => 'required|in:Male,Female',
            'dob' => 'required|date|before:today',
            'nationality' => 'required|string',
            'nin' => [
                'nullable',
                Rule::requiredIf($this->nationality === 'Tanzanian'),
                'string'
            ],
            'passport_number' => [
                'nullable',
                Rule::requiredIf($this->nationality === 'Foreign'),
                'string'
            ],
            'marital_status' => 'required|string',
            'passport_photo' => 'nullable|image|max:2048', // Optional here if mainly in Step 7
        ];
    }
}
