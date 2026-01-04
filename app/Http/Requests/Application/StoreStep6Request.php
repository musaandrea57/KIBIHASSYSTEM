<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStep6Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nhif_card_number' => 'nullable|string',
            'has_disability' => 'boolean',
            'disability_details' => [
                'nullable',
                Rule::requiredIf($this->boolean('has_disability')),
                'string'
            ],
            'medical_conditions' => 'nullable|string',
            'emergency_contact.name' => 'required|string',
            'emergency_contact.relationship' => 'required|string',
            'emergency_contact.phone' => 'required|string',
            'emergency_contact.address' => 'required|string',
        ];
    }
}
