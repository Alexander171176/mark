<?php

namespace App\Http\Requests\Admin\School\Subscription;

use App\Models\Admin\School\Order\SchoolOrder;
use App\Models\Admin\School\UserPaymentMethod\SchoolUserPaymentMethod;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['provider', 'provider_subscription_id', 'currency', 'status'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if ($this->filled('currency')) {
            $data['currency'] = strtoupper($this->input('currency'));
        }

        foreach ([
                     'user_id',
                     'school_subscription_plan_id',
                     'school_order_id',
                     'school_user_payment_method_id',
                     'interval',
                     'trial_days',
                     'renewal_attempts',
                 ] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('price')) {
            $data['price'] = is_string($this->input('price'))
                ? str_replace(',', '.', trim($this->input('price')))
                : $this->input('price');
        }

        if ($this->has('cancel_at_period_end')) {
            $data['cancel_at_period_end'] = filter_var(
                $this->input('cancel_at_period_end'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        foreach (['features', 'limits', 'meta'] as $jsonField) {
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
        $subscription = $this->route('school_subscription')
            ?? $this->route('subscription')
            ?? $this->route('schoolSubscription');

        $id = is_object($subscription)
            ? $subscription->id
            : ($subscription ? (int) $subscription : null);

        return [
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'school_subscription_plan_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_subscription_plans', 'id'),
            ],

            'school_order_id' => ['nullable', 'integer', Rule::exists('school_orders', 'id')],
            'school_user_payment_method_id' => ['nullable', 'integer', Rule::exists('school_user_payment_methods', 'id')],

            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'price' => [$this->isMethod('post') ? 'required' : 'sometimes', 'numeric', 'min:0'],

            'billing_period' => ['sometimes', 'string', Rule::in(['day', 'week', 'month', 'year'])],
            'interval' => ['sometimes', 'integer', 'min:1', 'max:365'],

            'trial_days' => ['sometimes', 'integer', 'min:0', 'max:3650'],
            'trial_ends_at' => ['nullable', 'date'],

            'current_period_start' => ['nullable', 'date'],
            'current_period_end' => ['nullable', 'date', 'after_or_equal:current_period_start'],

            'started_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'cancelled_at' => ['nullable', 'date'],

            'cancel_at_period_end' => ['sometimes', 'boolean'],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['trialing', 'active', 'past_due', 'paused', 'cancelled', 'expired', 'incomplete', 'pending']),
            ],

            'provider' => ['nullable', 'string', 'max:64'],

            'provider_subscription_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_subscriptions', 'provider_subscription_id')
                    ->where(fn ($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'last_paid_at' => ['nullable', 'date'],
            'next_billing_at' => ['nullable', 'date'],

            'renewal_attempts' => ['sometimes', 'integer', 'min:0', 'max:1000'],

            'features' => ['nullable', 'array'],
            'limits' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('school_user_payment_method_id') && $this->filled('user_id')) {
                $paymentMethod = SchoolUserPaymentMethod::query()
                    ->find($this->input('school_user_payment_method_id'));

                if ($paymentMethod && (int) $paymentMethod->user_id !== (int) $this->input('user_id')) {
                    $validator->errors()->add(
                        'school_user_payment_method_id',
                        'Указанный сохранённый способ оплаты не принадлежит этому пользователю.'
                    );
                }
            }

            if ($this->filled('school_order_id') && $this->filled('user_id')) {
                $order = SchoolOrder::query()->find($this->input('school_order_id'));

                if ($order && $order->user_id && (int) $order->user_id !== (int) $this->input('user_id')) {
                    $validator->errors()->add(
                        'school_order_id',
                        'Заказ принадлежит другому пользователю.'
                    );
                }
            }

            if ($this->filled('trial_ends_at') && $this->filled('started_at')) {
                $trialEnds = Carbon::parse($this->input('trial_ends_at'));
                $started = Carbon::parse($this->input('started_at'));

                if ($trialEnds->lt($started)) {
                    $validator->errors()->add(
                        'trial_ends_at',
                        'Дата окончания триала не может быть раньше даты начала подписки.'
                    );
                }
            }

            if (
                $this->filled('next_billing_at')
                && $this->filled('current_period_start')
                && $this->filled('current_period_end')
            ) {
                $next = Carbon::parse($this->input('next_billing_at'));
                $start = Carbon::parse($this->input('current_period_start'));
                $end = Carbon::parse($this->input('current_period_end'));

                if ($next->lt($start) || $next->gt($end)) {
                    $validator->errors()->add(
                        'next_billing_at',
                        'Дата следующего биллинга должна находиться внутри текущего периода.'
                    );
                }
            }

            if ($this->input('status') === 'cancelled' && !$this->filled('cancelled_at')) {
                $validator->errors()->add(
                    'cancelled_at',
                    'Для статуса cancelled желательно указать дату отмены.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_subscription_plan_id.required' => 'Не указан тарифный план.',
            'school_subscription_plan_id.exists' => 'Тарифный план не найден.',

            'school_order_id.exists' => 'Заказ не найден.',
            'school_user_payment_method_id.exists' => 'Сохранённый способ оплаты не найден.',

            'currency.size' => 'Код валюты должен содержать 3 буквы.',
            'currency.alpha' => 'Код валюты должен содержать только буквы.',

            'price.required' => 'Не указана цена за период.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',

            'billing_period.in' => 'Недопустимое значение периода биллинга.',
            'interval.min' => 'Интервал должен быть не меньше 1.',

            'trial_days.min' => 'Пробный период не может быть отрицательным.',
            'trial_ends_at.date' => 'Дата окончания триала указана некорректно.',

            'current_period_end.after_or_equal' => 'Окончание периода не может быть раньше начала.',
            'ends_at.after_or_equal' => 'Окончание подписки не может быть раньше начала.',

            'cancel_at_period_end.boolean' => 'Поле cancel_at_period_end должно быть булевым.',
            'status.in' => 'Недопустимый статус подписки.',

            'provider.max' => 'Поле provider не должно превышать 64 символа.',
            'provider_subscription_id.unique' => 'Такая подписка у провайдера уже зарегистрирована.',

            'features.array' => 'Поле features должно быть объектом/массивом.',
            'limits.array' => 'Поле limits должно быть объектом/массивом.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }
}
