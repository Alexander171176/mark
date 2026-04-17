<?php

namespace App\Http\Requests\Admin\School\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('status')) {
            $merge['status'] = trim((string)$this->input('status'));
        }
        if ($this->filled('reviewable_type')) {
            $merge['reviewable_type'] = trim((string)$this->input('reviewable_type'));
        }
        if ($this->filled('title')) {
            $merge['title'] = trim((string)$this->input('title'));
        }

        // числовые/булевые нормализации
        foreach (['rating','helpful_count','reported_count'] as $k) {
            if ($this->filled($k)) $merge[$k] = (int)$this->input($k);
        }
        if ($this->filled('is_public')) {
            $merge['is_public'] = filter_var($this->input('is_public'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        $currentId = $this->route('review')?->id ?? null;

        return [
            // автор и цель отзыва
            'user_id'         => ['required','integer','exists:users,id'],
            'reviewable_type' => ['required','string','max:191'],
            'reviewable_id'   => ['required','integer','min:1'],

            // уникальность одного отзыва на сущность от одного пользователя
            Rule::unique('reviews')
                ->where(fn($q) => $q
                    ->where('user_id', $this->input('user_id'))
                    ->where('reviewable_type', $this->input('reviewable_type'))
                    ->where('reviewable_id', $this->input('reviewable_id'))
                )
                ->ignore($currentId),

            // содержание
            'rating'          => ['required','integer','between:1,5'],
            'title'           => ['nullable','string','max:255'],
            'body'            => ['nullable','string'],

            // модерация/публикация
            'status'          => ['sometimes','string', Rule::in(['pending','approved','rejected'])],
            'is_public'       => ['sometimes','boolean'],
            'published_at'    => ['nullable','date'],

            // счётчики (обычно только системой)
            'helpful_count'   => ['sometimes','integer','min:0'],
            'reported_count'  => ['sometimes','integer','min:0'],

            // метаданные
            'meta'            => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'          => 'Не указан автор отзыва.',
            'user_id.exists'            => 'Пользователь не найден.',
            'reviewable_type.required'  => 'Не указан тип оцениваемой сущности.',
            'reviewable_id.required'    => 'Не указан идентификатор оцениваемой сущности.',
            'rating.required'           => 'Укажите оценку от 1 до 5.',
            'rating.between'            => 'Оценка должна быть в диапазоне 1–5.',
            'status.in'                 => 'Недопустимый статус. Разрешены: pending, approved, rejected.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'         => 'пользователь',
            'reviewable_type' => 'тип сущности',
            'reviewable_id'   => 'ID сущности',
            'rating'          => 'оценка',
            'title'           => 'заголовок',
            'body'            => 'текст отзыва',
            'status'          => 'статус',
            'is_public'       => 'публичность',
            'published_at'    => 'дата публикации',
        ];
    }
}
