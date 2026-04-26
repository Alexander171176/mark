<?php

namespace App\Http\Requests\Admin\Market\MarketStorefront;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketStorefrontRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка данных перед валидацией
     */
    public function prepareForValidation(): void
    {
        $slug = $this->input('slug');

        $preparedSlug = $this->filled('slug')
            ? Str::slug(trim((string) $slug))
            : null;

        $this->merge([
            'company_id' => $this->input('company_id'),

            'slug' => filled($preparedSlug) ? $preparedSlug : null,

            'domain' => $this->filled('domain')
                ? strtolower(trim((string) $this->input('domain')))
                : null,

            'subdomain' => $this->filled('subdomain')
                ? strtolower(trim((string) $this->input('subdomain')))
                : null,

            'primary_host' => $this->filled('primary_host')
                ? strtolower(trim((string) $this->input('primary_host')))
                : null,

            'default_locale' => $this->filled('default_locale')
                ? strtolower(trim((string) $this->input('default_locale')))
                : null,

            'default_currency_id' => $this->filled('default_currency_id')
                ? (int) $this->input('default_currency_id')
                : null,

            'note' => $this->filled('note')
                ? trim((string) $this->input('note'))
                : null,

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 0,

            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),

            'is_main' => filter_var($this->input('is_main', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Правила валидации
     */
    public function rules(): array
    {
        $storefrontId = $this->route('marketStorefront')?->id
            ?? $this->route('marketStorefront')
            ?? $this->route('id');

        $companyId = $this->input('company_id');

        return [
            'company_id' => [
                'required',
                'integer',
                'exists:market_companies,id',
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],
            'is_main' => ['nullable', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('market_storefronts', 'slug')
                    ->where(fn ($q) => $q->where('company_id', $companyId))
                    ->ignore($storefrontId),
            ],

            'domain' => ['nullable', 'string', 'max:255'],
            'subdomain' => ['nullable', 'string', 'max:191'],

            'primary_host' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('market_storefronts', 'primary_host')
                    ->ignore($storefrontId),
            ],

            'default_locale' => ['nullable', 'string', 'max:10'],

            'default_currency_id' => [
                'nullable',
                'integer',
                'exists:currencies,id',
            ],

            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Сообщения
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Компания обязательна.',
            'company_id.exists' => 'Компания не найдена.',

            'slug.required' => 'Slug обязателен.',
            'slug.regex' => 'Slug может содержать только строчные латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Такая витрина уже существует у этой компании.',
            'slug.max' => 'Slug не должен превышать 191 символ.',

            'primary_host.unique' => 'Такой host уже используется другой витриной.',
            'primary_host.max' => 'Host не должен превышать 255 символов.',

            'default_currency_id.exists' => 'Выбранная валюта не существует.',

            'note.max' => 'Заметка не должна превышать 255 символов.',

            'sort.integer' => 'Сортировка должна быть числом.',
            'sort.min' => 'Сортировка не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим.',
            'is_main.boolean' => 'Поле главной витрины должно быть логическим.',
        ];
    }
}
