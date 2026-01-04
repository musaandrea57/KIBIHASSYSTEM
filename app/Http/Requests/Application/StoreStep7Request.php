<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep7Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'passport_photo' => 'nullable|file|image|max:2048',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'academic_certificate' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'nida_id' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ];
    }
}
