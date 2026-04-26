<?php

namespace App\Http\Requests\Admin\Blog\BlogVideo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogVideoRequest extends FormRequest
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
            'activity' => filter_var(
                    $this->input('activity', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'is_private' => filter_var(
                    $this->input('is_private', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'left' => filter_var(
                    $this->input('left', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'main' => filter_var(
                    $this->input('main', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'right' => filter_var(
                    $this->input('right', false),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'moderation_status' => $this->filled('moderation_status')
                ? (int) $this->input('moderation_status')
                : 0,

            'moderated_by' => $this->filled('moderated_by') ? (int) $this->input('moderated_by') : null,
            'moderated_at' => $this->filled('moderated_at') ? $this->input('moderated_at') : null,
            'moderation_note' => $this->normalizeNullableString($this->input('moderation_note')),

            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

            'published_at' => $this->filled('published_at') ? $this->input('published_at') : null,
            'show_from_at' => $this->filled('show_from_at') ? $this->input('show_from_at') : null,
            'show_to_at' => $this->filled('show_to_at') ? $this->input('show_to_at') : null,

            'duration' => $this->filled('duration') ? (int) $this->input('duration') : null,
            'source_type' => $this->filled('source_type') ? trim((string) $this->input('source_type')) : 'local',
            'embed_code' => $this->normalizeNullableText($this->input('embed_code')),
            'external_video_id' => $this->normalizeNullableString($this->input('external_video_id')),

            'views' => $this->filled('views') ? (int) $this->input('views') : 0,

            'related_videos' => is_array($this->input('related_videos'))
                ? array_values(array_filter(array_map(function ($item) {
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
                }, $this->input('related_videos')), fn ($item) => is_array($item) && !is_null($item['id'])))
                : [],

            'translations' => $preparedTranslations,
        ]);

        // Нормализация по source_type
        if ($this->input('source_type') !== 'code') {
            $this->merge(['embed_code' => null]);
        }

        if (!in_array($this->input('source_type'), ['youtube', 'vimeo'], true)) {
            $this->merge(['external_video_id' => null]);
        }

        if ($this->input('source_type') !== 'local' && !$this->hasFile('video_file')) {
            $this->merge(['video_file' => null]);
        }
    }

    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        $videoId = $this->route('blogVideo')?->id
            ?? $this->route('blogVideo')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                /*
                 * Общие поля
                 */
                'user_id' => ['required', 'integer', 'exists:users,id'],

                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],
                'is_private' => ['nullable', 'boolean'],

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

                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('blog_videos', 'url')
                        ->where(fn ($query) => $query->where('user_id', $this->input('user_id')))
                        ->ignore($videoId),
                ],

                'published_at' => ['nullable', 'date_format:Y-m-d'],
                'show_from_at' => ['nullable', 'date'],
                'show_to_at' => ['nullable', 'date', 'after_or_equal:show_from_at'],

                'duration' => ['nullable', 'integer', 'min:0'],

                'source_type' => ['required', Rule::in(['local', 'youtube', 'vimeo', 'code'])],

                'embed_code' => [
                    Rule::requiredIf(fn () => $this->input('source_type') === 'code'),
                    'nullable',
                    'string',
                ],

                'external_video_id' => [
                    Rule::requiredIf(fn () => in_array($this->input('source_type'), ['youtube', 'vimeo'], true)),
                    'nullable',
                    'string',
                    'max:500',
                ],

                'video_file' => [
                    Rule::requiredIf(fn () => $this->input('source_type') === 'local' && $isCreating),
                    'nullable',
                    'file',
                    'mimes:mp4,mov,ogg,qt,webm,avi,mpeg,wmv',
                    'max:204800',
                ],

                'views' => ['nullable', 'integer', 'min:0'],

                /*
                 * Связанные видео
                 */
                'related_videos' => ['nullable', 'array'],
                'related_videos.*.id' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:blog_videos,id',
                    Rule::when($videoId, ['not_in:' . $videoId]),
                ],
                'related_videos.*.sort' => ['nullable', 'integer', 'min:0'],

                /*
                 * Изображения
                 */
                'images' => ['nullable', 'array'],
                'images.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('blog_video_images', 'id'),
                    Rule::prohibitedIf(fn () => $isCreating),
                ],
                'images.*.order' => ['nullable', 'integer', 'min:0'],
                'images.*.alt' => ['nullable', 'string', 'max:255'],
                'images.*.caption' => ['nullable', 'string', 'max:255'],
                'images.*.file' => [
                    'nullable',
                    'file',
                    'image',
                    'mimes:jpeg,jpg,png,gif,svg,webp',
                    'max:10240',
                    'required_without:images.*.id',
                ],

                'deletedImages' => ['sometimes', 'array'],
                'deletedImages.*' => ['integer', 'exists:blog_video_images,id'],

                /*
                 * Переводы
                 */
                'translations' => ['required', 'array', 'min:1'],
                'translations.*' => ['required', 'array'],

                'translations.*.title' => ['required', 'string', 'max:255'],
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
            'user_id.required' => 'Необходимо указать владельца видео.',
            'user_id.exists' => 'Указанный владелец не найден.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',
            'is_private.boolean' => 'Поле приватности должно быть логическим значением.',
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
            'url.unique' => 'Видео с таким URL уже существует у данного владельца.',

            'published_at.date_format' => 'Дата публикации должна быть в формате YYYY-MM-DD.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            'duration.integer' => 'Длительность должна быть числом.',
            'duration.min' => 'Длительность не может быть меньше 0.',

            'source_type.required' => 'Необходимо указать источник видео.',
            'source_type.in' => 'Недопустимый тип источника видео.',

            'embed_code.required_if' => 'Embed-код обязателен для типа источника code.',
            'external_video_id.required_if' => 'Внешний идентификатор обязателен для YouTube или Vimeo.',
            'external_video_id.max' => 'Внешний идентификатор не должен превышать 500 символов.',

            'video_file.required_if' => 'Необходимо загрузить видеофайл для локального источника.',
            'video_file.file' => 'Загруженный объект должен быть файлом.',
            'video_file.mimes' => 'Допустимые форматы видео: mp4, mov, ogg, qt, webm, avi, mpeg, wmv.',
            'video_file.max' => 'Размер видеофайла не должен превышать 200 МБ.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть меньше 0.',

            /*
             * Связанные видео
             */
            'related_videos.*.id.required' => 'Необходимо указать связанное видео.',
            'related_videos.*.id.exists' => 'Одно из связанных видео не найдено.',
            'related_videos.*.id.distinct' => 'Связанные видео не должны повторяться.',
            'related_videos.*.id.not_in' => 'Видео не может быть связано само с собой.',
            'related_videos.*.sort.integer' => 'Сортировка связанного видео должна быть числом.',
            'related_videos.*.sort.min' => 'Сортировка связанного видео не может быть меньше 0.',

            /*
             * Изображения
             */
            'images.*.id.exists' => 'Одно из изображений видео не найдено.',
            'images.*.id.prohibited_if' => 'При создании видео нельзя передавать существующий ID изображения.',
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
            'translations.min' => 'Необходимо добавить хотя бы одну локаль.',

            'translations.*.title.required' => 'Название видео обязательно для каждой локали.',
            'translations.*.title.max' => 'Название видео не должно превышать 255 символов.',
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
