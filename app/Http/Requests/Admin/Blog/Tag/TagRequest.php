<?php

namespace App\Http\Requests\Admin\Blog\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // route('tag') может быть моделью или id — оставляем универсально
        $routeTag = $this->route('tag');
        $tagId = is_object($routeTag) ? ($routeTag->id ?? null) : $routeTag;

        // По миграции уникальность в рамках user_id + locale
        $userId = (int) ($this->input('user_id') ?: ($this->user()?->id ?? 0));

        return [
            'sort'     => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            // moderation
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
            // moderated_by / moderated_at — НЕ принимаем с фронта

            'icon'   => ['nullable', 'string'],
            'locale' => ['required', 'string', 'max:10'],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($tagId),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tags')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($tagId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // views обычно системное поле (счётчик)
            // 'views' => ['nullable', 'integer', 'min:0'],

            'meta_title'    => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,

            'locale' => is_string($this->input('locale')) ? trim($this->input('locale')) : $this->input('locale'),
            'name'   => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'slug'   => is_string($this->input('slug')) ? trim($this->input('slug')) : $this->input('slug'),

            'subtitle' => is_string($this->input('subtitle')) ? trim($this->input('subtitle')) : $this->input('subtitle'),
            'short'    => is_string($this->input('short')) ? trim($this->input('short')) : $this->input('short'),
        ]);
    }
}
