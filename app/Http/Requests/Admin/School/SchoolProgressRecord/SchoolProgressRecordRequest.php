<?php

namespace App\Http\Requests\Admin\School\SchoolProgressRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolProgressRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->filled('status')) {
            $data['status'] = trim((string) $this->input('status'));
        }

        foreach ([
                     'user_id',
                     'school_enrollment_id',
                     'school_course_id',
                     'school_module_id',
                     'school_lesson_id',
                     'progress_percent',
                     'time_spent_seconds',
                 ] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $record = $this->route('school_progress_record')
            ?? $this->route('progressRecord')
            ?? $this->route('schoolProgressRecord');

        $id = is_object($record)
            ? $record->id
            : ($record ? (int) $record : null);

        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'school_enrollment_id' => ['nullable', 'integer', Rule::exists('school_enrollments', 'id')],
            'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
            'school_module_id' => ['nullable', 'integer', Rule::exists('school_modules', 'id')],

            'school_lesson_id' => [
                'nullable',
                'integer',
                Rule::exists('school_lessons', 'id'),
                Rule::unique('school_progress_records', 'school_lesson_id')
                    ->where(fn ($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($id),
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['in_progress', 'completed', 'skipped', 'locked']),
            ],

            'progress_percent' => ['sometimes', 'integer', 'between:0,100'],
            'time_spent_seconds' => ['sometimes', 'integer', 'min:0'],

            'last_viewed_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_enrollment_id.exists' => 'Зачисление не найдено.',
            'school_course_id.exists' => 'Курс не найден.',
            'school_module_id.exists' => 'Модуль не найден.',
            'school_lesson_id.exists' => 'Урок не найден.',
            'school_lesson_id.unique' => 'Прогресс по этому уроку уже зафиксирован для данного пользователя.',

            'status.in' => 'Недопустимый статус. Разрешены: in_progress, completed, skipped, locked.',

            'progress_percent.between' => 'Процент прогресса должен быть от 0 до 100.',
            'time_spent_seconds.min' => 'Затраченное время не может быть отрицательным.',

            'last_viewed_at.date' => 'Дата последней активности указана некорректно.',
            'completed_at.date' => 'Дата завершения указана некорректно.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_enrollment_id' => 'Зачисление',
            'school_course_id' => 'Курс',
            'school_module_id' => 'Модуль',
            'school_lesson_id' => 'Урок',
            'status' => 'Статус',
            'progress_percent' => 'Процент прогресса',
            'time_spent_seconds' => 'Затраченное время',
            'last_viewed_at' => 'Последняя активность',
            'completed_at' => 'Дата завершения',
        ];
    }
}
