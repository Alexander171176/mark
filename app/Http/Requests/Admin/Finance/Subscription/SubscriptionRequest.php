<?php

namespace App\Http\Requests\Admin\Finance\Subscription;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // привяжите к политике при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['provider','provider_subscription_id','currency','status'] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }
        if ($this->filled('currency')) {
            $merge['currency'] = strtoupper($this->input('currency'));
        }

        // JSON-поля могут прийти строкой
        foreach (['features','limits','meta'] as $jsonKey) {
            if ($this->filled($jsonKey) && is_string($this->input($jsonKey))) {
                $decoded = json_decode($this->input($jsonKey), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge[$jsonKey] = $decoded;
                }
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $sub = $this->route('subscription');
        $id  = is_object($sub) ? $sub->id : ($sub ? (int)$sub : null);

        $statusValues = ['trialing','active','past_due','paused','cancelled','expired','incomplete','pending'];
        $periodValues = ['day','week','month','year'];

        return [
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:users,id',
            ],
            'subscription_plan_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:subscription_plans,id',
            ],
            'order_id' => ['nullable','integer','exists:orders,id'],
            'user_payment_method_id' => ['nullable','integer','exists:user_payment_methods,id'],

            'currency'       => ['sometimes','string','size:3','alpha'],
            'price'          => [$this->isMethod('post') ? 'required' : 'sometimes','numeric','min:0'],
            'billing_period' => ['sometimes','string', Rule::in($periodValues)],
            'interval'       => ['sometimes','integer','min:1','max:365'],

            'trial_days'    => ['sometimes','integer','min:0','max:3650'],
            'trial_ends_at' => ['nullable','date'],

            'current_period_start' => ['nullable','date'],
            'current_period_end'   => ['nullable','date','after_or_equal:current_period_start'],

            'started_at'   => ['nullable','date'],
            'ends_at'      => ['nullable','date','after_or_equal:started_at'],
            'cancelled_at' => ['nullable','date'],

            'cancel_at_period_end' => ['sometimes','boolean'],

            'status' => ['sometimes','string', Rule::in($statusValues)],

            'provider' => ['nullable','string','max:64'],
            'provider_subscription_id' => [
                'nullable','string','max:191',
                // уникальность по (provider, provider_subscription_id)
                Rule::unique('subscriptions', 'provider_subscription_id')
                    ->where(fn($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'last_paid_at'    => ['nullable','date'],
            'next_billing_at' => ['nullable','date'],

            'renewal_attempts' => ['sometimes','integer','min:0','max:1000'],

            'features' => ['nullable','array'],
            'limits'   => ['nullable','array'],
            'meta'     => ['nullable','array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // user_payment_method должен принадлежать тому же пользователю
            if ($this->filled('user_payment_method_id') && $this->filled('user_id')) {
                $upm = UserPaymentMethod::query()->find($this->input('user_payment_method_id'));
                if ($upm && (int)$upm->user_id !== (int)$this->input('user_id')) {
                    $v->errors()->add('user_payment_method_id', 'Указанный сохранённый способ оплаты не принадлежит этому пользователю.');
                }
            }

            // Если есть order_id — его user_id должен совпадать с user_id подписки
            if ($this->filled('order_id') && $this->filled('user_id')) {
                $order = Order::query()->find($this->input('order_id'));
                if ($order && (int)$order->user_id !== (int)$this->input('user_id')) {
                    $v->errors()->add('order_id', 'Заказ принадлежит другому пользователю.');
                }
            }

            // trial_ends_at логика
            if ($this->filled('trial_ends_at') && $this->filled('started_at')) {
                try {
                    $trialEnds = Carbon::parse($this->input('trial_ends_at'));
                    $started   = Carbon::parse($this->input('started_at'));
                    if ($trialEnds->lt($started)) {
                        $v->errors()->add('trial_ends_at', 'Дата окончания триала не может быть раньше даты начала подписки.');
                    }
                } catch (\Throwable $e) {
                    // парсер даты уже покрыт базовыми правилами
                }
            }

            // next_billing_at должен попадать внутрь текущего периода (если оба заданы)
            if ($this->filled('next_billing_at') && $this->filled('current_period_start') && $this->filled('current_period_end')) {
                try {
                    $next = Carbon::parse($this->input('next_billing_at'));
                    $st   = Carbon::parse($this->input('current_period_start'));
                    $en   = Carbon::parse($this->input('current_period_end'));
                    if ($next->lt($st) || $next->gt($en)) {
                        $v->errors()->add('next_billing_at', 'Дата следующего биллинга должна находиться внутри текущего периода.');
                    }
                } catch (\Throwable $e) {
                    // базовые date-правила уже сработают
                }
            }

            // Если статус cancelled — должна быть задана cancelled_at (рекомендуемое правило)
            if ($this->input('status') === 'cancelled' && !$this->filled('cancelled_at')) {
                $v->errors()->add('cancelled_at', 'Для статуса cancelled желательно указать дату отмены (cancelled_at).');
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer'  => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'   => 'Пользователь не найден.',

            'subscription_plan_id.required' => 'Не указан тарифный план.',
            'subscription_plan_id.integer'  => 'Идентификатор плана должен быть числом.',
            'subscription_plan_id.exists'   => 'Тарифный план не найден.',

            'order_id.integer' => 'Идентификатор заказа должен быть числом.',
            'order_id.exists'  => 'Заказ не найден.',

            'user_payment_method_id.integer' => 'Идентификатор сохранённого метода должен быть числом.',
            'user_payment_method_id.exists'  => 'Сохранённый способ оплаты не найден.',

            'currency.size' => 'Код валюты должен содержать 3 буквы (ISO 4217).',
            'currency.alpha'=> 'Код валюты должен содержать только буквы.',
            'price.required'=> 'Не указана цена за период.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min'     => 'Цена не может быть отрицательной.',

            'billing_period.in' => 'Недопустимое значение периода биллинга.',
            'interval.integer'  => 'Интервал должен быть целым числом.',
            'interval.min'      => 'Интервал должен быть не меньше 1.',
            'interval.max'      => 'Интервал слишком большой.',

            'trial_days.integer' => 'Пробный период должен быть целым числом.',
            'trial_days.min'     => 'Пробный период не может быть отрицательным.',
            'trial_days.max'     => 'Пробный период слишком большой.',
            'trial_ends_at.date' => 'Дата окончания триала указана некорректно.',

            'current_period_start.date' => 'Дата начала периода указана некорректно.',
            'current_period_end.date'   => 'Дата окончания периода указана некорректно.',
            'current_period_end.after_or_equal' => 'Окончание периода не может быть раньше начала.',

            'started_at.date'  => 'Дата начала подписки указана некорректно.',
            'ends_at.date'     => 'Дата окончания подписки указана некорректно.',
            'ends_at.after_or_equal' => 'Окончание подписки не может быть раньше начала.',
            'cancelled_at.date'=> 'Дата отмены указана некорректно.',

            'cancel_at_period_end.boolean' => 'Поле cancel_at_period_end должно быть булевым.',

            'status.in' => 'Недопустимый статус подписки.',

            'provider.string' => 'Поле provider должно быть строкой.',
            'provider.max'    => 'Поле provider не должно превышать 64 символа.',

            'provider_subscription_id.string' => 'ID подписки у провайдера должен быть строкой.',
            'provider_subscription_id.max'    => 'ID подписки у провайдера слишком длинный.',
            'provider_subscription_id.unique' => 'Такая подписка у провайдера уже зарегистрирована (пара provider + provider_subscription_id).',

            'last_paid_at.date'    => 'Дата последнего платежа указана некорректно.',
            'next_billing_at.date' => 'Дата следующего биллинга указана некорректно.',

            'renewal_attempts.integer' => 'Количество попыток продления должно быть целым числом.',
            'renewal_attempts.min'     => 'Количество попыток продления не может быть отрицательным.',
            'renewal_attempts.max'     => 'Количество попыток продления слишком большое.',

            'features.array' => 'Поле features должно быть объектом/массивом.',
            'limits.array'   => 'Поле limits должно быть объектом/массивом.',
            'meta.array'     => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'                 => 'пользователь',
            'subscription_plan_id'    => 'тарифный план',
            'order_id'                => 'заказ',
            'user_payment_method_id'  => 'сохранённый способ оплаты',
            'currency'                => 'валюта',
            'price'                   => 'цена за период',
            'billing_period'          => 'период биллинга',
            'interval'                => 'интервал',
            'trial_days'              => 'пробный период (дней)',
            'trial_ends_at'           => 'окончание триала',
            'current_period_start'    => 'начало текущего периода',
            'current_period_end'      => 'окончание текущего периода',
            'started_at'              => 'дата начала',
            'ends_at'                 => 'дата окончания',
            'cancelled_at'            => 'дата отмены',
            'cancel_at_period_end'    => 'отменить в конце периода',
            'status'                  => 'статус',
            'provider'                => 'провайдер',
            'provider_subscription_id'=> 'ID подписки у провайдера',
            'last_paid_at'            => 'дата последнего платежа',
            'next_billing_at'         => 'дата следующего биллинга',
            'renewal_attempts'        => 'количество попыток продления',
            'features'                => 'фичи',
            'limits'                  => 'лимиты',
            'meta'                    => 'метаданные',
        ];
    }
}
