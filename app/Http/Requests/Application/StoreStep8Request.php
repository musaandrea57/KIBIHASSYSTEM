<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep8Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'declaration' => 'accepted',
        ];
    }
}
