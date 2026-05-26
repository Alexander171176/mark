<?php

namespace App\Http\Requests\Admin\School\Bookmark;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['folder', 'note', 'bookmarkable_type'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['user_id', 'bookmarkable_id', 'position'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('is_favorite')) {
            $data['is_favorite'] = filter_var(
                $this->input('is_favorite'),
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
        $bookmark = $this->route('school_bookmark')
            ?? $this->route('bookmark')
            ?? $this->route('schoolBookmark');

        $id = is_object($bookmark)
            ? $bookmark->id
            : ($bookmark ? (int) $bookmark : null);

        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'bookmarkable_type' => ['required', 'string', 'max:255'],
            'bookmarkable_id' => ['required', 'integer', 'min:1'],

            Rule::unique('school_bookmarks')
                ->where(fn ($q) => $q
                    ->where('user_id', $this->input('user_id'))
                    ->where('bookmarkable_type', $this->input('bookmarkable_type'))
                    ->where('bookmarkable_id', $this->input('bookmarkable_id'))
                )
                ->ignore($id),

            'is_favorite' => ['sometimes', 'boolean'],
            'folder' => ['nullable', 'string', 'max:64'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь.',
            'user_id.exists' => 'Пользователь не найден.',

            'bookmarkable_type.required' => 'Не указан тип объекта закладки.',
            'bookmarkable_type.max' => 'Тип объекта не должен превышать 255 символов.',

            'bookmarkable_id.required' => 'Не указан ID объекта закладки.',
            'bookmarkable_id.integer' => 'ID объекта должен быть числом.',
            'bookmarkable_id.min' => 'ID объекта должен быть больше 0.',

            'unique' => 'Такая закладка уже существует.',

            'is_favorite.boolean' => 'Поле избранного должно быть булевым.',
            'folder.max' => 'Название папки не должно превышать 64 символа.',
            'position.integer' => 'Позиция должна быть числом.',
            'position.min' => 'Позиция не может быть отрицательной.',
            'note.string' => 'Заметка должна быть текстом.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'bookmarkable_type' => 'Тип объекта',
            'bookmarkable_id' => 'Объект',
            'is_favorite' => 'Избранное',
            'folder' => 'Папка',
            'position' => 'Позиция',
            'note' => 'Заметка',
            'meta' => 'Метаданные',
        ];
    }
}
