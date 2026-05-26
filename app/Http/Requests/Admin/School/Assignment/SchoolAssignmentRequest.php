<?php

namespace App\Http\Requests\Admin\School\Assignment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolAssignmentRequest extends FormRequest
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

        foreach (['activity', 'left', 'main', 'right'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['sort', 'attempts_limit', 'max_score'] as $field) {
            if ($this->has($field) && $this->input($field) !== null && $this->input($field) !== '') {
                $data[$field] = (int) $this->input($field);
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $assignment = $this->route('schoolAssignment')
            ?? $this->route('assignment')
            ?? $this->route('school_assignment')
            ?? $this->route('id');

        $assignmentId = is_object($assignment)
            ? $assignment->id
            : ($assignment ? (int) $assignment : null);

        return [
            'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
            'school_module_id' => ['nullable', 'integer', Rule::exists('school_modules', 'id')],
            'school_lesson_id' => ['nullable', 'integer', Rule::exists('school_lessons', 'id')],
            'school_instructor_profile_id' => ['nullable', 'integer', Rule::exists('school_instructor_profiles', 'id')],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_assignments', 'slug')->ignore($assignmentId),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'left' => ['required', 'boolean'],
            'main' => ['required', 'boolean'],
            'right' => ['required', 'boolean'],

            'published_at' => ['nullable', 'date'],

            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
            'visibility' => ['nullable', 'string', Rule::in(['public', 'enrolled', 'private'])],
            'attempts_limit' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'grading_type' => ['nullable', 'string', Rule::in(['manual', 'auto'])],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'due_at' => ['nullable', 'date'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.instructions' => ['nullable', 'string'],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_assignment_images', 'id'),
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
            'deletedImages.*' => ['integer', Rule::exists('school_assignment_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_course_id.exists' => 'Указанный курс не найден.',
            'school_module_id.exists' => 'Указанный модуль не найден.',
            'school_lesson_id.exists' => 'Указанный урок не найден.',
            'school_instructor_profile_id.exists' => 'Указанный преподаватель не найден.',

            'slug.required' => 'Укажите slug задания.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Задание с таким slug уже существует.',

            'activity.required' => 'Укажите активность задания.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'left.required' => 'Укажите вывод в левой колонке.',
            'main.required' => 'Укажите вывод в главном блоке.',
            'right.required' => 'Укажите вывод в правой колонке.',
            'left.boolean' => 'Поле левой колонки должно быть булевым значением.',
            'main.boolean' => 'Поле главного блока должно быть булевым значением.',
            'right.boolean' => 'Поле правой колонки должно быть булевым значением.',

            'status.in' => 'Недопустимый статус задания.',
            'visibility.in' => 'Недопустимое значение видимости.',
            'grading_type.in' => 'Недопустимый тип проверки.',

            'max_score.integer' => 'Максимальный балл должен быть целым числом.',
            'max_score.min' => 'Максимальный балл должен быть не менее 1.',
            'attempts_limit.integer' => 'Лимит попыток должен быть целым числом.',
            'attempts_limit.min' => 'Лимит попыток не может быть отрицательным.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'due_at.date' => 'Дата дедлайна имеет неверный формат.',

            'translations.required' => 'Необходимо передать переводы задания.',
            'translations.*.title.required' => 'Укажите название задания.',

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
            'school_course_id' => 'Курс',
            'school_module_id' => 'Модуль',
            'school_lesson_id' => 'Урок',
            'school_instructor_profile_id' => 'Преподаватель',
            'slug' => 'Slug',
            'activity' => 'Активность',
            'left' => 'Левая колонка',
            'main' => 'Главный блок',
            'right' => 'Правая колонка',
            'published_at' => 'Дата публикации',
            'status' => 'Статус',
            'visibility' => 'Видимость',
            'attempts_limit' => 'Лимит попыток',
            'grading_type' => 'Тип проверки',
            'max_score' => 'Максимальный балл',
            'due_at' => 'Дедлайн',
            'translations' => 'Переводы',
            'images' => 'Изображения',
        ];
    }
}
