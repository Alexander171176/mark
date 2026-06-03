<?php

namespace App\Http\Requests\Admin\School\SchoolQuizAttempt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
                     'duration_seconds',
                 ] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('status')) {
            $data['status'] = mb_strtolower(trim((string) $this->input('status')));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $attempt = $this->route('schoolQuizAttempt')
            ?? $this->route('quizAttempt')
            ?? $this->route('school_quiz_attempt')
            ?? $this->route('id');

        $attemptId = is_object($attempt)
            ? $attempt->id
            : ($attempt ? (int) $attempt : null);

        $isStore = $this->isMethod('post');

        return [
            'user_id' => [
                $isStore ? 'required' : 'sometimes',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'school_quiz_id' => [
                $isStore ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_quizzes', 'id'),
            ],

            'school_enrollment_id' => [
                'nullable',
                'integer',
                Rule::exists('school_enrollments', 'id'),
            ],

            'school_course_id' => [
                'nullable',
                'integer',
                Rule::exists('school_courses', 'id'),
            ],

            'school_module_id' => [
                'nullable',
                'integer',
                Rule::exists('school_modules', 'id'),
            ],

            'school_lesson_id' => [
                'nullable',
                'integer',
                Rule::exists('school_lessons', 'id'),
            ],

            'attempt_number' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],

            'score' => [
                'nullable',
                'integer',
                'min:0',
                'max:65535',
            ],

            'max_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:65535',
            ],

            'status' => [
                $isStore ? 'nullable' : 'sometimes',
                'string',
                Rule::in(['in_progress', 'completed', 'graded']),
            ],

            'started_at' => [
                'nullable',
                'date',
            ],

            'finished_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],

            'duration_seconds' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],

            'user_agent' => [
                'nullable',
                'string',
                'max:512',
            ],

            'unique_attempt' => [
                function ($attribute, $value, $fail) use ($attemptId) {
                    $userId = $this->input('user_id');
                    $quizId = $this->input('school_quiz_id');
                    $attemptNumber = $this->input('attempt_number');

                    if (!$userId || !$quizId || !$attemptNumber) {
                        return;
                    }

                    $exists = DB::table('school_quiz_attempts')
                        ->where('user_id', $userId)
                        ->where('school_quiz_id', $quizId)
                        ->where('attempt_number', $attemptNumber)
                        ->when($attemptId, fn ($q) => $q->where('id', '!=', $attemptId))
                        ->exists();

                    if ($exists) {
                        $fail('У этого пользователя уже есть такая попытка для выбранного квиза.');
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
            'attempt_number.max' => 'Номер попытки не должен быть больше 65535.',

            'score.integer' => 'Баллы должны быть целым числом.',
            'score.min' => 'Баллы не могут быть отрицательными.',
            'score.max' => 'Баллы не могут быть больше 65535.',

            'max_score.integer' => 'Максимальный балл должен быть целым числом.',
            'max_score.min' => 'Максимальный балл не может быть отрицательным.',
            'max_score.max' => 'Максимальный балл не может быть больше 65535.',

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
            'status' => 'Статус',
            'started_at' => 'Начало',
            'finished_at' => 'Завершение',
            'duration_seconds' => 'Длительность',
            'ip_address' => 'IP-адрес',
            'user_agent' => 'User-Agent',
        ];
    }
}
