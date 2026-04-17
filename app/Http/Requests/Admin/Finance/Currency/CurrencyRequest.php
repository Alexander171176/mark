<?php

namespace App\Http\Requests\Admin\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('sort')) {
            $merge['sort'] = (int) $this->input('sort');
        }

        if ($this->has('code')) {
            $merge['code'] = strtoupper(trim((string) $this->input('code')));
        }

        if ($this->has('name')) {
            $merge['name'] = trim((string) $this->input('name'));
        }

        if ($this->has('symbol')) {
            $symbol = $this->input('symbol');
            $merge['symbol'] = is_string($symbol) ? trim($symbol) : null;
            if ($merge['symbol'] === '') $merge['symbol'] = null;
        }

        if ($this->has('precision')) {
            $merge['precision'] = (int) $this->input('precision');
        }

        if ($this->has('symbol_first')) {
            $merge['symbol_first'] = filter_var(
                $this->input('symbol_first'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        // токены, не символы — оставляем как есть до passedValidation()
        if ($this->has('thousands_sep')) {
            $merge['thousands_sep'] = (string) $this->input('thousands_sep');
        }

        if ($this->has('decimal_sep')) {
            $merge['decimal_sep'] = (string) $this->input('decimal_sep');
        }

        if ($this->has('activity')) {
            $merge['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $currencyId = $this->route('currency')?->id ?? $this->input('id');

        return [
            'sort' => ['sometimes','integer','min:0'],

            'code' => [
                'sometimes','required','string','size:3','regex:/^[A-Z]{3}$/',
                Rule::unique('currencies', 'code')->ignore($currencyId),
            ],

            'name'         => ['sometimes','required','string','max:64'],
            'symbol'       => ['nullable','string','max:8'],
            'precision'    => ['sometimes','required','integer','between:0,6'],
            'symbol_first' => ['sometimes','required','boolean'],

            'thousands_sep' => ['sometimes','required','string','in:space,nbsp,thinspace,comma,dot,apostrophe','different:decimal_sep'],
            'decimal_sep'   => ['sometimes','required','string','in:dot,comma','different:thousands_sep'],

            'activity' => ['sometimes','required','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex'              => 'Код должен быть в формате ISO-4217 (три заглавные буквы).',
            'thousands_sep.in'        => 'Недопустимый разделитель тысяч.',
            'decimal_sep.in'          => 'Недопустимый десятичный разделитель.',
            'thousands_sep.different' => 'Разделитель тысяч не может совпадать с десятичным.',
            'decimal_sep.different'   => 'Десятичный разделитель не может совпадать с разделителем тысяч.',
        ];
    }

    protected function passedValidation(): void
    {
        $map = [
            'space'      => ' ',
            'nbsp'       => "\u{00A0}",
            'thinspace'  => "\u{2009}",
            'comma'      => ',',
            'dot'        => '.',
            'apostrophe' => "'",
        ];

        $merge = [];

        if ($this->has('thousands_sep')) {
            $thToken = (string) $this->input('thousands_sep');
            $merge['thousands_sep'] = $map[$thToken] ?? $thToken;
        }

        if ($this->has('decimal_sep')) {
            $decToken = (string) $this->input('decimal_sep');
            $merge['decimal_sep'] = $map[$decToken] ?? $decToken;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
