<?php

namespace App\Http\Requests\Admin\School\SchoolQaMessage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQaMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->filled('body') && is_string($this->input('body'))) {
            $data['body'] = trim($this->input('body'));
        }

        foreach (['thread_id', 'user_id', 'parent_id', 'replies_count'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['is_private', 'is_pinned'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'thread_id' => ['required', 'integer', Rule::exists('school_qa_threads', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('school_qa_messages', 'id'),
            ],

            'body' => ['required', 'string'],

            'is_private' => ['sometimes', 'boolean'],
            'is_pinned' => ['sometimes', 'boolean'],

            'replies_count' => ['sometimes', 'integer', 'min:0'],

            'edited_at' => ['nullable', 'date'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'thread_id.required' => 'Не указана тема сообщения.',
            'thread_id.exists' => 'Тема не найдена.',

            'user_id.required' => 'Не указан автор сообщения.',
            'user_id.exists' => 'Пользователь не найден.',

            'parent_id.exists' => 'Родительское сообщение не найдено.',

            'body.required' => 'Текст сообщения обязателен.',
            'body.string' => 'Текст сообщения должен быть строкой.',

            'is_private.boolean' => 'Поле приватности должно быть булевым.',
            'is_pinned.boolean' => 'Поле закрепления должно быть булевым.',

            'replies_count.integer' => 'Количество ответов должно быть числом.',
            'replies_count.min' => 'Количество ответов не может быть отрицательным.',

            'edited_at.date' => 'Дата редактирования указана некорректно.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'thread_id' => 'Тема',
            'user_id' => 'Пользователь',
            'parent_id' => 'Родительское сообщение',
            'body' => 'Текст сообщения',
            'is_private' => 'Приватность',
            'is_pinned' => 'Закрепление',
            'replies_count' => 'Количество ответов',
            'edited_at' => 'Дата редактирования',
            'meta' => 'Метаданные',
        ];
    }
}
