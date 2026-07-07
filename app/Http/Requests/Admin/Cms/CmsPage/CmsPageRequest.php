<?php

namespace App\Http\Requests\Admin\Cms\CmsPage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CmsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $supportedLocales = config('app.available_locales', ['ru']);

        $translations = $this->input('translations', []);

        if (! is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (! in_array($locale, $supportedLocales, true)) {
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

            'parent_id' => $this->filled('parent_id')
                ? (int) $this->input('parent_id')
                : null,

            'level' => $this->filled('level')
                ? (int) $this->input('level')
                : 1,

            'url' => $this->normalizeUrl($this->input('url')),

            'icon' => $this->normalizeNullableText($this->input('icon')),

            'in_menu' => filter_var(
                    $this->input('in_menu', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'in_footer' => filter_var(
                    $this->input('in_footer', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'show_content' => filter_var(
                    $this->input('show_content', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'show_seo' => filter_var(
                    $this->input('show_seo', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 0,

            'activity' => filter_var(
                    $this->input('activity', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'status' => $this->normalizeNullableString($this->input('status')) ?: 'draft',

            'published_at' => $this->filled('published_at')
                ? $this->input('published_at')
                : null,

            'show_from_at' => $this->filled('show_from_at')
                ? $this->input('show_from_at')
                : null,

            'show_to_at' => $this->filled('show_to_at')
                ? $this->input('show_to_at')
                : null,

            'views' => $this->filled('views')
                ? (int) $this->input('views')
                : 0,

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $pageId = $this->route('cmsPage')?->id
            ?? $this->route('cmsPage')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                'user_id' => ['required', 'integer', 'exists:users,id'],

                'parent_id' => [
                    'nullable',
                    'integer',
                    Rule::notIn([(int) $pageId]),
                    Rule::exists('cms_pages', 'id'),
                ],

                'level' => ['nullable', 'integer', 'min:1', 'max:3'],

                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^\/[a-z0-9]+(?:-[a-z0-9]+)*(?:\/[a-z0-9]+(?:-[a-z0-9]+)*)*$/',
                    Rule::unique('cms_pages', 'url')->ignore($pageId),
                ],

                'icon' => ['nullable', 'string'],

                'in_menu' => ['nullable', 'boolean'],
                'in_footer' => ['nullable', 'boolean'],
                'show_content' => ['nullable', 'boolean'],
                'show_seo' => ['nullable', 'boolean'],

                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],

                'status' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::in(['draft', 'published', 'archived']),
                ],

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
            'user_id.required' => 'Необходимо указать создателя страницы.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'parent_id.exists' => 'Родительская страница не найдена.',
            'parent_id.not_in' => 'Страница не может быть родителем самой себя.',

            'level.integer' => 'Уровень вложенности должен быть числом.',
            'level.min' => 'Уровень вложенности не может быть меньше 1.',
            'level.max' => 'Уровень вложенности не может быть больше 3.',

            'url.required' => 'Поле URL обязательно для заполнения.',
            'url.max' => 'URL не должен превышать 500 символов.',
            'url.regex' => 'URL должен начинаться с "/" и содержать только строчные латинские буквы, цифры, дефисы и вложенные сегменты.',
            'url.unique' => 'Страница с таким URL уже существует.',

            'in_menu.boolean' => 'Поле "показывать в меню" должно быть логическим значением.',
            'in_footer.boolean' => 'Поле "показывать в футере" должно быть логическим значением.',
            'show_content.boolean' => 'Поле "показывать свой контент" должно быть логическим значением.',
            'show_seo.boolean' => 'Поле "показывать своё SEO" должно быть логическим значением.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',
            'activity.boolean' => 'Поле активности должно быть логическим значением.',

            'status.in' => 'Недопустимое значение статуса публикации.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть меньше 0.',

            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название страницы обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название страницы не должно превышать 255 символов.',
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

    protected function normalizeUrl(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = trim($value, '/');

        return '/' . collect(explode('/', $value))
                ->filter()
                ->map(fn (string $segment) => Str::slug($segment))
                ->implode('/');
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
