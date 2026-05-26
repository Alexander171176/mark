<?php

namespace App\Http\Requests\Admin\School\Quiz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolQuizRequest extends FormRequest
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

        foreach ([
                     'school_course_id',
                     'school_module_id',
                     'school_lesson_id',
                     'attempts_limit',
                     'time_limit_minutes',
                     'pass_score',
                     'sort',
                 ] as $field) {
            if ($this->has($field) && is_numeric($this->input($field))) {
                $data[$field] = (int) $this->input($field);
            }
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

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $quiz = $this->route('schoolQuiz')
            ?? $this->route('school_quiz')
            ?? $this->route('quiz')
            ?? $this->route('id');

        $quizId = is_object($quiz)
            ? $quiz->id
            : ($quiz ? (int) $quiz : null);

        return [
            'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
            'school_module_id' => ['nullable', 'integer', Rule::exists('school_modules', 'id')],
            'school_lesson_id' => ['nullable', 'integer', Rule::exists('school_lessons', 'id')],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_quizzes', 'slug')->ignore($quizId),
            ],

            'type' => ['required', 'string', Rule::in(['graded', 'practice'])],

            'attempts_limit' => ['required', 'integer', 'min:0', 'max:65535'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'pass_score' => ['required', 'integer', 'min:0', 'max:100'],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],

            'left' => ['nullable', 'boolean'],
            'main' => ['nullable', 'boolean'],
            'right' => ['nullable', 'boolean'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_quiz_images', 'id'),
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
            'deletedImages.*' => ['integer', Rule::exists('school_quiz_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_course_id.exists' => 'Указанный курс не найден.',
            'school_module_id.exists' => 'Указанный модуль не найден.',
            'school_lesson_id.exists' => 'Указанный урок не найден.',

            'slug.required' => 'Укажите slug квиза.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Квиз с таким slug уже существует.',

            'type.required' => 'Укажите тип квиза.',
            'type.in' => 'Недопустимый тип квиза. Разрешено: graded или practice.',

            'attempts_limit.required' => 'Укажите лимит попыток.',
            'attempts_limit.integer' => 'Лимит попыток должен быть целым числом.',
            'attempts_limit.min' => 'Лимит попыток не может быть отрицательным.',

            'time_limit_minutes.integer' => 'Лимит времени должен быть целым числом.',
            'time_limit_minutes.min' => 'Минимальный лимит времени — 1 минута.',

            'pass_score.required' => 'Укажите проходной порог.',
            'pass_score.integer' => 'Проходной порог должен быть целым числом.',
            'pass_score.min' => 'Проходной порог не может быть меньше 0.',
            'pass_score.max' => 'Проходной порог не может быть больше 100.',

            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'left.boolean' => 'Поле левой колонки должно быть булевым значением.',
            'main.boolean' => 'Поле главного блока должно быть булевым значением.',
            'right.boolean' => 'Поле правой колонки должно быть булевым значением.',

            'sort.integer' => 'Позиция сортировки должна быть целым числом.',
            'sort.min' => 'Позиция сортировки не может быть отрицательной.',
            'published_at.date' => 'Дата публикации указана некорректно.',

            'translations.required' => 'Необходимо передать переводы квиза.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один перевод.',

            'translations.*.title.required' => 'Укажите название квиза.',
            'translations.*.title.max' => 'Название квиза не должно превышать 255 символов.',

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
}
