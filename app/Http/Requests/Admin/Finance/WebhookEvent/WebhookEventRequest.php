<?php

namespace App\Http\Requests\Admin\Finance\WebhookEvent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebhookEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // доступ ограничивайте Policy/гейтами/маршрутами
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Трим полей-строк
        foreach (['provider','event_type','external_id','idempotency_key','signature'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim($this->input($k));
            }
        }

        // Если payload/headers пришли строкой JSON — распарсим
        foreach (['payload','headers'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $decoded = json_decode($this->input($k), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge[$k] = $decoded;
                }
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        // Для ignore() попытаемся взять id из модели маршрута (resource routes)
        $currentId = $this->route('webhook_event')?->id ?? null;

        return [
            'provider'        => ['required','string','max:64'],
            'event_type'      => ['required','string','max:128'],

            'external_id'     => [
                'nullable','string','max:191',
                // уникальность в рамках (provider, external_id), если external_id задан
                Rule::unique('webhook_events','external_id')
                    ->where(fn($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($currentId),
            ],
            'idempotency_key' => [
                'nullable','string','max:191',
                Rule::unique('webhook_events','idempotency_key')->ignore($currentId),
            ],
            'signature'       => ['nullable','string','max:255'],

            'order_id'        => ['nullable','integer','exists:orders,id'],
            'payment_id'      => ['nullable','integer','exists:payments,id'],
            'subscription_id' => ['nullable','integer','exists:subscriptions,id'],

            // payload обязателен и должен быть объектом/массивом
            'payload'         => ['required','array'],
            'headers'         => ['nullable','array'],

            'status'          => ['sometimes','string', Rule::in(['received','processing','processed','failed','skipped'])],
            'attempts'        => ['sometimes','integer','min:0'],

            'delivered_at'    => ['nullable','date'],
            'processed_at'    => ['nullable','date','after_or_equal:delivered_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required'   => 'Укажите источник события (provider).',
            'provider.max'        => 'Поле provider не должно превышать :max символов.',

            'event_type.required' => 'Укажите тип события (event_type).',
            'event_type.max'      => 'Поле event_type не должно превышать :max символов.',

            'external_id.max'     => 'Поле external_id не должно превышать :max символов.',
            'external_id.unique'  => 'Событие с таким external_id уже зарегистрировано для данного провайдера.',

            'idempotency_key.max'    => 'Поле idempotency_key не должно превышать :max символов.',
            'idempotency_key.unique' => 'Такой idempotency_key уже использован.',

            'signature.max'       => 'Сигнатура слишком длинная (макс. :max).',

            'order_id.exists'        => 'Указанный заказ не найден.',
            'payment_id.exists'      => 'Указанный платеж не найден.',
            'subscription_id.exists' => 'Указанная подписка не найдена.',

            'payload.required'    => 'Поле payload обязательно.',
            'payload.array'       => 'Поле payload должно быть валидным JSON-объектом.',
            'headers.array'       => 'Поле headers должно быть валидным JSON-объектом.',

            'status.in'           => 'Недопустимый статус. Разрешены: received, processing, processed, failed, skipped.',
            'attempts.integer'    => 'Поле attempts должно быть целым числом.',
            'attempts.min'        => 'Поле attempts не может быть отрицательным.',

            'delivered_at.date'   => 'Поле delivered_at должно быть датой.',
            'processed_at.date'   => 'Поле processed_at должно быть датой.',
            'processed_at.after_or_equal' => 'processed_at не может быть раньше delivered_at.',
        ];
    }

    public function attributes(): array
    {
        return [
            'provider'        => 'провайдер',
            'event_type'      => 'тип события',
            'external_id'     => 'внешний ID',
            'idempotency_key' => 'ключ идемпотентности',
            'signature'       => 'подпись',
            'payload'         => 'тело вебхука',
            'headers'         => 'заголовки',
            'status'          => 'статус',
            'attempts'        => 'число попыток',
            'delivered_at'    => 'время доставки',
            'processed_at'    => 'время обработки',
        ];
    }
}
