<?php

namespace App\Http\Requests\Admin\System\Refund;

use App\Models\Admin\Finance\Payment\Payment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подвяжите к Policy при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['provider','provider_refund_id','currency','status','reason'] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }
        if ($this->filled('currency')) {
            $merge['currency'] = strtoupper($this->input('currency'));
        }

        // JSON из строки
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
        $ref = $this->route('refund');
        $id  = is_object($ref) ? $ref->id : ($ref ? (int)$ref : null);

        $statusValues = ['requested','processing','succeeded','failed','canceled'];

        return [
            'order_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:orders,id',
            ],
            'payment_id' => ['nullable','integer','exists:payments,id'],

            'provider'           => ['nullable','string','max:64'],
            'provider_refund_id' => [
                'nullable','string','max:191',
                // уникальность пары (provider, provider_refund_id)
                Rule::unique('refunds', 'provider_refund_id')
                    ->where(fn($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'status'   => ['sometimes','string', Rule::in($statusValues)],
            'currency' => ['sometimes','string','size:3','alpha'],

            'amount'   => [$this->isMethod('post') ? 'required' : 'sometimes','numeric','gt:0'],

            'reason'   => ['nullable','string','max:191'],
            'notes'    => ['nullable','string'],
            'meta'     => ['nullable','array'],

            'requested_at' => ['nullable','date'],
            'processed_at' => ['nullable','date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Если указан payment_id — он должен принадлежать тому же order_id
            if ($this->filled('payment_id') && $this->filled('order_id')) {
                $payment = Payment::query()->find($this->input('payment_id'));
                if ($payment && (int)$payment->order_id !== (int)$this->input('order_id')) {
                    $v->errors()->add('payment_id', 'Указанный платёж относится к другому заказу.');
                }
            }

            // processed_at не раньше requested_at
            if ($this->filled('processed_at') && $this->filled('requested_at')) {
                try {
                    $proc = Carbon::parse($this->input('processed_at'));
                    $req  = Carbon::parse($this->input('requested_at'));
                    if ($proc->lt($req)) {
                        $v->errors()->add('processed_at', 'Дата завершения не может быть раньше даты запроса.');
                    }
                } catch (\Throwable $e) {}
            }

            // Для финальных статусов желательно наличие processed_at
            $finals = ['succeeded','failed','canceled'];
            if (in_array($this->input('status'), $finals, true) && !$this->filled('processed_at')) {
                $v->errors()->add('processed_at', 'Для финального статуса необходимо указать дату завершения processed_at.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Не указан заказ.',
            'order_id.integer'  => 'Идентификатор заказа должен быть числом.',
            'order_id.exists'   => 'Заказ не найден.',

            'payment_id.integer' => 'Идентификатор платежа должен быть числом.',
            'payment_id.exists'  => 'Платёж не найден.',

            'provider.string' => 'Поле provider должно быть строкой.',
            'provider.max'    => 'Поле provider не должно превышать 64 символа.',

            'provider_refund_id.string' => 'ID возврата у провайдера должен быть строкой.',
            'provider_refund_id.max'    => 'ID возврата у провайдера слишком длинный.',
            'provider_refund_id.unique' => 'Такой возврат у провайдера уже зарегистрирован (пара provider + provider_refund_id).',

            'status.in' => 'Недопустимый статус возврата.',

            'currency.size' => 'Код валюты должен состоять из 3 букв (ISO 4217).',
            'currency.alpha'=> 'Код валюты должен содержать только буквы.',

            'amount.required' => 'Не указана сумма возврата.',
            'amount.numeric'  => 'Сумма возврата должна быть числом.',
            'amount.gt'       => 'Сумма возврата должна быть больше нуля.',

            'reason.max'  => 'Причина возврата не должна превышать 191 символ.',
            'notes.string'=> 'Поле заметок должно быть строкой.',
            'meta.array'  => 'Поле meta должно быть объектом/массивом.',

            'requested_at.date' => 'Дата запроса возврата указана некорректно.',
            'processed_at.date' => 'Дата завершения возврата указана некорректно.',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id'           => 'заказ',
            'payment_id'         => 'платёж',
            'provider'           => 'провайдер',
            'provider_refund_id' => 'ID возврата у провайдера',
            'status'             => 'статус возврата',
            'currency'           => 'валюта',
            'amount'             => 'сумма возврата',
            'reason'             => 'причина',
            'notes'              => 'заметки',
            'meta'               => 'метаданные',
            'requested_at'       => 'дата запроса',
            'processed_at'       => 'дата завершения',
        ];
    }
}
