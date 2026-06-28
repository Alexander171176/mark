<?php

namespace App\Http\Requests\Admin\Market\MarketBrand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketBrandRequest extends FormRequest
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

        $socialLinks = $this->input('social_links', []);

        if (!is_array($socialLinks)) {
            $socialLinks = [];
        }

        $preparedSocialLinks = [];

        foreach ($socialLinks as $key => $value) {
            $key = trim((string) $key);
            $value = $this->normalizeNullableString($value);

            if ($key !== '' && !is_null($value)) {
                $preparedSocialLinks[$key] = $value;
            }
        }

        if ($this->hasFile('logo')) {
            $this->offsetUnset('logo');
        }

        $this->merge([
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

            'website' => $this->normalizeNullableString($this->input('website')),
            'icon' => $this->normalizeNullableText($this->input('icon')),
            'social_links' => $preparedSocialLinks,

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
            'moderation_note' => $this->normalizeNullableText($this->input('moderation_note')),

            'published_at' => $this->filled('published_at') ? $this->input('published_at') : null,
            'show_from_at' => $this->filled('show_from_at') ? $this->input('show_from_at') : null,
            'show_to_at' => $this->filled('show_to_at') ? $this->input('show_to_at') : null,

            'views' => $this->filled('views') ? (int) $this->input('views') : 0,

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $brandId = $this->route('marketBrand')?->id
            ?? $this->route('marketBrand')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                /* Основные поля */
                'user_id' => ['required', 'integer', 'exists:users,id'],

                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('market_brands', 'url')->ignore($brandId),
                ],

                'website' => ['nullable', 'string', 'max:500'],
                'icon' => ['nullable', 'string'],

                /* Логотип бренда */
                'logo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

                /* Социальные сети */
                'social_links' => ['nullable', 'array'],
                'social_links.*' => ['nullable', 'string', 'max:255'],

                /* Отображение */
                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],
                'left' => ['nullable', 'boolean'],
                'main' => ['nullable', 'boolean'],
                'right' => ['nullable', 'boolean'],

                /* Публикация */
                'status' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::in(['draft', 'published', 'archived']),
                ],

                /* Модерация */
                'moderation_status' => [
                    'nullable',
                    'integer',
                    Rule::in([0, 1, 2]),
                ],

                'moderated_by' => ['nullable', 'integer', 'exists:users,id'],
                'moderated_at' => ['nullable', 'date'],
                'moderation_note' => ['nullable', 'string'],

                /* Публикация и окно показа */
                'published_at' => ['nullable', 'date'],
                'show_from_at' => ['nullable', 'date'],
                'show_to_at' => ['nullable', 'date', 'after_or_equal:show_from_at'],

                /* Счётчики */
                'views' => ['nullable', 'integer', 'min:0'],

                /* Изображения бренда */
                'images' => ['nullable', 'array'],
                'images.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('market_brand_images', 'id'),
                    Rule::prohibitedIf(fn () => $this->isMethod('POST')),
                ],
                'images.*.order' => ['nullable', 'integer', 'min:0'],
                'images.*.alt' => ['nullable', 'string', 'max:255'],
                'images.*.caption' => ['nullable', 'string', 'max:255'],
                'images.*.file' => [
                    'nullable',
                    'required_without:images.*.id',
                    'file',
                    'image',
                    'mimes:jpeg,jpg,png,gif,svg,webp',
                    'max:10240',
                ],

                'deletedImages' => ['sometimes', 'array'],
                'deletedImages.*' => ['integer', 'exists:market_brand_images,id'],

                /* Переводы */
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
            /* Основные поля */
            'user_id.required' => 'Необходимо указать создателя бренда.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'url.required' => 'Поле URL обязательно для заполнения.',
            'url.max' => 'URL не должен превышать 500 символов.',
            'url.regex' => 'URL может содержать только строчные латинские буквы, цифры и дефисы.',
            'url.unique' => 'Бренд с таким URL уже существует.',

            'website.max' => 'Сайт бренда не должен превышать 500 символов.',

            /* Логотип */
            'logo.image' => 'Логотип должен быть изображением.',
            'logo.mimes' => 'Логотип должен быть в формате JPG, JPEG, PNG или WEBP.',
            'logo.max' => 'Размер логотипа не должен превышать 5 МБ.',

            /* Социальные сети */
            'social_links.array' => 'Социальные сети должны быть массивом.',
            'social_links.*.max' => 'Ссылка социальной сети не должна превышать 255 символов.',

            /* Отображение */
            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',
            'left.boolean' => 'Поле left должно быть логическим значением.',
            'main.boolean' => 'Поле main должно быть логическим значением.',
            'right.boolean' => 'Поле right должно быть логическим значением.',

            /* Публикация */
            'status.in' => 'Недопустимое значение статуса публикации.',

            /* Модерация */
            'moderation_status.in' => 'Недопустимое значение статуса модерации.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',

            /* Публикация и окно показа */
            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            /* Счётчики */
            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть меньше 0.',

            /* Изображения */
            'images.*.id.exists' => 'Одно из изображений бренда не найдено.',
            'images.*.id.prohibited_if' => 'При создании бренда нельзя передавать существующий ID изображения.',
            'images.*.order.integer' => 'Порядок изображения должен быть числом.',
            'images.*.order.min' => 'Порядок изображения не может быть меньше 0.',
            'images.*.alt.max' => 'Alt текст изображения не должен превышать 255 символов.',
            'images.*.caption.max' => 'Подпись изображения не должна превышать 255 символов.',
            'images.*.file.required_without' => 'Необходимо загрузить файл изображения.',
            'images.*.file.file' => 'Загруженный объект должен быть файлом.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Допустимые форматы изображения: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max' => 'Размер изображения не должен превышать 10 МБ.',

            'deletedImages.*.exists' => 'Одно из удаляемых изображений не найдено.',

            /* Переводы */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название бренда обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название бренда не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
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
