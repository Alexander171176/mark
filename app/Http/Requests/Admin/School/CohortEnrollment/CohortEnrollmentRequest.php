<?php

namespace App\Http\Requests\Admin\School\CohortEnrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CohortEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите Policy при необходимости. Пока разрешаем.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        foreach (['course_schedule_id','user_id'] as $int) {
            if ($this->filled($int)) $merge[$int] = (int) $this->input($int);
        }
        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        // Для уникальности пары (course_schedule_id, user_id) учтём update:
        // при обновлении игнорируем текущую запись по id из маршрута/ввода.
        $id = $this->route('cohort_enrollment')?->id ?? $this->input('id');

        return [
            'course_schedule_id' => ['required','integer','exists:course_schedules,id'],
            'user_id'            => ['required','integer','exists:users,id'],
            'status'             => ['sometimes','string', Rule::in(['pending','approved','rejected','cancelled'])],
            'enrolled_at'        => ['nullable','date'],
            'notes'              => ['nullable','string'],

            'unique_pair'        => [
                function ($attr, $val, $fail) use ($id) {
                    $exists = DB::table('cohort_enrollments')
                        ->where('course_schedule_id', $this->input('course_schedule_id'))
                        ->where('user_id', $this->input('user_id'))
                        ->when($id, fn($q) => $q->where('id', '!=', $id))
                        ->exists();

                    if ($exists) {
                        $fail('Пользователь уже записан в этот поток.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course_schedule_id.required' => 'Не указан поток (расписание курса).',
            'course_schedule_id.integer'  => 'Идентификатор потока должен быть числом.',
            'course_schedule_id.exists'   => 'Указанный поток не найден.',

            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer'  => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'   => 'Указанный пользователь не найден.',

            'status.string' => 'Статус должен быть строкой.',
            'status.in'     => 'Недопустимый статус. Возможные: pending, approved, rejected, cancelled.',

            'enrolled_at.date' => 'Дата зачисления указана некорректно.',
            'notes.string'     => 'Заметки должны быть текстом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_schedule_id' => 'Поток',
            'user_id'            => 'Пользователь',
            'status'             => 'Статус',
            'enrolled_at'        => 'Дата зачисления',
            'notes'              => 'Заметки',
        ];
    }
}
