<?php

namespace App\Http\Requests\Admin\Finance\UserPaymentMethod;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserPaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите политику при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Трим и нормализация строк
        foreach ([
                     'provider','provider_customer_id','provider_payment_method_id',
                     'brand','last4','country','billing_name','billing_email','billing_phone'
                 ] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }

        // country -> верхний регистр
        if ($this->filled('country')) {
            $merge['country'] = strtoupper($this->input('country'));
        }

        // billing_address может приходить строкой JSON
        if ($this->filled('billing_address') && is_string($this->input('billing_address'))) {
            $decoded = json_decode($this->input('billing_address'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['billing_address'] = $decoded;
            }
        }

        // meta может приходить строкой JSON
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
        // Для unique/ignore при PATCH/PUT
        $upm = $this->route('user_payment_method');
        $id  = is_object($upm) ? $upm->id : ($upm ? (int)$upm : null);

        $currentYear = (int) Carbon::now()->format('Y');

        return [
            // Владелец
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:users,id',
            ],

            // Справочник способа (необязательно — зависит от провайдера)
            'payment_method_id' => ['nullable','integer','exists:payment_methods,id'],

            // Идентификаторы провайдера
            'provider' => ['nullable','string','max:64'],
            'provider_customer_id' => ['nullable','string','max:191'],
            'provider_payment_method_id' => [
                'nullable','string','max:191',
                // уникальность для пары (provider, provider_payment_method_id)
                Rule::unique('user_payment_methods', 'provider_payment_method_id')
                    ->where(fn($q) => $q->where('provider', $this->input('provider')))
                    ->ignore($id),
            ],

            // Карточные реквизиты
            'brand'     => ['nullable','string','max:64'],
            'last4'     => ['nullable','string','regex:/^\d{4}$/'],
            'exp_month' => ['nullable','integer','between:1,12'],
            'exp_year'  => ['nullable','integer','min:'.$currentYear, 'max:'.($currentYear + 30)],
            'country'   => ['nullable','string','size:2','alpha'],

            // Биллинг-данные
            'billing_name'    => ['nullable','string','max:255'],
            'billing_email'   => ['nullable','email','max:255'],
            'billing_phone'   => ['nullable','string','max:32'],
            'billing_address' => ['nullable','array'],

            // Флаги/мета
            'is_default' => ['sometimes','boolean'],
            'activity'  => ['sometimes','boolean'],
            'meta'       => ['nullable','array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Проверяем «единственный default» на пользователя
            $isDefault = filter_var($this->input('is_default', false), FILTER_VALIDATE_BOOLEAN);
            if (!$isDefault) return;

            $userId = $this->input('user_id');
            // при обновлении user_id может не передаваться — достанем из текущей модели
            if (!$userId) {
                $upm = $this->route('user_payment_method');
                if (is_object($upm)) {
                    $userId = $upm->user_id;
                } elseif ($upm) {
                    // в крайнем случае — пробуем загрузить
                    try {
                        $model = \App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod::query()->findOrFail((int)$upm);
                        $userId = $model->user_id;
                    } catch (ModelNotFoundException $e) {
                        // игнор — пусть провалится базовая валидация
                    }
                }
            }

            if ($userId) {
                $query = \App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod::query()
                    ->where('user_id', $userId)
                    ->where('is_default', true);

                // исключаем текущую запись при обновлении
                $current = $this->route('user_payment_method');
                if ($current) {
                    $currentId = is_object($current) ? $current->id : (int)$current;
                    $query->where('id', '!=', $currentId);
                }

                if ($query->exists()) {
                    $v->errors()->add('is_default', 'У пользователя уже есть способ оплаты по умолчанию.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer'  => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'   => 'Пользователь не найден.',

            'payment_method_id.integer' => 'Идентификатор способа оплаты должен быть числом.',
            'payment_method_id.exists'  => 'Справочник способа оплаты не найден.',

            'provider.string' => 'Провайдер должен быть строкой.',
            'provider.max'    => 'Слишком длинное имя провайдера (максимум 64 символа).',

            'provider_customer_id.string' => 'Идентификатор клиента провайдера должен быть строкой.',
            'provider_customer_id.max'    => 'Идентификатор клиента слишком длинный.',

            'provider_payment_method_id.string' => 'Идентификатор способа оплаты провайдера должен быть строкой.',
            'provider_payment_method_id.max'    => 'Идентификатор способа оплаты провайдера слишком длинный.',
            'provider_payment_method_id.unique' => 'Такой токен/способ оплаты у провайдера уже сохранён.',

            'brand.string' => 'Бренд карты должен быть строкой.',
            'brand.max'    => 'Бренд карты слишком длинный.',

            'last4.regex'  => 'Поле last4 должно содержать ровно 4 цифры.',

            'exp_month.integer' => 'Месяц истечения должен быть числом.',
            'exp_month.between' => 'Месяц истечения должен быть в диапазоне 1..12.',

            'exp_year.integer' => 'Год истечения должен быть числом.',
            'exp_year.min'     => 'Год истечения не может быть в прошлом.',
            'exp_year.max'     => 'Год истечения слишком далёкий.',

            'country.size' => 'Код страны должен состоять из 2 букв (ISO-2).',
            'country.alpha'=> 'Код страны должен содержать только буквы.',

            'billing_name.string' => 'Имя плательщика должно быть строкой.',
            'billing_name.max'    => 'Имя плательщика слишком длинное.',
            'billing_email.email' => 'Укажите корректный email плательщика.',
            'billing_email.max'   => 'Email плательщика слишком длинный.',
            'billing_phone.string'=> 'Телефон плательщика должен быть строкой.',
            'billing_phone.max'   => 'Телефон плательщика слишком длинный.',
            'billing_address.array' => 'Адрес биллинга должен быть объектом/массивом.',

            'is_default.boolean' => 'Флаг «по умолчанию» должен быть булевым.',
            'activity.boolean'  => 'Флаг активности должен быть булевым.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'                    => 'пользователь',
            'payment_method_id'          => 'способ оплаты (справочник)',
            'provider'                   => 'провайдер',
            'provider_customer_id'       => 'ID клиента у провайдера',
            'provider_payment_method_id' => 'ID способа оплаты у провайдера',
            'brand'        => 'бренд карты',
            'last4'        => 'последние 4 цифры',
            'exp_month'    => 'месяц истечения',
            'exp_year'     => 'год истечения',
            'country'      => 'страна',
            'billing_name'  => 'имя плательщика',
            'billing_email' => 'email плательщика',
            'billing_phone' => 'телефон плательщика',
            'billing_address' => 'адрес биллинга',
            'is_default'   => 'по умолчанию',
            'activity'     => 'активность',
            'meta'         => 'метаданные',
        ];
    }
}
