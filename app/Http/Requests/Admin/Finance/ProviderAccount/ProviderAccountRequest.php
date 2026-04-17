<?php

namespace App\Http\Requests\Admin\Finance\ProviderAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProviderAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // прикрутите Policy при необходимости
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['provider','title','mode','account_id','public_key'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim($this->input($k));
            }
        }

        // Нормализуем регистры
        if (!empty($merge['provider'])) {
            $merge['provider'] = strtolower($merge['provider']);
        }
        if (!empty($merge['mode'])) {
            $merge['mode'] = strtolower($merge['mode']);
        }

        // Если config пришёл строкой — попробуем распарсить JSON
        if ($this->filled('config') && is_string($this->input('config'))) {
            $decoded = json_decode($this->input('config'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['config'] = $decoded;
            }
        }

        // supported_* могут прийти строкой CSV
        foreach (['supported_currencies','supported_countries'] as $arrKey) {
            if ($this->filled($arrKey) && is_string($this->input($arrKey))) {
                $parts = array_values(array_filter(array_map('trim', explode(',', $this->input($arrKey)))));
                $merge[$arrKey] = $parts;
            }
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        $routeModel = $this->route('provider_account');
        $id = is_object($routeModel) ? $routeModel->id : ($routeModel ? (int)$routeModel : null);

        $provider = (string) $this->input('provider');
        $mode     = (string) $this->input('mode', 'test');

        return [
            'provider'   => [$this->isMethod('post') ? 'required' : 'sometimes','string','max:64'],
            'title'      => ['nullable','string','max:255'],
            'mode'       => [$this->isMethod('post') ? 'required' : 'sometimes','string', Rule::in(['test','live'])],

            'account_id' => [
                'nullable','string','max:191',
                // в рамках (provider, mode) account_id должен быть уникален
                Rule::unique('provider_accounts','account_id')
                    ->where(fn($q) => $q->where('provider',$provider)->where('mode',$mode))
                    ->ignore($id),
            ],
            'public_key'    => ['nullable','string','max:255'],
            'secret_key'    => ['nullable','string'],   // храним шифрованно (cast: encrypted)
            'webhook_secret'=> ['nullable','string'],   // тоже шифруется

            'supported_currencies'   => ['nullable','array'],
            'supported_currencies.*' => ['string','size:3'],
            'supported_countries'    => ['nullable','array'],
            'supported_countries.*'  => ['string','size:2'],

            'config'    => ['nullable','array'],

            'activity'  => ['sometimes','boolean'],
            'is_default' => ['sometimes','boolean'],

            'created_by' => ['nullable','integer','exists:users,id'],
            'updated_by' => ['nullable','integer','exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Если указан is_default=true — рекомендуем mode=live (мягкая проверка/подсказка)
            if ($this->boolean('is_default') && $this->input('mode') === 'test') {
                $v->errors()->add('mode', 'Учётка по умолчанию не должна быть в режиме test.');
            }

            // Валидация кодов валют/стран по регистру
            if ($this->filled('supported_currencies')) {
                foreach ((array)$this->input('supported_currencies') as $i => $code) {
                    if (!preg_match('/^[A-Z]{3}$/', (string)$code)) {
                        $v->errors()->add("supported_currencies.$i", 'Код валюты должен быть в формате ISO 4217 (например, USD).');
                    }
                }
            }
            if ($this->filled('supported_countries')) {
                foreach ((array)$this->input('supported_countries') as $i => $code) {
                    if (!preg_match('/^[A-Z]{2}$/', (string)$code)) {
                        $v->errors()->add("supported_countries.$i", 'Код страны должен быть в формате ISO 3166-1 alpha-2 (например, US).');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'provider.required' => 'Укажите провайдера (например, stripe, paypal, yookassa).',
            'provider.max'      => 'Слишком длинное значение поля provider.',
            'mode.required'     => 'Укажите режим учётной записи.',
            'mode.in'           => 'Режим должен быть test или live.',

            'account_id.unique' => 'Такой account_id уже используется для этого провайдера и режима.',
            'account_id.max'    => 'Слишком длинный account_id.',

            'public_key.max'    => 'Слишком длинный public_key.',

            'supported_currencies.array'   => 'Список валют должен быть массивом.',
            'supported_currencies.*.size'  => 'Каждая валюта должна быть в формате из 3 букв (ISO 4217).',
            'supported_countries.array'    => 'Список стран должен быть массивом.',
            'supported_countries.*.size'   => 'Каждая страна должна быть в формате из 2 букв (ISO 3166-1 alpha-2).',

            'config.array'      => 'Поле config должно быть объектом/массивом.',

            'activity.boolean'   => 'Поле activity должно быть булевым.',
            'is_default.boolean' => 'Поле is_default должно быть булевым.',

            'created_by.exists'  => 'Пользователь (created_by) не найден.',
            'updated_by.exists'  => 'Пользователь (updated_by) не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'provider'              => 'провайдер',
            'title'                 => 'название',
            'mode'                  => 'режим',
            'account_id'            => 'ID аккаунта провайдера',
            'public_key'            => 'публичный ключ',
            'secret_key'            => 'секретный ключ',
            'webhook_secret'        => 'секрет вебхуков',
            'supported_currencies'  => 'поддерживаемые валюты',
            'supported_countries'   => 'поддерживаемые страны',
            'config'                => 'настройки',
            'activity'              => 'активна',
            'is_default'            => 'по умолчанию',
            'created_by'            => 'создал',
            'updated_by'            => 'обновил',
        ];
    }
}
