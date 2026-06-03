<?php

namespace App\Http\Requests\Admin\School\SchoolQaThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQaThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['title', 'body', 'status', 'threadable_type'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['user_id', 'threadable_id', 'replies_count'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['is_locked', 'is_pinned'] as $field) {
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
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'threadable_type' => ['required', 'string', 'max:191'],
            'threadable_id' => ['required', 'integer', 'min:1'],

            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['open', 'closed', 'archived']),
            ],

            'is_locked' => ['sometimes', 'boolean'],
            'is_pinned' => ['sometimes', 'boolean'],

            'replies_count' => ['sometimes', 'integer', 'min:0'],
            'last_reply_at' => ['nullable', 'date'],
            'last_activity_at' => ['nullable', 'date'],

            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан автор темы.',
            'user_id.integer' => 'ID автора должен быть числом.',
            'user_id.exists' => 'Пользователь не найден.',

            'threadable_type.required' => 'Не указан тип сущности для темы.',
            'threadable_type.string' => 'Тип сущности должен быть строкой.',
            'threadable_type.max' => 'Тип сущности не должен превышать 191 символ.',

            'threadable_id.required' => 'Не указан ID сущности для темы.',
            'threadable_id.integer' => 'ID сущности должен быть числом.',
            'threadable_id.min' => 'ID сущности должен быть больше 0.',

            'title.required' => 'Укажите заголовок темы.',
            'title.string' => 'Заголовок должен быть строкой.',
            'title.max' => 'Заголовок не должен превышать 255 символов.',

            'body.string' => 'Текст темы должен быть строкой.',

            'status.in' => 'Недопустимый статус. Разрешены: open, closed, archived.',

            'is_locked.boolean' => 'Поле блокировки должно быть булевым.',
            'is_pinned.boolean' => 'Поле закрепления должно быть булевым.',

            'replies_count.integer' => 'Количество ответов должно быть числом.',
            'replies_count.min' => 'Количество ответов не может быть отрицательным.',

            'last_reply_at.date' => 'Дата последнего ответа указана некорректно.',
            'last_activity_at.date' => 'Дата последней активности указана некорректно.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Автор',
            'threadable_type' => 'Тип целевой сущности',
            'threadable_id' => 'ID целевой сущности',
            'title' => 'Заголовок',
            'body' => 'Сообщение',
            'status' => 'Статус',
            'is_locked' => 'Блокировка',
            'is_pinned' => 'Закрепление',
            'replies_count' => 'Количество ответов',
            'last_reply_at' => 'Последний ответ',
            'last_activity_at' => 'Последняя активность',
            'meta' => 'Метаданные',
        ];
    }
}
