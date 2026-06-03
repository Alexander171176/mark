<?php

namespace App\Http\Requests\Admin\School\SchoolPayment;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolUserPaymentMethod\SchoolUserPaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['provider', 'provider_payment_id', 'idempotency_key', 'currency', 'error_code'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if ($this->filled('currency')) {
            $data['currency'] = strtoupper($this->input('currency'));
        }

        foreach (['school_order_id', 'school_payment_method_id', 'school_user_payment_method_id'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['amount', 'refunded_amount'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = is_string($this->input($field))
                    ? str_replace(',', '.', trim($this->input($field)))
                    : $this->input($field);
            }
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
        $payment = $this->route('school_payment')
            ?? $this->route('payment')
            ?? $this->route('schoolPayment');

        $id = is_object($payment)
            ? $payment->id
            : ($payment ? (int) $payment : null);

        $statuses = [
            'pending',
            'processing',
            'succeeded',
            'failed',
            'canceled',
            'refunded',
            'partially_refunded',
        ];

        return [
            'school_order_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_orders', 'id'),
            ],

            'school_payment_method_id' => [
                'nullable',
                'integer',
                Rule::exists('school_payment_methods', 'id'),
            ],

            'school_user_payment_method_id' => [
                'nullable',
                'integer',
                Rule::exists('school_user_payment_methods', 'id'),
            ],

            'provider' => ['nullable', 'string', 'max:64'],

            'provider_payment_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_payments', 'provider_payment_id')
                    ->where(fn ($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'idempotency_key' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_payments', 'idempotency_key')->ignore($id),
            ],

            'status' => ['sometimes', 'string', Rule::in($statuses)],

            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'amount' => [$this->isMethod('post') ? 'required' : 'sometimes', 'numeric', 'min:0'],

            'captured_at' => ['nullable', 'date'],
            'refunded_at' => ['nullable', 'date'],
            'refunded_amount' => ['nullable', 'numeric', 'min:0'],

            'error_code' => ['nullable', 'string', 'max:64'],
            'error_message' => ['nullable', 'string'],

            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = $this->input('status');

            if (
                $this->filled('refunded_amount')
                && !in_array($status, ['refunded', 'partially_refunded'], true)
            ) {
                $validator->errors()->add(
                    'refunded_amount',
                    'При наличии суммы возврата статус должен быть refunded или partially_refunded.'
                );
            }

            if ($this->filled('refunded_amount') && $this->filled('amount')) {
                if ((float) $this->input('refunded_amount') > (float) $this->input('amount')) {
                    $validator->errors()->add(
                        'refunded_amount',
                        'Сумма возврата не может превышать сумму платежа.'
                    );
                }
            }

            if ($this->filled('school_user_payment_method_id') && $this->filled('school_order_id')) {
                $order = SchoolOrder::query()->find($this->input('school_order_id'));
                $userPaymentMethod = SchoolUserPaymentMethod::query()
                    ->find($this->input('school_user_payment_method_id'));

                if (
                    $order
                    && $userPaymentMethod
                    && $order->user_id
                    && $userPaymentMethod->user_id
                    && (int) $order->user_id !== (int) $userPaymentMethod->user_id
                ) {
                    $validator->errors()->add(
                        'school_user_payment_method_id',
                        'Указанный сохранённый способ оплаты не принадлежит владельцу заказа.'
                    );
                }
            }

            if ($status === 'succeeded' && !$this->filled('captured_at')) {
                $validator->errors()->add(
                    'captured_at',
                    'Для статуса succeeded рекомендуется указать время подтверждения платежа.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_order_id.required' => 'Не указан заказ.',
            'school_order_id.integer' => 'Идентификатор заказа должен быть числом.',
            'school_order_id.exists' => 'Заказ не найден.',

            'school_payment_method_id.integer' => 'Идентификатор способа оплаты должен быть числом.',
            'school_payment_method_id.exists' => 'Справочник способа оплаты не найден.',

            'school_user_payment_method_id.integer' => 'Идентификатор сохранённого способа должен быть числом.',
            'school_user_payment_method_id.exists' => 'Сохранённый способ оплаты не найден.',

            'provider.max' => 'Поле provider не должно превышать 64 символа.',

            'provider_payment_id.max' => 'ID платежа у провайдера слишком длинный.',
            'provider_payment_id.unique' => 'Платёж с таким provider и provider_payment_id уже зарегистрирован.',

            'idempotency_key.max' => 'Идемпотентный ключ слишком длинный.',
            'idempotency_key.unique' => 'Такой идемпотентный ключ уже использован.',

            'status.in' => 'Недопустимый статус платежа.',
            'currency.size' => 'Код валюты должен быть из 3 букв.',
            'currency.alpha' => 'Код валюты должен содержать только буквы.',

            'amount.required' => 'Не указана сумма платежа.',
            'amount.numeric' => 'Сумма платежа должна быть числом.',
            'amount.min' => 'Сумма платежа не может быть отрицательной.',

            'captured_at.date' => 'Поле captured_at должно быть корректной датой.',
            'refunded_at.date' => 'Поле refunded_at должно быть корректной датой.',

            'refunded_amount.numeric' => 'Сумма возврата должна быть числом.',
            'refunded_amount.min' => 'Сумма возврата не может быть отрицательной.',

            'error_code.max' => 'Код ошибки не должен превышать 64 символа.',
            'error_message.string' => 'Сообщение об ошибке должно быть строкой.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_order_id' => 'Заказ',
            'school_payment_method_id' => 'Способ оплаты',
            'school_user_payment_method_id' => 'Сохранённый способ пользователя',
            'provider' => 'Провайдер',
            'provider_payment_id' => 'ID платежа у провайдера',
            'idempotency_key' => 'Идемпотентный ключ',
            'status' => 'Статус',
            'currency' => 'Валюта',
            'amount' => 'Сумма',
            'captured_at' => 'Время подтверждения',
            'refunded_at' => 'Время возврата',
            'refunded_amount' => 'Сумма возврата',
            'error_code' => 'Код ошибки',
            'error_message' => 'Сообщение об ошибке',
            'meta' => 'Метаданные',
        ];
    }
}
