<?php

namespace App\Http\Requests\Admin\School\QuizAttemptItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQuizAttemptItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function mode(): string
    {
        return $this->isMethod('post') ? 'create' : 'review';
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->mode() === 'create') {
            foreach ([
                         'school_quiz_attempt_id',
                         'school_quiz_question_id',
                         'selected_answer_id',
                         'score',
                         'max_score',
                     ] as $field) {
                if ($this->filled($field)) {
                    $data[$field] = (int) $this->input($field);
                }
            }

            if ($this->filled('is_correct')) {
                $data['is_correct'] = filter_var(
                    $this->input('is_correct'),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }

            if ($this->filled('selected_answer_ids') && is_string($this->input('selected_answer_ids'))) {
                $decoded = json_decode($this->input('selected_answer_ids'), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['selected_answer_ids'] = $decoded;
                }
            }
        } else {
            foreach (['score', 'max_score'] as $field) {
                if ($this->filled($field)) {
                    $data[$field] = (int) $this->input($field);
                }
            }

            if ($this->filled('is_correct')) {
                $data['is_correct'] = filter_var(
                    $this->input('is_correct'),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        if ($this->mode() === 'create') {
            return [
                'school_quiz_attempt_id' => [
                    'required',
                    'integer',
                    Rule::exists('school_quiz_attempts', 'id'),
                ],

                'school_quiz_question_id' => [
                    'required',
                    'integer',
                    Rule::exists('school_quiz_questions', 'id'),
                ],

                'selected_answer_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('school_quiz_answers', 'id'),
                    'required_without_all:selected_answer_ids,free_text_answer',
                ],

                'selected_answer_ids' => [
                    'nullable',
                    'array',
                    'min:1',
                    'required_without_all:selected_answer_id,free_text_answer',
                ],

                'selected_answer_ids.*' => [
                    'integer',
                    Rule::exists('school_quiz_answers', 'id'),
                ],

                'free_text_answer' => [
                    'nullable',
                    'string',
                    'max:20000',
                    'required_without_all:selected_answer_id,selected_answer_ids',
                ],

                'is_correct' => ['nullable', 'boolean'],
                'score' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'max_score' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'reviewer_comment' => ['nullable', 'string', 'max:2000'],
            ];
        }

        return [
            'school_quiz_attempt_id' => ['prohibited'],
            'school_quiz_question_id' => ['prohibited'],
            'selected_answer_id' => ['prohibited'],
            'selected_answer_ids' => ['prohibited'],
            'free_text_answer' => ['prohibited'],

            'is_correct' => ['nullable', 'boolean'],
            'score' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'max_score' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'reviewer_comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_quiz_attempt_id.required' => 'Не указана попытка квиза.',
            'school_quiz_attempt_id.integer' => 'Идентификатор попытки должен быть числом.',
            'school_quiz_attempt_id.exists' => 'Указанная попытка квиза не найдена.',

            'school_quiz_question_id.required' => 'Не указан вопрос квиза.',
            'school_quiz_question_id.integer' => 'Идентификатор вопроса должен быть числом.',
            'school_quiz_question_id.exists' => 'Указанный вопрос квиза не найден.',

            'selected_answer_id.integer' => 'Идентификатор ответа должен быть числом.',
            'selected_answer_id.exists' => 'Выбранный ответ не найден.',
            'selected_answer_id.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',

            'selected_answer_ids.array' => 'Список вариантов ответа должен быть массивом.',
            'selected_answer_ids.min' => 'Укажите хотя бы один вариант ответа.',
            'selected_answer_ids.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',
            'selected_answer_ids.*.integer' => 'Каждый вариант должен быть числовым идентификатором.',
            'selected_answer_ids.*.exists' => 'Один из вариантов ответа не найден.',

            'free_text_answer.string' => 'Ответ должен быть строкой.',
            'free_text_answer.max' => 'Текст ответа не должен превышать :max символов.',
            'free_text_answer.required_without_all' => 'Укажите вариант ответа, набор вариантов или текстовый ответ.',

            'is_correct.boolean' => 'Поле правильности должно быть булевым.',
            'score.integer' => 'Баллы должны быть целым числом.',
            'score.min' => 'Баллы не могут быть отрицательными.',
            'score.max' => 'Баллы не могут быть больше :max.',

            'max_score.integer' => 'Максимальные баллы должны быть целым числом.',
            'max_score.min' => 'Максимальные баллы не могут быть отрицательными.',
            'max_score.max' => 'Максимальные баллы не могут быть больше :max.',

            'reviewer_comment.string' => 'Комментарий проверяющего должен быть строкой.',
            'reviewer_comment.max' => 'Комментарий не должен превышать :max символов.',

            'school_quiz_attempt_id.prohibited' => 'Нельзя менять попытку квиза при проверке.',
            'school_quiz_question_id.prohibited' => 'Нельзя менять вопрос при проверке.',
            'selected_answer_id.prohibited' => 'Нельзя менять ответ студента при проверке.',
            'selected_answer_ids.prohibited' => 'Нельзя менять ответы студента при проверке.',
            'free_text_answer.prohibited' => 'Нельзя менять текст ответа студента при проверке.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_quiz_attempt_id' => 'Попытка квиза',
            'school_quiz_question_id' => 'Вопрос квиза',
            'selected_answer_id' => 'Выбранный ответ',
            'selected_answer_ids' => 'Множественные ответы',
            'free_text_answer' => 'Текстовый ответ',
            'is_correct' => 'Правильность',
            'score' => 'Начисленные баллы',
            'max_score' => 'Максимальные баллы',
            'reviewer_comment' => 'Комментарий проверяющего',
        ];
    }
}
