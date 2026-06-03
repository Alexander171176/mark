<?php

namespace App\Http\Requests\Admin\School\SchoolAssignmentSubmission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['content', 'status', 'review_comment'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['school_assignment_id', 'school_lesson_id', 'user_id', 'graded_by'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('score')) {
            $data['score'] = is_string($this->input('score'))
                ? str_replace(',', '.', trim($this->input('score')))
                : $this->input('score');
        }

        if ($this->filled('attachments') && is_string($this->input('attachments'))) {
            $decoded = json_decode($this->input('attachments'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['attachments'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $submission = $this->route('school_assignment_submission')
            ?? $this->route('assignmentSubmission')
            ?? $this->route('schoolAssignmentSubmission');

        $id = is_object($submission)
            ? $submission->id
            : ($submission ? (int) $submission : null);

        return [
            'school_assignment_id' => [
                'required',
                'integer',
                Rule::exists('school_assignments', 'id'),
                Rule::unique('school_assignment_submissions', 'school_assignment_id')
                    ->where(fn ($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($id),
            ],

            'school_lesson_id' => ['nullable', 'integer', Rule::exists('school_lessons', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'content' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['submitted', 'under_review', 'graded', 'needs_changes']),
            ],

            'score' => ['nullable', 'numeric', 'between:0,100'],
            'review_comment' => ['nullable', 'string'],

            'graded_by' => ['nullable', 'integer', Rule::exists('users', 'id')],

            'submitted_at' => ['nullable', 'date'],
            'graded_at' => ['nullable', 'date', 'after_or_equal:submitted_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_assignment_id.required' => 'Укажите задание.',
            'school_assignment_id.exists' => 'Задание не найдено.',
            'school_assignment_id.unique' => 'Этот пользователь уже отправил работу по данному заданию.',

            'school_lesson_id.exists' => 'Урок не найден.',

            'user_id.required' => 'Укажите пользователя.',
            'user_id.exists' => 'Пользователь не найден.',

            'attachments.array' => 'Поле attachments должно быть JSON-массивом.',
            'status.in' => 'Недопустимый статус работы.',

            'score.numeric' => 'Оценка должна быть числом.',
            'score.between' => 'Оценка должна быть от 0 до 100.',

            'graded_by.exists' => 'Проверяющий не найден.',

            'submitted_at.date' => 'Дата сдачи указана некорректно.',
            'graded_at.date' => 'Дата проверки указана некорректно.',
            'graded_at.after_or_equal' => 'Дата проверки не может быть раньше даты сдачи.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_assignment_id' => 'Задание',
            'school_lesson_id' => 'Урок',
            'user_id' => 'Пользователь',
            'content' => 'Ответ',
            'attachments' => 'Вложения',
            'status' => 'Статус',
            'score' => 'Оценка',
            'review_comment' => 'Комментарий проверяющего',
            'graded_by' => 'Проверяющий',
            'submitted_at' => 'Дата сдачи',
            'graded_at' => 'Дата проверки',
        ];
    }
}
