<?php

namespace App\Http\Requests\Admin\School\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolModuleRequest extends FormRequest
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

        foreach (['sort', 'difficulty', 'duration', 'lessons_count'] as $field) {
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
        $module = $this->route('schoolModule')
            ?? $this->route('module')
            ?? $this->route('id');

        $moduleId = is_object($module)
            ? $module->id
            : ($module ? (int) $module : null);

        $courseIdForUnique = $this->input('school_course_id');

        return [
            'school_course_id' => [
                'required',
                'integer',
                Rule::exists('school_courses', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_modules', 'slug')
                    ->where(fn ($q) => $q->where('school_course_id', $courseIdForUnique))
                    ->ignore($moduleId),
            ],

            'published_at' => ['nullable', 'date'],

            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
            'availability' => ['nullable', 'string', Rule::in(['unlisted', 'public', 'private'])],

            'difficulty' => ['nullable', 'integer', 'min:0', 'max:5'],
            'duration' => ['nullable', 'integer', 'min:0'],

            'lessons_count' => ['nullable', 'integer', 'min:0'],
            'popularity' => ['nullable', 'integer', 'min:0'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'rating_avg' => ['nullable', 'numeric', 'between:0,5'],
            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:160'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string', 'max:255'],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_module_images', 'id'),
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
            'deletedImages.*' => ['integer', Rule::exists('school_module_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_course_id.required' => 'Укажите курс, к которому относится модуль.',
            'school_course_id.integer' => 'ID курса должен быть числом.',
            'school_course_id.exists' => 'Указанный курс не найден.',

            'activity.required' => 'Укажите активность модуля.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Укажите slug модуля.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Модуль с таким slug уже существует в этом курсе.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'status.in' => 'Недопустимый статус модуля.',
            'availability.in' => 'Недопустимое значение доступности.',

            'difficulty.integer' => 'Сложность должна быть целым числом.',
            'difficulty.min' => 'Сложность не может быть меньше 0.',
            'difficulty.max' => 'Сложность не может быть больше 5.',

            'duration.integer' => 'Длительность должна быть целым числом.',
            'duration.min' => 'Длительность не может быть отрицательной.',

            'translations.required' => 'Необходимо передать переводы модуля.',
            'translations.*.title.required' => 'Укажите название модуля.',

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
            'slug' => 'Slug',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'published_at' => 'Дата публикации',
            'status' => 'Статус',
            'availability' => 'Доступность',
            'difficulty' => 'Сложность',
            'duration' => 'Длительность',
            'translations' => 'Переводы',
            'images' => 'Изображения',
        ];
    }
}
