<?php

namespace App\Http\Requests\Admin\School\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['user_id', 'school_course_id', 'school_course_schedule_id', 'payment_method_id'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('is_paid')) {
            $data['is_paid'] = filter_var(
                $this->input('is_paid'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        if ($this->filled('currency') && is_string($this->input('currency'))) {
            $data['currency'] = strtoupper($this->input('currency'));
        }

        foreach (['items', 'meta'] as $jsonField) {
            if ($this->filled($jsonField) && is_string($this->input($jsonField))) {
                $decoded = json_decode($this->input($jsonField), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$jsonField] = $decoded;
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $orderId = $this->route('school_order')?->id
            ?? $this->route('order')?->id
            ?? $this->route('schoolOrder')?->id
            ?? $this->input('id');

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'status' => [
                    'required',
                    'string',
                    Rule::in(['new', 'processing', 'cancelled', 'refunded', 'completed']),
                ],

                'is_paid' => ['required', 'boolean'],
                'payment_status' => [
                    'nullable',
                    'string',
                    Rule::in(['pending', 'paid', 'succeeded', 'failed', 'refunded', 'partial']),
                ],

                'paid_at' => ['nullable', 'date'],
                'manager_comment' => ['nullable', 'string'],
                'external_id' => ['nullable', 'string', 'max:128'],
            ];
        }

        return [
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],

            'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
            'school_course_schedule_id' => ['nullable', 'integer', Rule::exists('school_course_schedules', 'id')],

            'number' => [
                'required',
                'string',
                'max:32',
                Rule::unique('school_orders', 'number')->ignore($orderId),
            ],

            'buyer_name' => ['nullable', 'string', 'max:255'],
            'buyer_email' => ['nullable', 'string', 'email', 'max:255'],
            'buyer_phone' => ['nullable', 'string', 'max:64'],

            'billing_company' => ['nullable', 'string', 'max:255'],
            'billing_tax_id' => ['nullable', 'string', 'max:64'],
            'billing_address' => ['nullable', 'string'],

            'is_paid' => ['sometimes', 'boolean'],
            'paid_at' => ['nullable', 'date'],

            'payment_method_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'string', 'max:32'],
            'payment_provider' => ['nullable', 'string', 'max:32'],
            'payment_reference' => ['nullable', 'string', 'max:128'],
            'confirmation_code' => ['nullable', 'string', 'max:255'],
            'confirmation_status' => ['nullable', 'string', 'max:255'],
            'failure_reason' => ['nullable', 'string', 'max:255'],

            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_total' => ['required', 'numeric', 'min:0'],
            'tax_total' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],

            'status' => [
                'required',
                'string',
                Rule::in(['new', 'processing', 'cancelled', 'refunded', 'completed']),
            ],

            'payment_status' => [
                'required',
                'string',
                Rule::in(['pending', 'paid', 'succeeded', 'failed', 'refunded', 'partial']),
            ],

            'items' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],

            'user_comment' => ['nullable', 'string'],
            'manager_comment' => ['nullable', 'string'],

            'external_id' => ['nullable', 'string', 'max:128'],
            'exported_at' => ['nullable', 'date'],

            'client_ip' => ['nullable', 'string', 'max:45'],
            'user_agent' => ['nullable', 'string'],
            'public_hash' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'school_course_id.exists' => 'Указанный курс не найден.',
            'school_course_schedule_id.exists' => 'Указанный поток курса не найден.',

            'number.required' => 'Номер заказа обязателен.',
            'number.max' => 'Номер заказа не должен превышать :max символов.',
            'number.unique' => 'Такой номер заказа уже существует.',

            'buyer_email.email' => 'Email покупателя указан некорректно.',

            'is_paid.boolean' => 'Флаг оплаты должен быть булевым значением.',
            'paid_at.date' => 'Дата оплаты указана некорректно.',

            'currency.required' => 'Валюта обязательна.',
            'currency.size' => 'Валюта должна содержать ровно 3 символа.',
            'currency.regex' => 'Код валюты должен соответствовать ISO 4217, например USD.',

            'subtotal.required' => 'Сумма без скидок обязательна.',
            'subtotal.numeric' => 'Сумма без скидок должна быть числом.',
            'subtotal.min' => 'Сумма без скидок не может быть отрицательной.',

            'discount_total.required' => 'Сумма скидки обязательна.',
            'discount_total.numeric' => 'Сумма скидки должна быть числом.',
            'discount_total.min' => 'Сумма скидки не может быть отрицательной.',

            'tax_total.required' => 'Сумма налога обязательна.',
            'tax_total.numeric' => 'Сумма налога должна быть числом.',
            'tax_total.min' => 'Сумма налога не может быть отрицательной.',

            'total.required' => 'Итоговая сумма обязательна.',
            'total.numeric' => 'Итоговая сумма должна быть числом.',
            'total.min' => 'Итоговая сумма не может быть отрицательной.',

            'status.required' => 'Статус заказа обязателен.',
            'status.in' => 'Недопустимый статус заказа.',

            'payment_status.required' => 'Статус оплаты обязателен.',
            'payment_status.in' => 'Недопустимый статус оплаты.',

            'items.array' => 'Поле items должно быть массивом.',
            'meta.array' => 'Поле meta должно быть массивом.',

            'manager_comment.string' => 'Комментарий менеджера должен быть текстом.',
            'external_id.max' => 'Внешний ID не должен превышать :max символов.',

            'exported_at.date' => 'Дата выгрузки указана некорректно.',
            'client_ip.max' => 'IP клиента не должен превышать :max символов.',
            'public_hash.max' => 'Публичный хеш не должен превышать :max символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_course_id' => 'Курс',
            'school_course_schedule_id' => 'Поток курса',
            'number' => 'Номер заказа',
            'buyer_name' => 'Имя покупателя',
            'buyer_email' => 'Email покупателя',
            'buyer_phone' => 'Телефон покупателя',
            'billing_company' => 'Компания',
            'billing_tax_id' => 'ИИН/БИН',
            'billing_address' => 'Юридический адрес',
            'is_paid' => 'Флаг оплаты',
            'paid_at' => 'Дата оплаты',
            'payment_method' => 'Способ оплаты',
            'payment_provider' => 'Платёжный провайдер',
            'payment_reference' => 'ID транзакции',
            'currency' => 'Валюта',
            'subtotal' => 'Сумма без скидок',
            'discount_total' => 'Сумма скидки',
            'tax_total' => 'Сумма налога',
            'total' => 'Итоговая сумма',
            'status' => 'Статус заказа',
            'payment_status' => 'Статус оплаты',
            'items' => 'Позиции заказа',
            'meta' => 'Дополнительные данные',
            'user_comment' => 'Комментарий покупателя',
            'manager_comment' => 'Комментарий менеджера',
            'external_id' => 'Внешний ID',
            'exported_at' => 'Дата выгрузки',
            'client_ip' => 'IP клиента',
            'user_agent' => 'User Agent',
            'public_hash' => 'Публичный хеш',
        ];
    }
}
