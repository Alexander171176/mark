<?php

namespace App\Http\Requests\Admin\School\Bookmark;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('folder')) {
            $merge['folder'] = trim((string) $this->input('folder'));
        }
        if ($this->filled('note')) {
            $merge['note'] = trim((string) $this->input('note'));
        }

        foreach (['is_favorite'] as $flag) {
            if ($this->has($flag)) {
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
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Для ignore() попробуем вытащить id ресурса из роута (например, route-model binding: bookmark)
        $bookmarkId = $this->route('bookmark')?->id ?? $this->route('id');

        return [
            'user_id'           => ['required','integer','exists:users,id'],
            'bookmarkable_type' => ['required','string','max:255'],
            'bookmarkable_id'   => ['required','integer','min:1'],

            // Гарантия уникальности (user_id + type + id)
            Rule::unique('bookmarks')
                ->where(fn ($q) => $q
                    ->where('user_id', $this->input('user_id'))
                    ->where('bookmarkable_type', $this->input('bookmarkable_type'))
                    ->where('bookmarkable_id', $this->input('bookmarkable_id'))
                )
                ->ignore($bookmarkId),

            'is_favorite'       => ['sometimes','boolean'],
            'folder'            => ['nullable','string','max:64'],
            'position'          => ['sometimes','integer','min:0'],
            'note'              => ['nullable','string'],
            'meta'              => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'            => 'Не указан пользователь.',
            'user_id.exists'              => 'Пользователь не найден.',
            'bookmarkable_type.required'  => 'Не указан тип объекта закладки.',
            'bookmarkable_id.required'    => 'Не указан идентификатор объекта закладки.',
            'bookmarkable_id.min'         => 'Некорректный идентификатор объекта закладки.',
            'bookmarks_unique'            => 'Такая закладка уже существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'           => 'пользователь',
            'bookmarkable_type' => 'тип объекта',
            'bookmarkable_id'   => 'объект',
            'is_favorite'       => 'избранное',
            'folder'            => 'папка',
            'position'          => 'позиция',
            'note'              => 'заметка',
        ];
    }
}
