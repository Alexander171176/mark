<?php

namespace App\Http\Requests\Admin\Finance\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите политики при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализуем code: нижний регистр + трим
        if ($this->filled('code') && is_string($this->input('code'))) {
            $merge['code'] = strtolower(trim($this->input('code')));
        }

        // meta может прийти как строка JSON
        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['meta'] = $decoded;
            }
        }

        if ($this->filled('sort')) $merge['sort'] = (int) $this->input('sort');

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        // Определяем ID при обновлении (Route Model Binding поддерживается)
        $pm = $this->route('payment_method');
        $id = is_object($pm) ? $pm->id : (int) $pm;

        return [
            'code' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:64',
                Rule::unique('payment_methods', 'code')->ignore($id),
                'regex:/^[a-z0-9._-]+$/', // безопасный машинный код
            ],
            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string', 'max:255',
            ],

            // Произвольный, но ограничим набор часто встречающихся значений
            'provider' => ['nullable','string','max:64'],
            'type'     => ['nullable','string','in:card,bank_transfer,ewallet,cash,invoice,other'],

            'supports_refund'    => ['sometimes','boolean'],
            'supports_recurring' => ['sometimes','boolean'],

            'activity' => ['sometimes','boolean'],
            'sort'  => ['sometimes','integer','min:0'],

            'meta' => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Укажите системный код способа оплаты.',
            'code.string'   => 'Код должен быть строкой.',
            'code.max'      => 'Код слишком длинный (максимум 64 символа).',
            'code.unique'   => 'Способ оплаты с таким кодом уже существует.',
            'code.regex'    => 'Код может содержать только латинские буквы, цифры, точки, дефисы и подчёркивания.',

            'name.required' => 'Укажите название способа оплаты.',
            'name.string'   => 'Название должно быть строкой.',
            'name.max'      => 'Название слишком длинное.',

            'provider.string'=> 'Поле provider должно быть строкой.',
            'provider.max'   => 'Значение provider слишком длинное.',

            'type.in'       => 'type должен быть одним из: card, bank_transfer, ewallet, cash, invoice, other.',

            'supports_refund.boolean'    => 'Поле supports_refund должно быть булевым.',
            'supports_recurring.boolean' => 'Поле supports_recurring должно быть булевым.',
            'activity.boolean'          => 'Поле activity должно быть булевым.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min'     => 'Позиция не может быть отрицательной.',

            'meta.array'       => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'                => 'код способа оплаты',
            'name'                => 'название',
            'provider'            => 'провайдер',
            'type'                => 'тип',
            'supports_refund'     => 'поддержка возвратов',
            'supports_recurring'  => 'поддержка рекуррентных списаний',
            'activity'            => 'активность',
            'sort'                => 'позиция',
            'meta'                => 'метаданные',
        ];
    }
}
