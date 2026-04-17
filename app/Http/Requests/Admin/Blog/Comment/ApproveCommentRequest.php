<?php

namespace App\Http\Requests\Admin\Blog\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Пока не трогаем права (как ты просил: контроллеры/маршруты потом)
        return true;

        // Пример на будущее:
        // return $this->user()->can('approve comments');
    }

    public function rules(): array
    {
        return [
            // любой из вариантов
            'approved' => ['sometimes', 'boolean'],
            'moderation_status' => ['sometimes', 'integer', Rule::in([0, 1, 2])],
        ];
    }

    protected function prepareForValidation(): void
    {
        // если прислали approved — конвертим в moderation_status
        if ($this->has('approved') && ! $this->has('moderation_status')) {
            $approved = filter_var($this->input('approved'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($approved !== null) {
                $this->merge([
                    'moderation_status' => $approved ? 1 : 0, // false -> pending (или 2 если “reject”, это уже контроллер решит)
                ]);
            }
        }

        if ($this->has('moderation_status') && $this->input('moderation_status') !== null) {
            $this->merge([
                'moderation_status' => is_numeric($this->input('moderation_status'))
                    ? (int) $this->input('moderation_status')
                    : $this->input('moderation_status'),
            ]);
        }
    }
}
