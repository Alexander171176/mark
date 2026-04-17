<?php

namespace App\Http\Requests\Admin\School\QaMessage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QaMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('body')) {
            $merge['body'] = trim((string) $this->input('body'));
        }

        foreach (['is_private','is_pinned'] as $flag) {
            if ($this->filled($flag)) {
                $merge[$flag] = filter_var(
                    $this->input($flag),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'thread_id'   => ['required','integer','exists:qa_threads,id'],
            'user_id'     => ['required','integer','exists:users,id'],

            'parent_id'   => ['nullable','integer','exists:qa_messages,id'],

            'body'        => ['required','string'],

            'is_private'  => ['sometimes','boolean'],
            'is_pinned'   => ['sometimes','boolean'],

            // Обычно сервис сам считает, но позволим передавать при импортах/сидерах
            'replies_count' => ['sometimes','integer','min:0'],

            'edited_at'   => ['nullable','date'],
            'meta'        => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'thread_id.required' => 'Не указана тема, к которой относится сообщение.',
            'thread_id.exists'   => 'Тема не найдена.',
            'user_id.required'   => 'Не указан автор сообщения.',
            'user_id.exists'     => 'Автор не найден.',
            'body.required'      => 'Текст сообщения обязателен.',
        ];
    }

    public function attributes(): array
    {
        return [
            'thread_id'    => 'тема',
            'user_id'      => 'пользователь',
            'parent_id'    => 'родительское сообщение',
            'body'         => 'текст сообщения',
            'is_private'   => 'приватность',
            'is_pinned'    => 'закреплено',
            'edited_at'    => 'время редактирования',
        ];
    }
}
