<?php

namespace App\Http\Requests\Admin\School\QuizAttemptItem;

use Illuminate\Foundation\Http\FormRequest;

class QuizAttemptItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Определяем режим реквеста:
     * - create: POST (store)
     * - review: PUT/PATCH (update)
     */
    protected function mode(): string
    {
        return $this->isMethod('post') ? 'create' : 'review';
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализуем числа/булево только для тех полей, которые потенциально валидируются
        if ($this->mode() === 'create') {
            $ints = [
                'quiz_attempt_id',
                'quiz_question_id',
                'selected_answer_id',
                'score',
            ];

            foreach ($ints as $key) {
                if ($this->filled($key)) {
                    $merge[$key] = (int) $this->input($key);
                }
            }

            if ($this->filled('is_correct')) {
                $merge['is_correct'] = filter_var(
                    $this->input('is_correct'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }

            // selected_answer_ids может прийти строкой JSON
            if ($this->filled('selected_answer_ids') && is_string($this->input('selected_answer_ids'))) {
                $decoded = json_decode($this->input('selected_answer_ids'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge['selected_answer_ids'] = $decoded;
                }
            }
        } else {
            // review
            if ($this->filled('score')) {
                $merge['score'] = (int) $this->input('score');
            }

            if ($this->filled('is_correct')) {
                $merge['is_correct'] = filter_var(
                    $this->input('is_correct'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        if ($this->mode() === 'create') {
            return [
                'quiz_attempt_id'  => ['required', 'integer', 'exists:quiz_attempts,id'],
                'quiz_question_id' => ['required', 'integer', 'exists:quiz_questions,id'],

                // минимум одно из трёх
                'selected_answer_id' => [
                    'nullable',
                    'integer',
                    'exists:quiz_answers,id',
                    'required_without_all:selected_answer_ids,free_text_answer',
                ],

                'selected_answer_ids' => [
                    'nullable',
                    'array',
                    'min:1',
                    'required_without_all:selected_answer_id,free_text_answer',
                ],
                'selected_answer_ids.*' => ['integer', 'exists:quiz_answers,id'],

                'free_text_answer' => [
                    'nullable',
                    'string',
                    'max:20000',
                    'required_without_all:selected_answer_id,selected_answer_ids',
                ],

                // системные/авто-поля можно принимать, но НЕ обязательно
                'is_correct'       => ['sometimes', 'boolean'],
                'score'            => ['sometimes', 'integer', 'min:0'],

                // ❌ max_score убрали как лишнее
                'reviewer_comment' => ['nullable', 'string', 'max:2000'],
            ];
        }

        // review (update): разрешаем только поля проверки и запрещаем всё остальное
        return [
            'quiz_attempt_id'     => ['prohibited'],
            'quiz_question_id'    => ['prohibited'],
            'selected_answer_id'  => ['prohibited'],
            'selected_answer_ids' => ['prohibited'],
            'free_text_answer'    => ['prohibited'],

            'is_correct'       => ['sometimes', 'boolean'],         // (контроллер дополнительно ограничит по типу вопроса)
            'score'            => ['sometimes', 'integer', 'min:0'],
            'reviewer_comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            // create
            'quiz_attempt_id.required' => 'Не указана попытка квиза.',
            'quiz_attempt_id.integer'  => 'Идентификатор попытки должен быть числом.',
            'quiz_attempt_id.exists'   => 'Указанная попытка квиза не найдена.',

            'quiz_question_id.required' => 'Не указан вопрос квиза.',
            'quiz_question_id.integer'  => 'Идентификатор вопроса должен быть числом.',
            'quiz_question_id.exists'   => 'Указанный вопрос квиза не найден.',

            'selected_answer_id.integer' => 'Идентификатор ответа должен быть числом.',
            'selected_answer_id.exists'  => 'Выбранный ответ не найден.',
            'selected_answer_id.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',

            'selected_answer_ids.array'  => 'Список вариантов ответа должен быть массивом.',
            'selected_answer_ids.min'    => 'Укажите хотя бы один вариант ответа.',
            'selected_answer_ids.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',
            'selected_answer_ids.*.integer' => 'Каждый вариант должен быть числовым идентификатором.',
            'selected_answer_ids.*.exists'  => 'Один из вариантов ответа не найден.',

            'free_text_answer.string' => 'Ответ должен быть строкой.',
            'free_text_answer.max'    => 'Текст ответа не должен превышать :max символов.',
            'free_text_answer.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',

            // review fields
            'is_correct.boolean' => 'Поле "правильность" должно быть булевым.',
            'score.integer'      => 'Баллы должны быть целым числом.',
            'score.min'          => 'Баллы не могут быть отрицательными.',

            'reviewer_comment.string' => 'Комментарий проверяющего должен быть строкой.',
            'reviewer_comment.max'    => 'Комментарий не должен превышать :max символов.',

            // prohibited (review)
            'quiz_attempt_id.prohibited'     => 'Нельзя менять попытку квиза при проверке.',
            'quiz_question_id.prohibited'    => 'Нельзя менять вопрос при проверке.',
            'selected_answer_id.prohibited'  => 'Нельзя менять ответ студента при проверке.',
            'selected_answer_ids.prohibited' => 'Нельзя менять ответы студента при проверке.',
            'free_text_answer.prohibited'    => 'Нельзя менять текст ответа студента при проверке.',
        ];
    }

    public function attributes(): array
    {
        return [
            'quiz_attempt_id'     => 'Попытка квиза',
            'quiz_question_id'    => 'Вопрос квиза',
            'selected_answer_id'  => 'Выбранный ответ',
            'selected_answer_ids' => 'Множественные ответы',
            'free_text_answer'    => 'Текстовый ответ',
            'is_correct'          => 'Правильность',
            'score'               => 'Начисленные баллы',
            'reviewer_comment'    => 'Комментарий проверяющего',
        ];
    }
}
