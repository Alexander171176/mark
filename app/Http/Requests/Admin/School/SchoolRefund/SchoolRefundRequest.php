<?php

namespace App\Http\Requests\Admin\School\SchoolRefund;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_order_id', 'school_payment_id'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['provider', 'provider_refund_id', 'status', 'currency', 'reason'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if ($this->filled('currency')) {
            $data['currency'] = strtoupper($this->input('currency'));
        }

        if ($this->filled('amount')) {
            $data['amount'] = is_string($this->input('amount'))
                ? str_replace(',', '.', trim($this->input('amount')))
                : $this->input('amount');
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
        $refund = $this->route('school_refund')
            ?? $this->route('refund')
            ?? $this->route('schoolRefund');

        $id = is_object($refund)
            ? $refund->id
            : ($refund ? (int) $refund : null);

        return [
            'school_order_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_orders', 'id'),
            ],

            'school_payment_id' => [
                'nullable',
                'integer',
                Rule::exists('school_payments', 'id'),
            ],

            'provider' => ['nullable', 'string', 'max:64'],

            'provider_refund_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_refunds', 'provider_refund_id')
                    ->where(fn ($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['requested', 'processing', 'succeeded', 'failed', 'canceled']),
            ],

            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'amount' => [$this->isMethod('post') ? 'required' : 'sometimes', 'numeric', 'min:0.01'],

            'reason' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],

            'requested_at' => ['nullable', 'date'],
            'processed_at' => ['nullable', 'date', 'after_or_equal:requested_at'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                $this->filled('school_payment_id')
                && $this->filled('school_order_id')
            ) {
                $payment = \DB::table('school_payments')
                    ->where('id', $this->input('school_payment_id'))
                    ->first();

                if ($payment && (int) $payment->school_order_id !== (int) $this->input('school_order_id')) {
                    $validator->errors()->add(
                        'school_payment_id',
                        'Указанный платёж не относится к выбранному заказу.'
                    );
                }

                if ($payment && $this->filled('amount') && (float) $this->input('amount') > (float) $payment->amount) {
                    $validator->errors()->add(
                        'amount',
                        'Сумма возврата не может превышать сумму платежа.'
                    );
                }
            }

            if ($this->input('status') === 'succeeded' && !$this->filled('processed_at')) {
                $validator->errors()->add(
                    'processed_at',
                    'Для успешного возврата желательно указать дату обработки.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_order_id.required' => 'Не указан заказ.',
            'school_order_id.exists' => 'Заказ не найден.',

            'school_payment_id.exists' => 'Платёж не найден.',

            'provider.max' => 'Провайдер не должен превышать 64 символа.',
            'provider_refund_id.unique' => 'Возврат с таким ID у провайдера уже существует.',

            'status.in' => 'Недопустимый статус возврата.',

            'currency.size' => 'Код валюты должен содержать 3 буквы.',
            'currency.alpha' => 'Код валюты должен содержать только буквы.',

            'amount.required' => 'Укажите сумму возврата.',
            'amount.numeric' => 'Сумма возврата должна быть числом.',
            'amount.min' => 'Сумма возврата должна быть больше 0.',

            'reason.max' => 'Причина возврата не должна превышать 191 символ.',
            'notes.string' => 'Заметки должны быть текстом.',
            'meta.array' => 'Meta должен быть объектом/массивом.',

            'requested_at.date' => 'Дата запроса указана некорректно.',
            'processed_at.date' => 'Дата обработки указана некорректно.',
            'processed_at.after_or_equal' => 'Дата обработки не может быть раньше даты запроса.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_order_id' => 'Заказ',
            'school_payment_id' => 'Платёж',
            'provider' => 'Провайдер',
            'provider_refund_id' => 'ID возврата у провайдера',
            'status' => 'Статус',
            'currency' => 'Валюта',
            'amount' => 'Сумма возврата',
            'reason' => 'Причина',
            'notes' => 'Заметки',
            'meta' => 'Meta',
            'requested_at' => 'Дата запроса',
            'processed_at' => 'Дата обработки',
        ];
    }
}
