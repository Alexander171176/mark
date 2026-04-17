<?php

namespace App\Http\Requests\Admin\School\QaThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QaThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['title','body','status','threadable_type'] as $k) {
            if ($this->filled($k)) $merge[$k] = trim((string)$this->input($k));
        }

        foreach (['is_locked','is_pinned'] as $k) {
            if ($this->filled($k)) {
                $merge[$k] = filter_var($this->input($k), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }

        foreach (['replies_count'] as $k) {
            if ($this->filled($k)) $merge[$k] = (int)$this->input($k);
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            // автор и цель
            'user_id'         => ['required','integer','exists:users,id'],
            'threadable_type' => ['required','string','max:191'],
            'threadable_id'   => ['required','integer','min:1'],

            // контент
            'title'           => ['required','string','max:255'],
            'body'            => ['nullable','string'],

            // статус/флаги
            'status'          => ['sometimes','string', Rule::in(['open','closed','archived'])],
            'is_locked'       => ['sometimes','boolean'],
            'is_pinned'       => ['sometimes','boolean'],

            // метрики/даты
            'replies_count'   => ['sometimes','integer','min:0'],
            'last_reply_at'   => ['nullable','date'],
            'last_activity_at'=> ['nullable','date'],

            // метаданные
            'meta'            => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'         => 'Не указан автор темы.',
            'user_id.exists'           => 'Пользователь не найден.',
            'threadable_type.required' => 'Не указан тип сущности для темы.',
            'threadable_id.required'   => 'Не указан идентификатор сущности для темы.',
            'title.required'           => 'Укажите заголовок темы.',
            'status.in'                => 'Недопустимый статус. Разрешены: open, closed, archived.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'         => 'пользователь',
            'threadable_type' => 'тип целевой сущности',
            'threadable_id'   => 'ID целевой сущности',
            'title'           => 'заголовок',
            'body'            => 'сообщение',
            'status'          => 'статус',
            'is_locked'       => 'блокировка',
            'is_pinned'       => 'закреплена',
        ];
    }
}
