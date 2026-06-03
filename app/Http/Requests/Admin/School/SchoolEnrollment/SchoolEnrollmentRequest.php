<?php

namespace App\Http\Requests\Admin\School\SchoolEnrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['user_id', 'school_course_id', 'school_course_schedule_id', 'school_order_id'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('progress_percent')) {
            $data['progress_percent'] = (int) $this->input('progress_percent');
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $enrollmentId = $this->route('school_enrollment')?->id
            ?? $this->route('enrollment')?->id
            ?? $this->route('schoolEnrollment')?->id
            ?? $this->input('id');

        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'school_course_id' => [
                'required',
                'integer',
                Rule::exists('school_courses', 'id'),
            ],

            'school_course_schedule_id' => [
                'nullable',
                'integer',
                Rule::exists('school_course_schedules', 'id'),
            ],

            'school_order_id' => [
                'nullable',
                'integer',
                Rule::exists('school_orders', 'id'),
            ],

            'status' => [
                'required',
                'string',
                Rule::in(['active', 'completed', 'cancelled', 'expired', 'paused']),
            ],

            'started_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'completed_at' => ['nullable', 'date'],

            'progress_percent' => ['nullable', 'integer', 'between:0,100'],

            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],

            'unique_enrollment' => [
                function ($attribute, $value, $fail) use ($enrollmentId) {
                    $exists = \DB::table('school_enrollments')
                        ->where('user_id', $this->input('user_id'))
                        ->where('school_course_id', $this->input('school_course_id'))
                        ->where('school_course_schedule_id', $this->input('school_course_schedule_id'))
                        ->when($enrollmentId, fn ($q) => $q->where('id', '!=', $enrollmentId))
                        ->exists();

                    if ($exists) {
                        $fail('Пользователь уже зачислен на этот курс и поток.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_course_id.required' => 'Не указан курс.',
            'school_course_id.integer' => 'Идентификатор курса должен быть числом.',
            'school_course_id.exists' => 'Курс не найден.',

            'school_course_schedule_id.integer' => 'Идентификатор потока должен быть числом.',
            'school_course_schedule_id.exists' => 'Поток курса не найден.',

            'school_order_id.integer' => 'Идентификатор заказа должен быть числом.',
            'school_order_id.exists' => 'Заказ не найден.',

            'status.required' => 'Статус обязателен.',
            'status.in' => 'Недопустимый статус зачисления.',

            'started_at.date' => 'Дата начала указана некорректно.',
            'expires_at.date' => 'Дата окончания указана некорректно.',
            'expires_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',
            'completed_at.date' => 'Дата завершения указана некорректно.',

            'progress_percent.integer' => 'Процент прогресса должен быть целым числом.',
            'progress_percent.between' => 'Процент прогресса должен быть от 0 до 100.',

            'notes.string' => 'Заметки должны быть текстом.',
            'meta.array' => 'Поле meta должно быть JSON-объектом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_course_id' => 'Курс',
            'school_course_schedule_id' => 'Поток курса',
            'school_order_id' => 'Заказ',
            'status' => 'Статус',
            'started_at' => 'Дата начала доступа',
            'expires_at' => 'Дата окончания доступа',
            'completed_at' => 'Дата завершения',
            'progress_percent' => 'Прогресс',
            'notes' => 'Заметки',
            'meta' => 'Дополнительные данные',
        ];
    }
}
