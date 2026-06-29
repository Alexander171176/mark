<?php

namespace App\Http\Requests\Admin\Market\MarketTag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketTagRequest extends FormRequest
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

        $this->merge([
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

            'icon' => $this->normalizeNullableText($this->input('icon')),
            'color' => $this->normalizeNullableString($this->input('color')),

            'sort' => $this->filled('sort') ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),

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
        $tagId = $this->route('marketTag')?->id
            ?? $this->route('marketTag')
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
                    Rule::unique('market_tags', 'url')->ignore($tagId),
                ],

                'icon' => ['nullable', 'string'],
                'color' => ['nullable', 'string', 'max:50'],

                /* Отображение */
                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],

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
            'user_id.required' => 'Необходимо указать создателя тега.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'url.required' => 'Поле URL обязательно для заполнения.',
            'url.max' => 'URL не должен превышать 500 символов.',
            'url.regex' => 'URL может содержать только строчные латинские буквы, цифры и дефисы.',
            'url.unique' => 'Тег с таким URL уже существует.',

            'color.max' => 'Цвет тега не должен превышать 50 символов.',

            /* Отображение */
            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',

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

            /* Переводы */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название тега обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название тега не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
            'translations.*.meta_title.max' => 'Meta title не должен превышать 255 символов.',
            'translations.*.meta_keywords.max' => 'Meta keywords не должны превышать 255 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Создатель',
            'url' => 'URL',
            'icon' => 'Иконка',
            'color' => 'Цвет',
            'sort' => 'Сортировка',
            'activity' => 'Активность',
            'status' => 'Статус публикации',
            'moderation_status' => 'Статус модерации',
            'moderated_by' => 'Модератор',
            'moderated_at' => 'Дата модерации',
            'moderation_note' => 'Комментарий модератора',
            'published_at' => 'Дата публикации',
            'show_from_at' => 'Начало показа',
            'show_to_at' => 'Окончание показа',
            'views' => 'Просмотры',
            'translations' => 'Переводы',
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
