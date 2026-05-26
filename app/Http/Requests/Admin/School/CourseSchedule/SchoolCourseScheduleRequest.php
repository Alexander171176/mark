<?php

namespace App\Http\Requests\Admin\School\CourseSchedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolCourseScheduleRequest extends FormRequest
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

        foreach (['activity', 'is_online'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['sort', 'capacity', 'views'] as $field) {
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
        $schedule = $this->route('schoolCourseSchedule')
            ?? $this->route('courseSchedule')
            ?? $this->route('schedule')
            ?? $this->route('id');

        $scheduleId = is_object($schedule)
            ? $schedule->id
            : ($schedule ? (int) $schedule : null);

        return [
            'school_course_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_courses', 'id'),
            ],

            'school_instructor_profile_id' => [
                'nullable',
                'integer',
                Rule::exists('school_instructor_profiles', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_course_schedules', 'slug')->ignore($scheduleId),
            ],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'enroll_starts_at' => ['nullable', 'date'],
            'enroll_ends_at' => ['nullable', 'date', 'after_or_equal:enroll_starts_at'],

            'capacity' => ['nullable', 'integer', 'min:0'],

            'is_online' => ['required', 'boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'timezone'],

            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived', 'cancelled'])],
            'views' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],

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
                Rule::exists('school_course_schedule_images', 'id'),
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
            'deletedImages.*' => ['integer', Rule::exists('school_course_schedule_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_course_id.required' => 'Не указан курс.',
            'school_course_id.exists' => 'Указанный курс не найден.',

            'school_instructor_profile_id.exists' => 'Указанный преподаватель не найден.',

            'activity.required' => 'Укажите активность потока.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Укажите slug потока.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Поток с таким slug уже существует.',

            'starts_at.date' => 'Дата начала указана неверно.',
            'ends_at.date' => 'Дата окончания указана неверно.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',
            'enroll_starts_at.date' => 'Дата начала записи указана неверно.',
            'enroll_ends_at.date' => 'Дата окончания записи указана неверно.',
            'enroll_ends_at.after_or_equal' => 'Дата окончания записи не может быть раньше даты начала записи.',

            'capacity.integer' => 'Лимит мест должен быть целым числом.',
            'capacity.min' => 'Лимит мест не может быть отрицательным.',

            'is_online.required' => 'Укажите формат потока.',
            'is_online.boolean' => 'Поле онлайн-формата должно быть булевым значением.',
            'location.max' => 'Локация не должна превышать 255 символов.',
            'meeting_url.url' => 'Ссылка на встречу указана некорректно.',
            'timezone.timezone' => 'Укажите корректный часовой пояс.',

            'status.in' => 'Недопустимый статус потока.',
            'views.integer' => 'Количество просмотров должно быть целым числом.',
            'views.min' => 'Количество просмотров не может быть отрицательным.',

            'translations.required' => 'Необходимо передать переводы потока.',
            'translations.array' => 'Переводы потока должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один перевод.',

            'translations.*.title.string' => 'Название потока должно быть строкой.',
            'translations.*.title.max' => 'Название потока не должно превышать 255 символов.',
            'translations.*.title.required' => 'Укажите название потока.',

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
            'school_instructor_profile_id' => 'Преподаватель',
            'slug' => 'Slug',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'starts_at' => 'Дата начала',
            'ends_at' => 'Дата окончания',
            'enroll_starts_at' => 'Начало записи',
            'enroll_ends_at' => 'Окончание записи',
            'capacity' => 'Лимит мест',
            'is_online' => 'Онлайн',
            'location' => 'Локация',
            'meeting_url' => 'Ссылка на встречу',
            'timezone' => 'Часовой пояс',
            'status' => 'Статус',
            'views' => 'Просмотры',
            'notes' => 'Заметки',
            'translations' => 'Переводы',
            'images' => 'Изображения',
        ];
    }
}
