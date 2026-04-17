<?php

namespace App\Http\Requests\Admin\Crm\EmailSubscription;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('email')) {
            $merge['email'] = mb_strtolower(trim((string) $this->input('email')));
        }

        foreach ([
                     'list','status','confirm_token','unsub_reason','source','locale',
                     'ip','user_agent','provider','provider_subscriber_id','last_event'
                 ] as $f) {
            if ($this->filled($f)) {
                $merge[$f] = trim((string) $this->input($f));
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        $statuses = ['pending','subscribed','unsubscribed','bounced','complained'];

        // Для уникальности email+list при обновлении нужно игнорировать текущую запись
        // Пытаемся получить id из route-model binding или параметра маршрута.
        $currentId = $this->route('email_subscription');
        if ($currentId instanceof \App\Models\Admin\Crm\EmailSubscription\EmailSubscription) {
            $currentId = $currentId->getKey();
        }

        return [
            'user_id'   => ['nullable','integer','exists:users,id'],

            'email'     => [
                'required','email','max:255',
                Rule::unique('email_subscriptions')
                    ->ignore($currentId)
                    ->where(fn($q) => $q->where('list', $this->input('list', 'newsletter')))
            ],

            'list'      => ['sometimes','string','max:64'],

            'status'    => ['sometimes','string', Rule::in($statuses)],

            // Подтверждение/отписка
            'confirm_token'   => ['nullable','string','max:64'],
            'confirmed_at'    => ['nullable','date'],
            'unsubscribed_at' => ['nullable','date'],
            'unsub_reason'    => ['nullable','string','max:255'],

            // Источник/тех.инфо
            'source'     => ['nullable','string','max:64'],
            'locale'     => ['nullable','string','max:16'],
            'ip'         => ['nullable','ip'],
            'user_agent' => ['nullable','string'],

            // Интеграции
            'provider'               => ['nullable','string','max:64'],
            'provider_subscriber_id' => ['nullable','string','max:255'],
            'last_event'             => ['nullable','string','max:64'],

            // Метки/метаданные
            'tags'       => ['nullable','array'],
            'tags.*'     => ['string','max:64'],
            'meta'       => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'   => 'пользователь',
            'email'     => 'email',
            'list'      => 'лист подписки',
            'status'    => 'статус',
            'confirm_token'   => 'токен подтверждения',
            'confirmed_at'    => 'дата подтверждения',
            'unsubscribed_at' => 'дата отписки',
            'unsub_reason'    => 'причина отписки',
            'source'     => 'источник',
            'locale'     => 'локаль',
            'ip'         => 'IP адрес',
            'user_agent' => 'User‑Agent',
            'provider'               => 'провайдер',
            'provider_subscriber_id' => 'ID подписчика у провайдера',
            'last_event'             => 'последнее событие',
            'tags'       => 'теги',
            'meta'       => 'метаданные',
        ];
    }
}
