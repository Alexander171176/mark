<?php

namespace App\Http\Requests\Admin\School\Enrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите Policy при необходимости. Пока разрешаем.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['user_id','course_id','course_schedule_id','order_id'] as $k) {
            if ($this->filled($k)) $merge[$k] = (int) $this->input($k);
        }

        if ($this->filled('progress_percent')) {
            $merge['progress_percent'] = (int) $this->input('progress_percent');
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        $id = $this->route('enrollment')?->id ?? $this->input('id');

        return [
            'user_id'            => ['required','integer','exists:users,id'],
            'course_id'          => ['required','integer','exists:courses,id'],
            'course_schedule_id' => ['nullable','integer','exists:course_schedules,id'],
            'order_id'           => ['nullable','integer','exists:orders,id'],

            'status' => [
                'required','string',
                Rule::in(['active','completed','cancelled','expired','paused']),
            ],

            'started_at'   => ['nullable','date'],
            'expires_at'   => ['nullable','date','after_or_equal:started_at'],
            'completed_at' => ['nullable','date'],

            'progress_percent' => ['nullable', 'integer', 'between:0,100'],

            'notes' => ['nullable','string'],
            'meta'  => ['nullable','array'],

            // опциональная защита от дубликатов: для одного пользователя,
            // курса и потока не более одной активной записи
            // (раскомментируйте при необходимости)
            // Rule::unique('enrollments')->where(fn($q) => $q
            //     ->where('user_id', $this->user_id)
            //     ->where('course_id', $this->course_id)
            //     ->where('course_schedule_id', $this->course_schedule_id)
            // )->ignore($id),
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'   => 'Не указан пользователь.',
            'user_id.integer'    => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'     => 'Пользователь не найден.',

            'course_id.required' => 'Не указан курс.',
            'course_id.integer'  => 'Идентификатор курса должен быть числом.',
            'course_id.exists'   => 'Курс не найден.',

            'course_schedule_id.integer' => 'Идентификатор потока должен быть числом.',
            'course_schedule_id.exists'  => 'Поток/расписание не найдено.',

            'order_id.integer'   => 'Идентификатор заказа должен быть числом.',
            'order_id.exists'    => 'Заказ не найден.',

            'status.required'    => 'Статус обязателен.',
            'status.string'      => 'Статус должен быть строкой.',
            'status.in'          => 'Недопустимый статус зачисления.',

            'started_at.date'    => 'Дата начала указана некорректно.',
            'expires_at.date'    => 'Дата окончания указана некорректно.',
            'expires_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',
            'completed_at.date'  => 'Дата завершения указана некорректно.',

            'progress_percent.required' => 'Процент прогресса обязателен.',
            'progress_percent.integer'  => 'Процент прогресса должен быть целым числом.',
            'progress_percent.min'      => 'Процент прогресса не может быть меньше :min.',
            'progress_percent.max'      => 'Процент прогресса не может быть больше :max.',

            'notes.string'       => 'Примечания должны быть текстом.',
            'meta.array'         => 'Поле meta должно быть JSON-объектом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'          => 'Пользователь',
            'course_id'        => 'Курс',
            'course_schedule_id'=> 'Поток/расписание',
            'order_id'         => 'Заказ',
            'status'           => 'Статус',
            'started_at'       => 'Дата начала доступа',
            'expires_at'       => 'Дата окончания доступа',
            'completed_at'     => 'Дата завершения',
            'progress_percent' => 'Прогресс, %',
            'notes'            => 'Примечания',
            'meta'             => 'Доп. данные',
        ];
    }
}
