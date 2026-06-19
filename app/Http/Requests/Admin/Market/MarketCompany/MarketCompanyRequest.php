<?php

namespace App\Http\Requests\Admin\Market\MarketCompany;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
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
        $supportedLocales = config('app.available_locales', ['ru']);

        $translations = $this->input('translations', []);

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (!in_array($locale, $supportedLocales, true)) {
                continue;
            }

            $preparedTranslations[$locale] = [
                'title' => $this->normalizeNullableString(Arr::get($translation, 'title')),
                'subtitle' => $this->normalizeNullableString(Arr::get($translation, 'subtitle')),
                'short' => $this->normalizeNullableString(Arr::get($translation, 'short')),
                'description' => $this->normalizeNullableText(Arr::get($translation, 'description')),
                'meta_title' => $this->normalizeNullableString(Arr::get($translation, 'meta_title')),
                'meta_keywords' => $this->normalizeNullableString(Arr::get($translation, 'meta_keywords')),
                'meta_desc' => $this->normalizeNullableText(Arr::get($translation, 'meta_desc')),
            ];
        }

        if ($this->hasFile('logo')) { $this->offsetUnset('logo'); }
        if ($this->hasFile('signature')) { $this->offsetUnset('signature'); }
        if ($this->hasFile('stamp')) { $this->offsetUnset('stamp'); }

        $this->merge([
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

            'company_type' => $this->normalizeNullableString($this->input('company_type')) ?: 'company',
            'bin_iin' => $this->normalizeNullableString($this->input('bin_iin')),
            'legal_name' => $this->normalizeNullableString($this->input('legal_name')),
            'director_name' => $this->normalizeNullableString($this->input('director_name')),

            'email' => $this->normalizeNullableString($this->input('email')),
            'phone' => $this->normalizeNullableString($this->input('phone')),
            'website' => $this->normalizeNullableString($this->input('website')),

            'country' => $this->normalizeNullableString($this->input('country')),
            'region' => $this->normalizeNullableString($this->input('region')),
            'city' => $this->normalizeNullableString($this->input('city')),
            'legal_address' => $this->normalizeNullableString($this->input('legal_address')),
            'actual_address' => $this->normalizeNullableString($this->input('actual_address')),

            'latitude' => $this->filled('latitude') ? $this->input('latitude') : null,
            'longitude' => $this->filled('longitude') ? $this->input('longitude') : null,

            'bank_name' => $this->normalizeNullableString($this->input('bank_name')),
            'bank_account' => $this->normalizeNullableString($this->input('bank_account')),
            'bank_account_secondary' => $this->normalizeNullableString($this->input('bank_account_secondary')),
            'bank_bik' => $this->normalizeNullableString($this->input('bank_bik')),
            'bank_iban' => $this->normalizeNullableString($this->input('bank_iban')),

            'vat_enabled' => filter_var($this->input('vat_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'vat_rate' => $this->filled('vat_rate') ? $this->input('vat_rate') : null,

            'social_links' => is_array($this->input('social_links')) ? $this->input('social_links') : null,

            'sort' => $this->filled('sort') ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'left' => filter_var($this->input('left', false), FILTER_VALIDATE_BOOLEAN),
            'main' => filter_var($this->input('main', false), FILTER_VALIDATE_BOOLEAN),
            'right' => filter_var($this->input('right', false), FILTER_VALIDATE_BOOLEAN),

            'status' => $this->normalizeNullableString($this->input('status')) ?: 'draft',

            'moderation_status' => $this->filled('moderation_status')
                ? (int) $this->input('moderation_status')
                : 0,

            'moderated_by' => $this->filled('moderated_by') ? (int) $this->input('moderated_by') : null,
            'moderated_at' => $this->filled('moderated_at') ? $this->input('moderated_at') : null,
            'moderation_note' => $this->normalizeNullableString($this->input('moderation_note')),

            'published_at' => $this->filled('published_at') ? $this->input('published_at') : null,
            'show_from_at' => $this->filled('show_from_at') ? $this->input('show_from_at') : null,
            'show_to_at' => $this->filled('show_to_at') ? $this->input('show_to_at') : null,

            'views' => $this->filled('views') ? (int) $this->input('views') : 0,

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $companyId = $this->route('marketCompany')?->id
            ?? $this->route('marketCompany')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                'user_id' => ['required', 'integer', 'exists:users,id'],

                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('market_companies', 'url')
                        ->where(fn ($query) => $query->where('user_id', $this->input('user_id')))
                        ->ignore($companyId),
                ],

                'company_type' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in(['company', 'entrepreneur', 'individual']),
                ],

                'bin_iin' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('market_companies', 'bin_iin')->ignore($companyId),
                ],

                'legal_name' => ['nullable', 'string', 'max:255'],
                'director_name' => ['nullable', 'string', 'max:255'],

                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'website' => ['nullable', 'url', 'max:255'],

                'logo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

                'signature' => [
                    'nullable',
                    'image',
                    'mimes:png',
                    'max:5120',
                ],

                'stamp' => [
                    'nullable',
                    'image',
                    'mimes:png',
                    'max:5120',
                ],

                'country' => ['nullable', 'string', 'max:100'],
                'region' => ['nullable', 'string', 'max:100'],
                'city' => ['nullable', 'string', 'max:100'],
                'legal_address' => ['nullable', 'string', 'max:500'],
                'actual_address' => ['nullable', 'string', 'max:500'],

                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],

                'bank_name' => ['nullable', 'string', 'max:255'],
                'bank_account' => ['nullable', 'string', 'max:100'],
                'bank_account_secondary' => ['nullable', 'string', 'max:100'],
                'bank_bik' => ['nullable', 'string', 'max:50'],
                'bank_iban' => ['nullable', 'string', 'max:100'],

                'vat_enabled' => ['nullable', 'boolean'],
                'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],

                'social_links' => ['nullable', 'array'],

                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],
                'left' => ['nullable', 'boolean'],
                'main' => ['nullable', 'boolean'],
                'right' => ['nullable', 'boolean'],

                'status' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::in(['draft', 'published', 'archived']),
                ],

                'moderation_status' => [
                    'nullable',
                    'integer',
                    Rule::in([0, 1, 2]),
                ],

                'moderated_by' => ['nullable', 'integer', 'exists:users,id'],
                'moderated_at' => ['nullable', 'date'],
                'moderation_note' => ['nullable', 'string', 'max:500'],

                'published_at' => ['nullable', 'date'],
                'show_from_at' => ['nullable', 'date'],
                'show_to_at' => ['nullable', 'date', 'after_or_equal:show_from_at'],

                'views' => ['nullable', 'integer', 'min:0'],

                'translations' => ['required', 'array', 'min:1'],
                'translations.*' => ['required', 'array'],

                'translations.*.title' => ['required', 'string', 'max:255'],
                'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
                'translations.*.short' => ['nullable', 'string', 'max:255'],
                'translations.*.description' => ['nullable', 'string'],
                'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
                'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
                'translations.*.meta_desc' => ['nullable', 'string'],
            ] + $this->localeRules($availableLocales);
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо указать владельца компании.',
            'user_id.exists' => 'Указанный владелец компании не найден.',

            'url.required' => 'Поле URL обязательно для заполнения.',
            'url.max' => 'URL не должен превышать 500 символов.',
            'url.regex' => 'URL может содержать только строчные латинские буквы, цифры и дефисы.',
            'url.unique' => 'Компания с таким URL уже существует у данного владельца.',

            'company_type.required' => 'Необходимо указать тип компании.',
            'company_type.in' => 'Недопустимый тип компании.',

            'bin_iin.unique' => 'Компания с таким БИН/ИИН уже зарегистрирована.',
            'bin_iin.max' => 'БИН/ИИН не должен превышать 20 символов.',

            'legal_name.max' => 'Юридическое название не должно превышать 255 символов.',
            'director_name.max' => 'ФИО руководителя не должно превышать 255 символов.',

            'email.email' => 'Email компании имеет неверный формат.',
            'email.max' => 'Email не должен превышать 255 символов.',
            'phone.max' => 'Телефон не должен превышать 50 символов.',
            'website.url' => 'Сайт компании должен быть корректным URL.',
            'website.max' => 'Сайт компании не должен превышать 255 символов.',

            'logo.image' => 'Логотип должен быть изображением.',
            'logo.mimes' => 'Логотип должен быть в формате JPG, JPEG, PNG или WEBP.',
            'logo.max' => 'Размер логотипа не должен превышать 5 МБ.',

            'signature.image' => 'Подпись должна быть изображением.',
            'signature.mimes' => 'Подпись должна быть только в формате PNG.',
            'signature.max' => 'Размер подписи не должен превышать 5 МБ.',

            'stamp.image' => 'Печать должна быть изображением.',
            'stamp.mimes' => 'Печать должна быть только в формате PNG.',
            'stamp.max' => 'Размер печати не должен превышать 5 МБ.',

            'country.max' => 'Страна не должна превышать 100 символов.',
            'region.max' => 'Регион не должен превышать 100 символов.',
            'city.max' => 'Город не должен превышать 100 символов.',
            'legal_address.max' => 'Юридический адрес не должен превышать 500 символов.',
            'actual_address.max' => 'Фактический адрес не должен превышать 500 символов.',

            'latitude.numeric' => 'Широта должна быть числом.',
            'latitude.between' => 'Широта должна быть в диапазоне от -90 до 90.',
            'longitude.numeric' => 'Долгота должна быть числом.',
            'longitude.between' => 'Долгота должна быть в диапазоне от -180 до 180.',

            'bank_name.max' => 'Название банка не должно превышать 255 символов.',
            'bank_account.max' => 'Банковский счёт №1 не должен превышать 100 символов.',
            'bank_account_secondary.max' => 'Банковский счёт №2 не должен превышать 100 символов.',
            'bank_bik.max' => 'БИК банка не должен превышать 50 символов.',
            'bank_iban.max' => 'IBAN счёт не должен превышать 100 символов.',

            'vat_enabled.boolean' => 'Поле НДС должно быть логическим значением.',
            'vat_rate.numeric' => 'Ставка НДС должна быть числом.',
            'vat_rate.min' => 'Ставка НДС не может быть меньше 0.',
            'vat_rate.max' => 'Ставка НДС не может быть больше 100.',

            'social_links.array' => 'Социальные сети должны быть массивом.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',
            'left.boolean' => 'Поле left должно быть логическим значением.',
            'main.boolean' => 'Поле main должно быть логическим значением.',
            'right.boolean' => 'Поле right должно быть логическим значением.',

            'status.in' => 'Недопустимое значение статуса публикации.',

            'moderation_status.in' => 'Недопустимое значение статуса модерации.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',
            'moderation_note.max' => 'Комментарий модератора не должен превышать 500 символов.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть меньше 0.',

            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название компании обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название компании не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок компании не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание компании не должно превышать 255 символов.',
            'translations.*.meta_title.max' => 'Meta title не должен превышать 255 символов.',
            'translations.*.meta_keywords.max' => 'Meta keywords не должны превышать 255 символов.',
        ];
    }

    protected function localeRules(array $availableLocales): array
    {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.$locale"] = ['sometimes', 'array'];
        }

        return $rules;
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeNullableText(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
