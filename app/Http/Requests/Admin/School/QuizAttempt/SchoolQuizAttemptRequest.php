<?php

namespace App\Http\Requests\Admin\School\QuizAttempt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQuizAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ([
                     'user_id',
                     'school_quiz_id',
                     'school_enrollment_id',
                     'school_course_id',
                     'school_module_id',
                     'school_lesson_id',
                     'attempt_number',
                     'score',
                     'max_score',
                     'percent',
                     'duration_seconds',
                 ] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('status')) {
            $data['status'] = strtolower((string) $this->input('status'));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $statuses = ['in_progress', 'completed', 'graded'];

        if ($this->isMethod('post')) {
            return [
                'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
                'school_quiz_id' => ['required', 'integer', Rule::exists('school_quizzes', 'id')],

                'school_enrollment_id' => ['nullable', 'integer', Rule::exists('school_enrollments', 'id')],
                'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
                'school_module_id' => ['nullable', 'integer', Rule::exists('school_modules', 'id')],
                'school_lesson_id' => ['nullable', 'integer', Rule::exists('school_lessons', 'id')],

                'attempt_number' => ['nullable', 'integer', 'min:1', 'max:65535'],

                'score' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'max_score' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'percent' => ['nullable', 'integer', 'min:0', 'max:100'],

                'status' => ['nullable', 'string', Rule::in($statuses)],

                'started_at' => ['nullable', 'date'],
                'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
                'duration_seconds' => ['nullable', 'integer', 'min:0'],

                'ip_address' => ['nullable', 'string', 'max:45'],
                'user_agent' => ['nullable', 'string', 'max:512'],
            ];
        }

        return [
            'status' => ['nullable', 'string', Rule::in($statuses)],

            'score' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'max_score' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],

            'started_at' => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_quiz_id.required' => 'Не указан квиз.',
            'school_quiz_id.integer' => 'Идентификатор квиза должен быть числом.',
            'school_quiz_id.exists' => 'Квиз не найден.',

            'school_enrollment_id.integer' => 'Идентификатор зачисления должен быть числом.',
            'school_enrollment_id.exists' => 'Указанное зачисление не найдено.',

            'school_course_id.integer' => 'Идентификатор курса должен быть числом.',
            'school_course_id.exists' => 'Курс не найден.',

            'school_module_id.integer' => 'Идентификатор модуля должен быть числом.',
            'school_module_id.exists' => 'Модуль не найден.',

            'school_lesson_id.integer' => 'Идентификатор урока должен быть числом.',
            'school_lesson_id.exists' => 'Урок не найден.',

            'attempt_number.integer' => 'Номер попытки должен быть целым числом.',
            'attempt_number.min' => 'Номер попытки должен быть не менее 1.',

            'score.integer' => 'Баллы должны быть целым числом.',
            'score.min' => 'Баллы не могут быть отрицательными.',

            'max_score.integer' => 'Максимальный балл должен быть целым числом.',
            'max_score.min' => 'Максимальный балл не может быть отрицательным.',

            'percent.integer' => 'Процент должен быть целым числом.',
            'percent.min' => 'Процент не может быть меньше 0.',
            'percent.max' => 'Процент не может быть больше 100.',

            'status.in' => 'Недопустимый статус. Разрешены: in_progress, completed, graded.',

            'started_at.date' => 'Неверный формат даты начала.',
            'finished_at.date' => 'Неверный формат даты завершения.',
            'finished_at.after_or_equal' => 'Время завершения не может быть раньше времени начала.',

            'duration_seconds.integer' => 'Длительность должна быть целым числом секунд.',
            'duration_seconds.min' => 'Длительность не может быть отрицательной.',

            'ip_address.max' => 'IP-адрес не должен превышать 45 символов.',
            'user_agent.max' => 'User-Agent не должен превышать 512 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_quiz_id' => 'Квиз',
            'school_enrollment_id' => 'Зачисление',
            'school_course_id' => 'Курс',
            'school_module_id' => 'Модуль',
            'school_lesson_id' => 'Урок',
            'attempt_number' => 'Номер попытки',
            'score' => 'Баллы',
            'max_score' => 'Максимальные баллы',
            'percent' => 'Процент',
            'status' => 'Статус',
            'started_at' => 'Начало',
            'finished_at' => 'Завершение',
            'duration_seconds' => 'Длительность',
            'ip_address' => 'IP-адрес',
            'user_agent' => 'User-Agent',
        ];
    }
}
