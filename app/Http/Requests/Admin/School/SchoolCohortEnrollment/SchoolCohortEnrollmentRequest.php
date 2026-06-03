<?php

namespace App\Http\Requests\Admin\School\SchoolCohortEnrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolCohortEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_course_schedule_id', 'user_id'] as $field) {
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
        $enrollmentId = $this->route('school_cohort_enrollment')?->id
            ?? $this->route('cohortEnrollment')?->id
            ?? $this->route('cohort_enrollment')?->id
            ?? $this->input('id');

        return [
            'school_course_schedule_id' => [
                'required',
                'integer',
                Rule::exists('school_course_schedules', 'id'),
            ],

            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'status' => [
                'nullable',
                'string',
                Rule::in(['pending', 'approved', 'rejected', 'cancelled']),
            ],

            'enrolled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],

            'unique_pair' => [
                function ($attribute, $value, $fail) use ($enrollmentId) {
                    $exists = DB::table('school_cohort_enrollments')
                        ->where('school_course_schedule_id', $this->input('school_course_schedule_id'))
                        ->where('user_id', $this->input('user_id'))
                        ->when($enrollmentId, fn ($q) => $q->where('id', '!=', $enrollmentId))
                        ->exists();

                    if ($exists) {
                        $fail('Пользователь уже записан на этот поток.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'school_course_schedule_id.required' => 'Не указан поток курса.',
            'school_course_schedule_id.integer' => 'Идентификатор потока должен быть числом.',
            'school_course_schedule_id.exists' => 'Указанный поток не найден.',

            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'status.string' => 'Статус должен быть строкой.',
            'status.in' => 'Недопустимый статус. Возможные: pending, approved, rejected, cancelled.',

            'enrolled_at.date' => 'Дата записи указана некорректно.',
            'notes.string' => 'Заметки должны быть текстом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_course_schedule_id' => 'Поток курса',
            'user_id' => 'Пользователь',
            'status' => 'Статус',
            'enrolled_at' => 'Дата записи',
            'notes' => 'Заметки',
        ];
    }
}
