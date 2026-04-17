<?php

namespace App\Http\Requests\Admin\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('activity')) {
            $this->merge([
                'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'activity' => ['required', 'boolean'],
        ];
    }
}
