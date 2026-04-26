<?php

namespace App\Http\Requests\Admin\Blog\BlogArticle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogArticleRequest extends FormRequest
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
                'pseudonym' => $this->normalizeNullableString(Arr::get($translation, 'pseudonym')),
                'meta_title' => $this->normalizeNullableString(Arr::get($translation, 'meta_title')),
                'meta_keywords' => $this->normalizeNullableString(Arr::get($translation, 'meta_keywords')),
                'meta_desc' => $this->normalizeNullableText(Arr::get($translation, 'meta_desc')),
            ];
        }

        $this->merge([
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'sort' => $this->filled('sort') ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'left' => filter_var($this->input('left', false), FILTER_VALIDATE_BOOLEAN),
            'main' => filter_var($this->input('main', false), FILTER_VALIDATE_BOOLEAN),
            'right' => filter_var($this->input('right', false), FILTER_VALIDATE_BOOLEAN),

            'moderation_status' => $this->filled('moderation_status')
                ? (int) $this->input('moderation_status')
                : 0,

            'moderated_by' => $this->filled('moderated_by') ? (int) $this->input('moderated_by') : null,
            'moderated_at' => $this->filled('moderated_at') ? $this->input('moderated_at') : null,
            'moderation_note' => $this->normalizeNullableString($this->input('moderation_note')),

            'img' => $this->normalizeNullableText($this->input('img')),

            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

            'published_at' => $this->filled('published_at') ? $this->input('published_at') : null,
            'show_from_at' => $this->filled('show_from_at') ? $this->input('show_from_at') : null,
            'show_to_at' => $this->filled('show_to_at') ? $this->input('show_to_at') : null,

            'views' => $this->filled('views') ? (int) $this->input('views') : 0,

            'rubrics' => is_array($this->input('rubrics')) ? array_values(array_filter(
                array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $this->input('rubrics')),
                fn ($id) => !is_null($id)
            )) : [],

            'tags' => is_array($this->input('tags')) ? array_values(array_filter(
                array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $this->input('tags')),
                fn ($id) => !is_null($id)
            )) : [],

            'related_articles' => is_array($this->input('related_articles')) ? array_values(array_filter(
                array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $this->input('related_articles')),
                fn ($id) => !is_null($id)
            )) : [],

            'videos' => is_array($this->input('videos')) ? array_values(array_filter(
                array_map(function ($item) {
                    if (is_array($item)) {
                        return [
                            'id' => isset($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null,
                            'sort' => isset($item['sort']) && is_numeric($item['sort']) ? (int) $item['sort'] : 0,
                        ];
                    }

                    if (is_numeric($item)) {
                        return [
                            'id' => (int) $item,
                            'sort' => 0,
                        ];
                    }

                    return null;
                }, $this->input('videos')),
                fn ($item) => is_array($item) && !is_null($item['id'])
            )) : [],

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $articleId = $this->route('blogArticle')?->id
            ?? $this->route('blogArticle')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                /*
                 * Общие поля
                 */
                'user_id' => ['required', 'integer', 'exists:users,id'],

                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],
                'left' => ['nullable', 'boolean'],
                'main' => ['nullable', 'boolean'],
                'right' => ['nullable', 'boolean'],

                'moderation_status' => [
                    'nullable',
                    'integer',
                    Rule::in([0, 1, 2]),
                ],

                'moderated_by' => ['nullable', 'integer', 'exists:users,id'],
                'moderated_at' => ['nullable', 'date'],
                'moderation_note' => ['nullable', 'string', 'max:500'],

                'img' => ['nullable', 'string'],

                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('blog_articles', 'url')
                        ->where(fn ($query) => $query->where('user_id', $this->input('user_id')))
                        ->ignore($articleId),
                ],

                'published_at' => ['nullable', 'date'],
                'show_from_at' => ['nullable', 'date'],
                'show_to_at' => ['nullable', 'date', 'after_or_equal:show_from_at'],

                'views' => ['nullable', 'integer', 'min:0'],

                /*
                 * Связи
                 */
                'rubrics' => ['nullable', 'array'],
                'rubrics.*' => ['integer', 'distinct', 'exists:blog_rubrics,id'],

                'tags' => ['nullable', 'array'],
                'tags.*' => ['integer', 'distinct', 'exists:blog_tags,id'],

                'related_articles' => ['nullable', 'array'],
                'related_articles.*' => [
                    'integer',
                    'distinct',
                    'different:id',
                    'exists:blog_articles,id',
                ],

                'videos' => ['nullable', 'array'],
                'videos.*.id' => ['required', 'integer', 'distinct', 'exists:blog_videos,id'],
                'videos.*.sort' => ['nullable', 'integer', 'min:0'],

                /*
                 * Изображения
                 */
                'images' => ['nullable', 'array'],
                'images.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('blog_article_images', 'id'),
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
                'deletedImages.*' => ['integer', 'exists:blog_article_images,id'],

                /*
                 * Переводы
                 */
                'translations' => ['required', 'array', 'min:1'],
                'translations.*' => ['required', 'array'],

                'translations.*.title' => ['required', 'string', 'max:255'],
                'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
                'translations.*.short' => ['nullable', 'string', 'max:255'],
                'translations.*.description' => ['nullable', 'string'],
                'translations.*.pseudonym' => ['nullable', 'string', 'max:255'],
                'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
                'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
                'translations.*.meta_desc' => ['nullable', 'string'],
            ] + $this->localeRules($availableLocales);
    }

    public function messages(): array
    {
        return [
            /*
             * Общие поля
             */
            'user_id.required' => 'Необходимо указать владельца статьи.',
            'user_id.exists' => 'Указанный владелец не найден.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',
            'left.boolean' => 'Поле left должно быть логическим значением.',
            'main.boolean' => 'Поле main должно быть логическим значением.',
            'right.boolean' => 'Поле right должно быть логическим значением.',

            'moderation_status.in' => 'Недопустимое значение статуса модерации.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',
            'moderation_note.max' => 'Комментарий модератора не должен превышать 500 символов.',

            'url.required' => 'Поле URL обязательно для заполнения.',
            'url.max' => 'URL не должен превышать 500 символов.',
            'url.regex' => 'URL может содержать только строчные латинские буквы, цифры и дефисы.',
            'url.unique' => 'Статья с таким URL уже существует у данного владельца.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть меньше 0.',

            /*
             * Связи
             */
            'rubrics.*.exists' => 'Одна из выбранных рубрик не найдена.',
            'rubrics.*.distinct' => 'Рубрики не должны повторяться.',

            'tags.*.exists' => 'Один из выбранных тегов не найден.',
            'tags.*.distinct' => 'Теги не должны повторяться.',

            'related_articles.*.exists' => 'Одна из связанных статей не найдена.',
            'related_articles.*.distinct' => 'Связанные статьи не должны повторяться.',
            'related_articles.*.different' => 'Статья не может быть связана сама с собой.',

            'videos.*.id.required' => 'Необходимо указать видео.',
            'videos.*.id.exists' => 'Одно из выбранных видео не найдено.',
            'videos.*.id.distinct' => 'Видео не должны повторяться.',
            'videos.*.sort.integer' => 'Сортировка видео должна быть числом.',
            'videos.*.sort.min' => 'Сортировка видео не может быть меньше 0.',

            /*
             * Изображения
             */
            'images.*.id.exists' => 'Одно из изображений статьи не найдено.',
            'images.*.id.prohibited_if' => 'При создании статьи нельзя передавать существующий ID изображения.',
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

            /*
             * Переводы
             */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название статьи обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название статьи не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
            'translations.*.pseudonym.max' => 'Псевдоним не должен превышать 255 символов.',
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
