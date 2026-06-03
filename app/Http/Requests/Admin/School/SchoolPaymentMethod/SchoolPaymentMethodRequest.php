<?php

namespace App\Http\Requests\Admin\School\SchoolPaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolPaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->filled('code') && is_string($this->input('code'))) {
            $data['code'] = strtolower(trim($this->input('code')));
        }

        foreach (['supports_refund', 'supports_recurring', 'activity'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if ($this->filled('sort')) {
            $data['sort'] = (int) $this->input('sort');
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $paymentMethodId = $this->route('school_payment_method')?->id
            ?? $this->route('paymentMethod')?->id
            ?? $this->route('schoolPaymentMethod')?->id
            ?? $this->input('id');

        return [
            'code' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:64',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('school_payment_methods', 'code')->ignore($paymentMethodId),
            ],

            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'provider' => ['nullable', 'string', 'max:64'],
            'type' => ['nullable', 'string', 'max:32', Rule::in([
                'card',
                'bank_transfer',
                'ewallet',
                'cash',
                'invoice',
                'other',
            ])],

            'supports_refund' => ['sometimes', 'boolean'],
            'supports_recurring' => ['sometimes', 'boolean'],

            'activity' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'integer', 'min:0'],

            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Укажите системный код способа оплаты.',
            'code.max' => 'Код не должен превышать 64 символа.',
            'code.unique' => 'Способ оплаты с таким кодом уже существует.',
            'code.regex' => 'Код может содержать только латинские буквы, цифры, точки, дефисы и подчёркивания.',

            'name.required' => 'Укажите название способа оплаты.',
            'name.max' => 'Название не должно превышать 255 символов.',

            'provider.max' => 'Провайдер не должен превышать 64 символа.',
            'type.in' => 'Тип оплаты должен быть одним из: card, bank_transfer, ewallet, cash, invoice, other.',

            'supports_refund.boolean' => 'Поле поддержки возвратов должно быть булевым.',
            'supports_recurring.boolean' => 'Поле поддержки рекуррентных платежей должно быть булевым.',
            'activity.boolean' => 'Поле активности должно быть булевым.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'meta.array' => 'Поле meta должно быть JSON-объектом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Код',
            'name' => 'Название',
            'provider' => 'Провайдер',
            'type' => 'Тип оплаты',
            'supports_refund' => 'Поддержка возвратов',
            'supports_recurring' => 'Поддержка рекуррентных платежей',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'meta' => 'Meta',
        ];
    }
}
