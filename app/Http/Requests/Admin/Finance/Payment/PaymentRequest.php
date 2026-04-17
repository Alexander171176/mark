<?php

namespace App\Http\Requests\Admin\Finance\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите политику при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['provider','provider_payment_id','idempotency_key','currency','error_code'] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }

        // Валюта -> верхний регистр
        if ($this->filled('currency')) {
            $merge['currency'] = strtoupper($this->input('currency'));
        }

        // meta может прийти строкой JSON
        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['meta'] = $decoded;
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $payment = $this->route('payment'); // для ignore при update
        $id = is_object($payment) ? $payment->id : ($payment ? (int)$payment : null);

        $statusValues = ['pending','processing','succeeded','failed','canceled','refunded','partially_refunded'];

        return [
            'order_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:orders,id',
            ],

            'payment_method_id' => ['nullable','integer','exists:payment_methods,id'],
            'user_payment_method_id' => ['nullable','integer','exists:user_payment_methods,id'],

            'provider'            => ['nullable','string','max:64'],
            'provider_payment_id' => [
                'nullable','string','max:191',
                // уникальность по (provider, provider_payment_id)
                Rule::unique('payments', 'provider_payment_id')
                    ->where(fn($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],
            'idempotency_key'     => [
                'nullable','string','max:191',
                Rule::unique('payments', 'idempotency_key')->ignore($id),
            ],

            'status'   => ['sometimes','string', Rule::in($statusValues)],
            'currency' => ['sometimes','string','size:3','alpha'],
            'amount'   => [$this->isMethod('post') ? 'required' : 'sometimes', 'numeric','min:0'],

            'captured_at'     => ['nullable','date'],
            'refunded_at'     => ['nullable','date'],
            'refunded_amount' => ['nullable','numeric','min:0'],

            'error_code'    => ['nullable','string','max:64'],
            'error_message' => ['nullable','string'],

            'meta' => ['nullable','array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $status = $this->input('status');

            // Если указана refunded_amount — статус должен быть refunded или partially_refunded
            if ($this->filled('refunded_amount') && !in_array($status, ['refunded','partially_refunded'], true)) {
                $v->errors()->add('refunded_amount', 'При наличии суммы возврата статус должен быть refunded или partially_refunded.');
            }

            // refunded_amount не должен превышать amount
            if ($this->filled('refunded_amount') && $this->filled('amount')) {
                if ((float)$this->input('refunded_amount') > (float)$this->input('amount')) {
                    $v->errors()->add('refunded_amount', 'Сумма возврата не может превышать сумму платежа.');
                }
            }

            // Если есть user_payment_method_id — он должен принадлежать тому же пользователю, что и заказ
            if ($this->filled('user_payment_method_id') && $this->filled('order_id')) {
                $order = \App\Models\Admin\Finance\Order\Order::query()->find($this->input('order_id'));
                $upm   = \App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod::query()->find($this->input('user_payment_method_id'));
                if ($order && $upm && $order->user_id !== $upm->user_id) {
                    $v->errors()->add('user_payment_method_id', 'Указанный сохранённый способ оплаты не принадлежит владельцу заказа.');
                }
            }

            // Если статус succeeded — можно мягко требовать captured_at (не строго обязательно)
            if ($status === 'succeeded' && !$this->filled('captured_at')) {
                // Не «ошибка», а предупреждение? Оставим как ошибку в строгом режиме.
                $v->errors()->add('captured_at', 'Для статуса succeeded рекомендуется указать время подтверждения (captured_at).');
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Не указан заказ.',
            'order_id.integer'  => 'Идентификатор заказа должен быть числом.',
            'order_id.exists'   => 'Заказ не найден.',

            'payment_method_id.integer' => 'Идентификатор способа оплаты должен быть числом.',
            'payment_method_id.exists'  => 'Справочник способа оплаты не найден.',

            'user_payment_method_id.integer' => 'Идентификатор сохранённого способа должен быть числом.',
            'user_payment_method_id.exists'  => 'Сохранённый способ оплаты не найден.',

            'provider.string' => 'Поле provider должно быть строкой.',
            'provider.max'    => 'Поле provider не должно превышать 64 символа.',

            'provider_payment_id.string' => 'ID платежа у провайдера должен быть строкой.',
            'provider_payment_id.max'    => 'ID платежа у провайдера слишком длинный.',
            'provider_payment_id.unique' => 'Платёж с таким (provider, provider_payment_id) уже зарегистрирован.',

            'idempotency_key.string' => 'Идемпотентный ключ должен быть строкой.',
            'idempotency_key.max'    => 'Идемпотентный ключ слишком длинный.',
            'idempotency_key.unique' => 'Такой идемпотентный ключ уже использован.',

            'status.in'    => 'Недопустимый статус платежа.',
            'currency.size'=> 'Код валюты должен быть из 3 букв (ISO 4217).',
            'currency.alpha'=> 'Код валюты должен содержать только буквы.',
            'amount.required' => 'Не указана сумма платежа.',
            'amount.numeric'  => 'Сумма платежа должна быть числом.',
            'amount.min'      => 'Сумма платежа не может быть отрицательной.',

            'captured_at.date' => 'Поле captured_at должно быть корректной датой.',
            'refunded_at.date' => 'Поле refunded_at должно быть корректной датой.',

            'refunded_amount.numeric' => 'Сумма возврата должна быть числом.',
            'refunded_amount.min'     => 'Сумма возврата не может быть отрицательной.',

            'error_code.string' => 'Код ошибки должен быть строкой.',
            'error_code.max'    => 'Код ошибки слишком длинный.',
            'error_message.string' => 'Сообщение об ошибке должно быть строкой.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id'                => 'заказ',
            'payment_method_id'       => 'способ оплаты (справочник)',
            'user_payment_method_id'  => 'сохранённый способ пользователя',
            'provider'                => 'провайдер',
            'provider_payment_id'     => 'ID платежа у провайдера',
            'idempotency_key'         => 'идемпотентный ключ',
            'status'                  => 'статус',
            'currency'                => 'валюта',
            'amount'                  => 'сумма',
            'captured_at'             => 'время подтверждения',
            'refunded_at'             => 'время возврата',
            'refunded_amount'         => 'сумма возврата',
            'error_code'              => 'код ошибки',
            'error_message'           => 'сообщение об ошибке',
            'meta'                    => 'метаданные',
        ];
    }
}
