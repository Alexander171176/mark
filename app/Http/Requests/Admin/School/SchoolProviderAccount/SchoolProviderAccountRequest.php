<?php

namespace App\Http\Requests\Admin\School\SchoolProviderAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolProviderAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['provider', 'title', 'mode', 'account_id', 'public_key'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['provider'])) {
            $data['provider'] = strtolower($data['provider']);
        }

        if (!empty($data['mode'])) {
            $data['mode'] = strtolower($data['mode']);
        }

        foreach (['activity', 'is_default'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['created_by', 'updated_by'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['supported_currencies', 'supported_countries'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = array_values(array_filter(
                    array_map('trim', explode(',', $this->input($field)))
                ));
            }
        }

        foreach (['supported_currencies', 'supported_countries'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_map('strtoupper', $data[$field]);
            }
        }

        if ($this->filled('config') && is_string($this->input('config'))) {
            $decoded = json_decode($this->input('config'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['config'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $account = $this->route('school_provider_account')
            ?? $this->route('providerAccount')
            ?? $this->route('schoolProviderAccount');

        $id = is_object($account)
            ? $account->id
            : ($account ? (int) $account : null);

        $provider = (string) $this->input('provider');
        $mode = (string) $this->input('mode', 'test');

        return [
            'provider' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:64',
            ],

            'title' => ['nullable', 'string', 'max:255'],

            'mode' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                Rule::in(['test', 'live']),
            ],

            'account_id' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('school_provider_accounts', 'account_id')
                    ->where(fn ($q) => $q
                        ->where('provider', $provider)
                        ->where('mode', $mode))
                    ->ignore($id),
            ],

            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string'],
            'webhook_secret' => ['nullable', 'string'],

            'supported_currencies' => ['nullable', 'array'],
            'supported_currencies.*' => ['string', 'size:3'],

            'supported_countries' => ['nullable', 'array'],
            'supported_countries.*' => ['string', 'size:2'],

            'config' => ['nullable', 'array'],

            'activity' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],

            'created_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'updated_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->boolean('is_default') && $this->input('mode') === 'test') {
                $validator->errors()->add(
                    'mode',
                    'Учётка по умолчанию не должна быть в режиме test.'
                );
            }

            foreach ((array) $this->input('supported_currencies', []) as $index => $code) {
                if (!preg_match('/^[A-Z]{3}$/', (string) $code)) {
                    $validator->errors()->add(
                        "supported_currencies.$index",
                        'Код валюты должен быть в формате ISO 4217, например USD.'
                    );
                }
            }

            foreach ((array) $this->input('supported_countries', []) as $index => $code) {
                if (!preg_match('/^[A-Z]{2}$/', (string) $code)) {
                    $validator->errors()->add(
                        "supported_countries.$index",
                        'Код страны должен быть в формате ISO 3166-1 alpha-2, например US.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'provider.required' => 'Укажите провайдера.',
            'provider.max' => 'Провайдер не должен превышать 64 символа.',

            'mode.required' => 'Укажите режим учётной записи.',
            'mode.in' => 'Режим должен быть test или live.',

            'account_id.unique' => 'Такой account_id уже используется для этого провайдера и режима.',
            'account_id.max' => 'Account ID не должен превышать 191 символ.',

            'public_key.max' => 'Public key не должен превышать 255 символов.',

            'supported_currencies.array' => 'Список валют должен быть массивом.',
            'supported_currencies.*.size' => 'Каждая валюта должна состоять из 3 букв.',

            'supported_countries.array' => 'Список стран должен быть массивом.',
            'supported_countries.*.size' => 'Каждая страна должна состоять из 2 букв.',

            'config.array' => 'Поле config должно быть объектом/массивом.',

            'activity.boolean' => 'Поле активности должно быть булевым.',
            'is_default.boolean' => 'Поле по умолчанию должно быть булевым.',

            'created_by.exists' => 'Пользователь created_by не найден.',
            'updated_by.exists' => 'Пользователь updated_by не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'provider' => 'Провайдер',
            'title' => 'Название',
            'mode' => 'Режим',
            'account_id' => 'ID аккаунта провайдера',
            'public_key' => 'Публичный ключ',
            'secret_key' => 'Секретный ключ',
            'webhook_secret' => 'Webhook секрет',
            'supported_currencies' => 'Поддерживаемые валюты',
            'supported_countries' => 'Поддерживаемые страны',
            'config' => 'Настройки',
            'activity' => 'Активность',
            'is_default' => 'По умолчанию',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто обновил',
        ];
    }
}
