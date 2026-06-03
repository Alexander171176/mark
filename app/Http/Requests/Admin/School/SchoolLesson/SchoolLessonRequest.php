<?php

namespace App\Http\Requests\Admin\School\SchoolLesson;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolLessonRequest extends FormRequest
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

        foreach (['sort', 'difficulty', 'duration', 'preview_value'] as $field) {
            if ($this->has($field) && is_numeric($this->input($field))) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $lesson = $this->route('schoolLesson')
            ?? $this->route('lesson')
            ?? $this->route('school_lesson')
            ?? $this->route('id');

        $lessonId = is_object($lesson)
            ? $lesson->id
            : ($lesson ? (int) $lesson : null);

        $moduleIdForUnique = $this->input('school_module_id');

        return [
            'school_module_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_modules', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_lessons', 'slug')
                    ->where(fn ($q) => $q->where('school_module_id', $moduleIdForUnique))
                    ->ignore($lessonId),
            ],

            'content_type' => ['nullable', 'string', 'max:255'],
            'content_id' => ['nullable', 'integer', 'min:1'],

            'published_at' => ['nullable', 'date'],

            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
            'availability' => ['nullable', 'string', Rule::in(['unlisted', 'public', 'private'])],
            'access_type' => ['nullable', 'string', Rule::in(['free', 'paid', 'bonus'])],

            'difficulty' => ['nullable', 'integer', 'min:0', 'max:5'],
            'duration' => ['nullable', 'integer', 'min:0'],

            'preview_mode' => ['nullable', 'string', 'max:32', Rule::in(['none', 'full', 'percent', 'duration', 'chars'])],
            'preview_value' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:160'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string', 'max:255'],

            'hashtag_ids' => ['nullable', 'array'],
            'hashtag_ids.*' => ['integer', Rule::exists('school_hashtags', 'id')],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_lesson_images', 'id'),
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
            'deletedImages.*' => ['integer', Rule::exists('school_lesson_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_module_id.required' => 'Укажите модуль, к которому относится урок.',
            'school_module_id.exists' => 'Указанный модуль не найден.',

            'activity.required' => 'Укажите активность урока.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Укажите slug урока.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Урок с таким slug уже существует в этом модуле.',

            'content_type.string' => 'Тип контента должен быть строкой.',
            'content_id.integer' => 'ID связанного контента должен быть числом.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'status.in' => 'Недопустимый статус урока.',
            'availability.in' => 'Недопустимое значение доступности.',
            'access_type.in' => 'Недопустимый тип доступа.',

            'difficulty.integer' => 'Сложность должна быть целым числом.',
            'difficulty.max' => 'Сложность не может быть больше 5.',
            'duration.integer' => 'Длительность должна быть целым числом.',
            'duration.min' => 'Длительность не может быть отрицательной.',

            'preview_mode.in' => 'Недопустимый режим превью.',
            'preview_value.integer' => 'Значение превью должно быть числом.',

            'translations.required' => 'Необходимо передать переводы урока.',
            'translations.*.title.required' => 'Укажите название урока.',

            'hashtag_ids.array' => 'Список хештегов должен быть массивом.',
            'hashtag_ids.*.exists' => 'Некоторых хештегов не существует.',

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
            'school_module_id' => 'Модуль',
            'slug' => 'Slug',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'published_at' => 'Дата публикации',
            'status' => 'Статус',
            'availability' => 'Доступность',
            'access_type' => 'Тип доступа',
            'difficulty' => 'Сложность',
            'duration' => 'Длительность',
            'preview_mode' => 'Режим превью',
            'preview_value' => 'Значение превью',
            'translations' => 'Переводы',
            'hashtag_ids' => 'Хештеги',
            'images' => 'Изображения',
        ];
    }
}
