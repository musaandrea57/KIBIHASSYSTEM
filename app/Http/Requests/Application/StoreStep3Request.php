<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permanent_address' => 'required|string',
            'current_address' => 'required|string',
            'region' => 'required|string',
            'country' => 'required|string',
        ];
    }
}
