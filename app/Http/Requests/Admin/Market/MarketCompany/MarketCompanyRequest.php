<?php

namespace App\Http\Requests\Admin\Market\MarketCompany;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $name = $this->input('name');
        $slug = $this->input('slug');

        $preparedSlug = $this->filled('slug')
            ? Str::slug(trim((string) $slug))
            : ($this->filled('name') ? Str::slug(trim((string) $name)) : null);

        $this->merge([
            'name' => $this->filled('name') ? trim((string) $name) : null,
            'brand_name' => $this->filled('brand_name') ? trim((string) $this->input('brand_name')) : null,
            'legal_name' => $this->filled('legal_name') ? trim((string) $this->input('legal_name')) : null,

            'slug' => filled($preparedSlug) ? $preparedSlug : null,

            'external_id' => $this->filled('external_id') ? trim((string) $this->input('external_id')) : null,

            'company_type' => $this->filled('company_type') ? trim((string) $this->input('company_type')) : null,
            'tax_regime' => $this->filled('tax_regime') ? trim((string) $this->input('tax_regime')) : null,
            'bin_iin' => $this->filled('bin_iin') ? trim((string) $this->input('bin_iin')) : null,

            'email' => $this->filled('email') ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'phone' => $this->filled('phone') ? trim((string) $this->input('phone')) : null,

            'messenger_type' => $this->filled('messenger_type')
                ? mb_strtolower(trim((string) $this->input('messenger_type')))
                : null,
            'messenger_contact' => $this->filled('messenger_contact') ? trim((string) $this->input('messenger_contact')) : null,

            'country' => $this->filled('country')
                ? mb_strtoupper(trim((string) $this->input('country')))
                : null,
            'city' => $this->filled('city') ? trim((string) $this->input('city')) : null,

            'legal_address' => $this->filled('legal_address') ? trim((string) $this->input('legal_address')) : null,
            'actual_address' => $this->filled('actual_address') ? trim((string) $this->input('actual_address')) : null,

            'sort' => $this->filled('sort') ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        $marketCompanyId = $this->route('marketCompany')?->id
            ?? $this->route('marketCompany')
            ?? $this->route('id');

        return [
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],

            'name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],

            'slug' => [
                'nullable',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('market_companies', 'slug')->ignore($marketCompanyId),
            ],

            'external_id' => ['nullable', 'string', 'max:191'],

            'company_type' => ['nullable', 'string', 'max:64'],
            'tax_regime' => ['nullable', 'string', 'max:64'],

            'bin_iin' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('market_companies', 'bin_iin')->ignore($marketCompanyId),
            ],

            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:32'],

            'messenger_type' => [
                'nullable',
                'string',
                'max:32',
                Rule::in(['whatsapp', 'telegram', 'wechat', 'viber']),
            ],

            'messenger_contact' => ['nullable', 'string', 'max:191'],

            'country' => ['nullable', 'string', 'size:2'],
            'city' => ['nullable', 'string', 'max:128'],

            'legal_address' => ['nullable', 'string'],
            'actual_address' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле названия компании обязательно для заполнения.',
            'name.max' => 'Название компании не должно превышать 255 символов.',

            'brand_name.max' => 'Бренд компании не должен превышать 255 символов.',
            'legal_name.max' => 'Юридическое название компании не должно превышать 255 символов.',

            'slug.regex' => 'Slug может содержать только строчные латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Компания с таким slug уже существует.',
            'slug.max' => 'Slug не должен превышать 191 символ.',

            'external_id.max' => 'Внешний ID не должен превышать 191 символ.',

            'company_type.max' => 'Тип компании не должен превышать 64 символа.',
            'tax_regime.max' => 'Налоговый режим не должен превышать 64 символа.',

            'bin_iin.unique' => 'Компания с таким БИН/ИИН уже существует.',
            'bin_iin.max' => 'БИН/ИИН не должен превышать 32 символа.',

            'email.email' => 'Введите корректный email адрес.',
            'email.max' => 'Email не должен превышать 191 символ.',

            'phone.max' => 'Телефон не должен превышать 32 символа.',

            'messenger_type.in' => 'Допустимые мессенджеры: whatsapp, telegram, wechat, viber.',
            'messenger_type.max' => 'Тип мессенджера не должен превышать 32 символа.',
            'messenger_contact.max' => 'Контакт мессенджера не должен превышать 191 символ.',

            'country.size' => 'Код страны должен состоять ровно из 2 символов.',
            'city.max' => 'Название города не должно превышать 128 символов.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',
        ];
    }
}
