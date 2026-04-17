<?php

namespace App\Http\Requests\Admin\School\QuizAttempt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuizAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ints = [
            'user_id','quiz_id','enrollment_id','course_id','module_id','lesson_id',
            'attempt_number','score','max_score','percent','duration_seconds',
        ];

        $merge = [];

        foreach ($ints as $key) {
            // ВАЖНО: для update многие поля не придут — это нормально
            if ($this->filled($key)) {
                $merge[$key] = (int) $this->input($key);
            }
        }

        // Нормализуем status к нижнему регистру
        if ($this->filled('status')) {
            $merge['status'] = strtolower((string) $this->input('status'));
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $statuses = ['in_progress', 'completed', 'graded'];

        // ✅ STORE: создаём попытку (нужны user_id + quiz_id)
        if ($this->isMethod('post')) {
            return [
                'user_id'        => ['required','integer','exists:users,id'],
                'quiz_id'        => ['required','integer','exists:quizzes,id'],

                'enrollment_id'  => ['nullable','integer','exists:enrollments,id'],
                'course_id'      => ['nullable','integer','exists:courses,id'],
                'module_id'      => ['nullable','integer','exists:modules,id'],
                'lesson_id'      => ['nullable','integer','exists:lessons,id'],

                'attempt_number' => ['sometimes','integer','min:1'],

                'score' => ['sometimes', 'nullable', 'integer', 'min:0'],
                'max_score' => ['sometimes', 'nullable', 'integer', 'min:0'],
                'duration_seconds' => ['sometimes', 'nullable', 'integer', 'min:0'],

                // percent на store можно принимать, но лучше тоже считать на бэке:
                'percent'        => ['sometimes','integer','min:0','max:100'],

                'status'         => ['sometimes','string', Rule::in($statuses)],

                'started_at'     => ['nullable','date'],
                'finished_at'    => ['nullable','date','after_or_equal:started_at'],

                'ip_address'     => ['nullable','string','max:45'],
                'user_agent'     => ['nullable','string','max:512'],
            ];
        }

        // ✅ UPDATE: редактируем только разрешённые поля (как в Edit.vue)
        return [
            'status' => ['sometimes', 'string', Rule::in($statuses)],

            'score' => ['nullable','integer','min:0'],
            'max_score' => ['nullable','integer','min:0'],
            'duration_seconds' => ['nullable','integer','min:0'],

            'started_at' => ['nullable','date'],
            'finished_at' => ['nullable','date','after_or_equal:started_at'],

            // ✅ запретили менять всё контекстное и аудит:
            // user_id, quiz_id, enrollment_id, course_id, module_id, lesson_id, attempt_number
            // ip_address, user_agent, percent
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.integer'  => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'   => 'Пользователь не найден.',

            'quiz_id.required' => 'Не указан квиз.',
            'quiz_id.integer'  => 'Идентификатор квиза должен быть числом.',
            'quiz_id.exists'   => 'Квиз не найден.',

            'enrollment_id.integer' => 'Идентификатор зачисления должен быть числом.',
            'enrollment_id.exists'  => 'Указанное зачисление не найдено.',

            'course_id.integer' => 'Идентификатор курса должен быть числом.',
            'course_id.exists'  => 'Курс не найден.',

            'module_id.integer' => 'Идентификатор модуля должен быть числом.',
            'module_id.exists'  => 'Модуль не найден.',

            'lesson_id.integer' => 'Идентификатор урока должен быть числом.',
            'lesson_id.exists'  => 'Урок не найден.',

            'attempt_number.integer' => 'Номер попытки должен быть целым числом.',
            'attempt_number.min'     => 'Номер попытки должен быть не менее :min.',

            'score.integer'     => 'Баллы должны быть целым числом.',
            'score.min'         => 'Баллы не могут быть отрицательными.',

            'max_score.integer' => 'Максимальный балл должен быть целым числом.',
            'max_score.min'     => 'Максимальный балл не может быть отрицательным.',

            'percent.integer' => 'Процент должен быть целым числом.',
            'percent.min'     => 'Процент не может быть меньше :min.',
            'percent.max'     => 'Процент не может быть больше :max.',

            'status.string' => 'Статус должен быть строкой.',
            'status.in'     => 'Недопустимый статус. Разрешены: in_progress, completed, graded.',

            'started_at.date'  => 'Неверный формат даты начала.',
            'finished_at.date' => 'Неверный формат даты завершения.',
            'finished_at.after_or_equal' => 'Время завершения не может быть раньше времени начала.',

            'duration_seconds.integer' => 'Длительность должна быть целым числом секунд.',
            'duration_seconds.min'     => 'Длительность не может быть отрицательной.',

            'ip_address.max' => 'IP-адрес не должен превышать :max символов.',
            'user_agent.max' => 'User-Agent не должен превышать :max символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'         => 'Пользователь',
            'quiz_id'         => 'Квиз',
            'enrollment_id'   => 'Зачисление',
            'course_id'       => 'Курс',
            'module_id'       => 'Модуль',
            'lesson_id'       => 'Урок',
            'attempt_number'  => 'Номер попытки',
            'score'           => 'Баллы',
            'max_score'       => 'Максимальные баллы',
            'percent'         => 'Процент',
            'status'          => 'Статус',
            'started_at'      => 'Начало',
            'finished_at'     => 'Завершение',
            'duration_seconds'=> 'Длительность (сек.)',
            'ip_address'      => 'IP-адрес',
            'user_agent'      => 'User-Agent',
        ];
    }
}
