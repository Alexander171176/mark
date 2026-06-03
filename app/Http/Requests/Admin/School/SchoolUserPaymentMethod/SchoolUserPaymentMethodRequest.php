<?php

namespace App\Http\Requests\Admin\School\SchoolUserPaymentMethod;

use App\Models\Admin\School\SchoolUserPaymentMethod\SchoolUserPaymentMethod;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolUserPaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ([
                     'provider',
                     'provider_customer_id',
                     'provider_payment_method_id',
                     'brand',
                     'last4',
                     'country',
                     'billing_name',
                     'billing_email',
                     'billing_phone',
                 ] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if ($this->filled('country')) {
            $data['country'] = strtoupper($this->input('country'));
        }

        foreach (['user_id', 'school_payment_method_id', 'exp_month', 'exp_year'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['is_default', 'activity'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['billing_address', 'meta'] as $jsonField) {
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
        $current = $this->route('school_user_payment_method')
            ?? $this->route('userPaymentMethod')
            ?? $this->route('schoolUserPaymentMethod');

        $id = is_object($current)
            ? $current->id
            : ($current ? (int) $current : null);

        $currentYear = (int) Carbon::now()->format('Y');

        return [
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'school_payment_method_id' => [
                'nullable',
                'integer',
                Rule::exists('school_payment_methods', 'id'),
            ],

            'provider' => ['nullable', 'string', 'max:64'],
            'provider_customer_id' => ['nullable', 'string', 'max:191'],

            'provider_payment_method_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_user_payment_methods', 'provider_payment_method_id')
                    ->where(fn ($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            'brand' => ['nullable', 'string', 'max:64'],
            'last4' => ['nullable', 'string', 'regex:/^\d{4}$/'],
            'exp_month' => ['nullable', 'integer', 'between:1,12'],
            'exp_year' => ['nullable', 'integer', 'min:' . $currentYear, 'max:' . ($currentYear + 30)],
            'country' => ['nullable', 'string', 'size:2', 'alpha'],

            'billing_name' => ['nullable', 'string', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:32'],
            'billing_address' => ['nullable', 'array'],

            'is_default' => ['sometimes', 'boolean'],
            'activity' => ['sometimes', 'boolean'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $isDefault = filter_var($this->input('is_default', false), FILTER_VALIDATE_BOOLEAN);

            if (!$isDefault) {
                return;
            }

            $userId = $this->input('user_id');
            $current = $this->route('school_user_payment_method')
                ?? $this->route('userPaymentMethod')
                ?? $this->route('schoolUserPaymentMethod');

            $currentId = null;

            if (is_object($current)) {
                $currentId = $current->id;
                $userId = $userId ?: $current->user_id;
            } elseif ($current) {
                $currentId = (int) $current;

                try {
                    $model = SchoolUserPaymentMethod::query()->findOrFail($currentId);
                    $userId = $userId ?: $model->user_id;
                } catch (ModelNotFoundException) {
                    return;
                }
            }

            if (!$userId) {
                return;
            }

            $exists = SchoolUserPaymentMethod::query()
                ->where('user_id', $userId)
                ->where('is_default', true)
                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'is_default',
                    'У пользователя уже есть способ оплаты по умолчанию.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_payment_method_id.integer' => 'Идентификатор способа оплаты должен быть числом.',
            'school_payment_method_id.exists' => 'Справочник способа оплаты не найден.',

            'provider.max' => 'Имя провайдера не должно превышать 64 символа.',
            'provider_customer_id.max' => 'ID клиента у провайдера слишком длинный.',
            'provider_payment_method_id.unique' => 'Такой способ оплаты у провайдера уже сохранён.',

            'last4.regex' => 'Поле last4 должно содержать ровно 4 цифры.',
            'exp_month.between' => 'Месяц окончания должен быть от 1 до 12.',
            'exp_year.min' => 'Год окончания не может быть в прошлом.',
            'exp_year.max' => 'Год окончания слишком далёкий.',

            'country.size' => 'Код страны должен состоять из 2 букв.',
            'country.alpha' => 'Код страны должен содержать только буквы.',

            'billing_email.email' => 'Укажите корректный email плательщика.',
            'billing_phone.max' => 'Телефон плательщика не должен превышать 32 символа.',
            'billing_address.array' => 'Платёжный адрес должен быть объектом/массивом.',

            'is_default.boolean' => 'Флаг по умолчанию должен быть булевым.',
            'activity.boolean' => 'Флаг активности должен быть булевым.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_payment_method_id' => 'Способ оплаты',
            'provider' => 'Провайдер',
            'provider_customer_id' => 'ID клиента у провайдера',
            'provider_payment_method_id' => 'ID способа оплаты у провайдера',
            'brand' => 'Бренд карты',
            'last4' => 'Последние 4 цифры',
            'exp_month' => 'Месяц окончания',
            'exp_year' => 'Год окончания',
            'country' => 'Страна',
            'billing_name' => 'Имя плательщика',
            'billing_email' => 'Email плательщика',
            'billing_phone' => 'Телефон плательщика',
            'billing_address' => 'Платёжный адрес',
            'is_default' => 'По умолчанию',
            'activity' => 'Активность',
            'meta' => 'Meta',
        ];
    }
}
