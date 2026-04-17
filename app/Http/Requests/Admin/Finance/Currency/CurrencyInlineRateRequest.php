<?php

namespace App\Http\Requests\Admin\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyInlineRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // rate: строкой, поддержка запятой
        if ($this->has('rate')) {
            $raw = trim((string) $this->input('rate'));
            $merge['rate'] = $raw === '' ? null : str_replace(',', '.', $raw);
        }

        if ($this->has('provider')) {
            $provider = trim((string) $this->input('provider'));
            $merge['provider'] = $provider !== '' ? $provider : null;
        }

        // история: всегда фиксируем момент
        $merge['fetched_at'] = now()->toDateTimeString();

        $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'rate'      => ['required', 'numeric', 'gt:0', 'decimal:0,18'],
            'provider'  => ['nullable', 'string', 'max:64'],
            'fetched_at'=> ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'rate.required' => 'Введите курс.',
            'rate.numeric'  => 'Курс должен быть числом.',
            'rate.gt'       => 'Курс должен быть больше нуля.',
            'rate.decimal'  => 'Слишком много знаков после запятой у курса.',
        ];
    }
}
