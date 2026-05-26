<?php

namespace App\Http\Requests\Admin\School\Bundle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('slug') && is_string($this->input('slug'))) {
            $data['slug'] = Str::slug($this->input('slug'));
        }

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        foreach (['sort', 'views', 'likes'] as $field) {
            if ($this->has($field) && $this->input($field) !== null && is_numeric($this->input($field))) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if ($this->has('translations') && is_array($this->input('translations'))) {
            $data['translations'] = collect($this->input('translations'))
                ->map(function ($translation) {
                    if (isset($translation['locale']) && is_string($translation['locale'])) {
                        $translation['locale'] = mb_strtolower(trim($translation['locale']));
                    }

                    return $translation;
                })
                ->toArray();
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $bundleId = $this->route('school_bundle')?->id
            ?? $this->route('bundle')?->id
            ?? $this->route('schoolBundle')?->id
            ?? $this->input('id');

        return [
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_bundles', 'slug')->ignore($bundleId),
            ],

            'published_at' => ['nullable', 'date'],

            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],

            'meta' => ['nullable', 'array'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => ['required', 'string', 'max:10', 'distinct'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:160'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string', 'max:255'],

            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', Rule::exists('school_courses', 'id')],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_bundle_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('post')),
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
            'deletedImages.*' => ['integer', Rule::exists('school_bundle_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'activity.required' => 'Укажите активность набора.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'slug.required' => 'Укажите slug набора.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Набор с таким slug уже существует.',

            'published_at.date' => 'Дата публикации указана некорректно.',

            'views.integer' => 'Просмотры должны быть целым числом.',
            'views.min' => 'Просмотры не могут быть отрицательными.',
            'likes.integer' => 'Лайки должны быть целым числом.',
            'likes.min' => 'Лайки не могут быть отрицательными.',

            'meta.array' => 'Meta должен быть объектом JSON.',

            'translations.required' => 'Необходимо передать переводы набора.',
            'translations.*.locale.required' => 'Укажите локаль перевода.',
            'translations.*.locale.distinct' => 'Локали переводов не должны повторяться.',
            'translations.*.title.required' => 'Укажите название набора.',

            'course_ids.array' => 'Список курсов должен быть массивом.',
            'course_ids.*.exists' => 'Один из указанных курсов не найден.',

            'images.array' => 'Неверный формат поля изображений.',
            'images.*.id.exists' => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании.',
            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max' => 'Максимальный размер изображения — 10 МБ.',

            'deletedImages.*.exists' => 'Некоторых изображений для удаления не существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'slug' => 'Slug',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'published_at' => 'Дата публикации',
            'views' => 'Просмотры',
            'likes' => 'Лайки',
            'meta' => 'Meta-данные',
            'translations' => 'Переводы',
            'course_ids' => 'Курсы',
            'images' => 'Изображения',
            'deletedImages' => 'Удаляемые изображения',
        ];
    }
}
