<?php

namespace App\Http\Requests\Admin\School\AssignmentSubmission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['content','status','review_comment'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim((string)$this->input($k));
            }
        }

        if ($this->filled('attachments') && is_string($this->input('attachments'))) {
            $decoded = json_decode($this->input('attachments'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['attachments'] = $decoded;
            }
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        $currentId = $this->route('assignment_submission')?->id ?? null;

        return [
            'assignment_id' => [
                'required','integer','exists:assignments,id',
                Rule::unique('assignment_submissions', 'assignment_id')
                    ->where(fn($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($currentId),
            ],
            'lesson_id'     => ['nullable','integer','exists:lessons,id'],
            'user_id'       => ['required','integer','exists:users,id'],

            'content'       => ['nullable','string'],
            'attachments'   => ['nullable','array'],

            'status'        => ['sometimes','string', Rule::in(['submitted','under_review','graded','needs_changes'])],
            'score'         => ['nullable','numeric','between:0,100'],
            'review_comment'=> ['nullable','string'],

            'graded_by'     => ['nullable','integer','exists:users,id'],

            'submitted_at'  => ['nullable','date'],
            'graded_at'     => ['nullable','date','after_or_equal:submitted_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_id.required' => 'Укажите задание, к которому относится работа.',
            'assignment_id.exists'   => 'Указанное задание не найдено.',
            'assignment_id.unique'   => 'Эта работа для данного задания уже была отправлена этим пользователем.',

            'lesson_id.exists'       => 'Указанный урок не найден.',
            'user_id.required'       => 'Не указан пользователь, сдающий работу.',
            'user_id.exists'         => 'Указанный пользователь не найден.',

            'attachments.array'      => 'Поле attachments должно быть массивом (JSON).',
            'status.in'              => 'Недопустимый статус. Разрешённые: submitted, under_review, graded, needs_changes.',
            'score.numeric'          => 'Оценка должна быть числом.',
            'score.between'          => 'Оценка должна быть в диапазоне от :min до :max.',
            'graded_by.exists'       => 'Указанный проверяющий не найден.',
            'submitted_at.date'      => 'Поле submitted_at должно быть корректной датой.',
            'graded_at.date'         => 'Поле graded_at должно быть корректной датой.',
            'graded_at.after_or_equal' => 'graded_at не может быть раньше submitted_at.',
        ];
    }

    public function attributes(): array
    {
        return [
            'assignment_id'  => 'задание',
            'lesson_id'      => 'урок',
            'user_id'        => 'пользователь',
            'content'        => 'ответ',
            'attachments'    => 'вложения',
            'status'         => 'статус',
            'score'          => 'оценка',
            'review_comment' => 'комментарий проверяющего',
            'graded_by'      => 'проверяющий',
            'submitted_at'   => 'время сдачи',
            'graded_at'      => 'время проверки',
        ];
    }
}
