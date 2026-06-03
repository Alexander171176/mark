<?php

namespace App\Http\Requests\Admin\School\SchoolWebhookEvent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolWebhookEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['provider', 'event_type', 'external_id', 'idempotency_key', 'signature', 'status'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['school_order_id', 'school_payment_id', 'school_subscription_id', 'attempts'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['payload', 'headers'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $decoded = json_decode($this->input($field), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$field] = $decoded;
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $event = $this->route('school_webhook_event')
            ?? $this->route('webhookEvent')
            ?? $this->route('schoolWebhookEvent');

        $id = is_object($event)
            ? $event->id
            : ($event ? (int) $event : null);

        return [
            'provider' => ['required', 'string', 'max:64'],
            'event_type' => ['required', 'string', 'max:128'],

            'external_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_webhook_events', 'external_id')
                    ->where(fn ($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'idempotency_key' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_webhook_events', 'idempotency_key')->ignore($id),
            ],

            'signature' => ['nullable', 'string', 'max:255'],

            'school_order_id' => ['nullable', 'integer', Rule::exists('school_orders', 'id')],
            'school_payment_id' => ['nullable', 'integer', Rule::exists('school_payments', 'id')],
            'school_subscription_id' => ['nullable', 'integer', Rule::exists('school_subscriptions', 'id')],

            'payload' => ['required', 'array'],
            'headers' => ['nullable', 'array'],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['received', 'processing', 'processed', 'failed', 'skipped']),
            ],

            'attempts' => ['sometimes', 'integer', 'min:0'],

            'error_message' => ['nullable', 'string'],

            'delivered_at' => ['nullable', 'date'],
            'processed_at' => ['nullable', 'date', 'after_or_equal:delivered_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required' => 'Укажите провайдера.',
            'provider.max' => 'Провайдер не должен превышать 64 символа.',

            'event_type.required' => 'Укажите тип события.',
            'event_type.max' => 'Тип события не должен превышать 128 символов.',

            'external_id.unique' => 'Событие с таким external_id уже зарегистрировано для этого провайдера.',
            'idempotency_key.unique' => 'Такой idempotency_key уже использован.',

            'school_order_id.exists' => 'Заказ не найден.',
            'school_payment_id.exists' => 'Платёж не найден.',
            'school_subscription_id.exists' => 'Подписка не найдена.',

            'payload.required' => 'Поле payload обязательно.',
            'payload.array' => 'Payload должен быть JSON-объектом/массивом.',
            'headers.array' => 'Headers должен быть JSON-объектом/массивом.',

            'status.in' => 'Недопустимый статус вебхука.',
            'attempts.integer' => 'Количество попыток должно быть числом.',
            'attempts.min' => 'Количество попыток не может быть отрицательным.',

            'processed_at.after_or_equal' => 'Дата обработки не может быть раньше даты получения.',
        ];
    }

    public function attributes(): array
    {
        return [
            'provider' => 'Провайдер',
            'event_type' => 'Тип события',
            'external_id' => 'Внешний ID',
            'idempotency_key' => 'Ключ идемпотентности',
            'signature' => 'Подпись',
            'school_order_id' => 'Заказ',
            'school_payment_id' => 'Платёж',
            'school_subscription_id' => 'Подписка',
            'payload' => 'Payload',
            'headers' => 'Headers',
            'status' => 'Статус',
            'attempts' => 'Попытки',
            'error_message' => 'Ошибка',
            'delivered_at' => 'Дата получения',
            'processed_at' => 'Дата обработки',
        ];
    }
}
