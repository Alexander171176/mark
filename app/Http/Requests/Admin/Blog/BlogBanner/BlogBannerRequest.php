<?php

namespace App\Http\Requests\Admin\Blog\BlogBanner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class BlogBannerRequest extends FormRequest
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
                'link' => $this->normalizeNullableText(Arr::get($translation, 'link')),
                'short' => $this->normalizeNullableString(Arr::get($translation, 'short')),
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

            'comment' => $this->normalizeNullableString($this->input('comment')),

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $bannerId = $this->route('blogBanner')?->id
            ?? $this->route('blogBanner')
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

                'comment' => ['nullable', 'string', 'max:255'],

                /*
                 * Изображения
                 */
                'images' => ['nullable', 'array'],

                'images.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('blog_banner_images', 'id'),
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
                'deletedImages.*' => ['integer', 'exists:blog_banner_images,id'],

                /*
                 * Переводы
                 */
                'translations' => ['required', 'array', 'min:1'],
                'translations.*' => ['required', 'array'],

                'translations.*.title' => ['required', 'string', 'max:255'],
                'translations.*.link' => ['nullable', 'string'],
                'translations.*.short' => ['nullable', 'string', 'max:255'],
            ] + $this->localeRules($availableLocales);
    }

    public function messages(): array
    {
        return [
            /*
             * Общие поля
             */
            'user_id.required' => 'Необходимо указать владельца баннера.',
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

            'comment.max' => 'Служебный комментарий не должен превышать 255 символов.',

            /*
             * Изображения
             */
            'images.*.id.exists' => 'Одно из изображений баннера не найдено.',
            'images.*.id.prohibited_if' => 'При создании баннера нельзя передавать существующий ID изображения.',
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

            'translations.*.title.required' => 'Название баннера обязательно для каждой локали.',
            'translations.*.title.max' => 'Название баннера не должно превышать 255 символов.',
            'translations.*.short.max' => 'Краткий текст не должен превышать 255 символов.',
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
