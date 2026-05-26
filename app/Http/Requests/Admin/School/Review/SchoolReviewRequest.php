<?php

namespace App\Http\Requests\Admin\School\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['status', 'reviewable_type', 'title'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['rating', 'helpful_count', 'reported_count', 'reviewable_id', 'user_id'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('is_public')) {
            $data['is_public'] = filter_var(
                $this->input('is_public'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
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
        $review = $this->route('school_review')
            ?? $this->route('review')
            ?? $this->route('schoolReview');

        $id = is_object($review)
            ? $review->id
            : ($review ? (int) $review : null);

        return [
            // Автор
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            // Полиморфная связь
            'reviewable_type' => ['required', 'string', 'max:191'],
            'reviewable_id' => ['required', 'integer', 'min:1'],

            // Уникальность: 1 пользователь = 1 отзыв на сущность
            Rule::unique('school_reviews')
                ->where(fn ($q) => $q
                    ->where('user_id', $this->input('user_id'))
                    ->where('reviewable_type', $this->input('reviewable_type'))
                    ->where('reviewable_id', $this->input('reviewable_id'))
                )
                ->ignore($id),

            // Контент
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],

            // Модерация
            'status' => [
                'sometimes',
                'string',
                Rule::in(['pending', 'approved', 'rejected']),
            ],
            'is_public' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],

            // Системные счётчики
            'helpful_count' => ['sometimes', 'integer', 'min:0'],
            'reported_count' => ['sometimes', 'integer', 'min:0'],

            // Meta
            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан автор отзыва.',
            'user_id.exists' => 'Пользователь не найден.',

            'reviewable_type.required' => 'Не указан тип сущности.',
            'reviewable_id.required' => 'Не указан ID сущности.',

            'rating.required' => 'Укажите оценку.',
            'rating.between' => 'Оценка должна быть от 1 до 5.',

            'status.in' => 'Недопустимый статус (pending, approved, rejected).',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'пользователь',
            'reviewable_type' => 'тип сущности',
            'reviewable_id' => 'ID сущности',
            'rating' => 'оценка',
            'title' => 'заголовок',
            'body' => 'текст отзыва',
            'status' => 'статус',
            'is_public' => 'публичность',
            'published_at' => 'дата публикации',
        ];
    }
}
