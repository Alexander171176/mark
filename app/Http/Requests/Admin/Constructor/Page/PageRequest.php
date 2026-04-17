<?php

namespace App\Http\Requests\Admin\Constructor\Page;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['title','slug','excerpt','template','layout','locale','note'] as $f) {
            if ($this->filled($f)) {
                $merge[$f] = trim((string) $this->input($f));
            }
        }

        if ($this->filled('status')) {
            $merge['status'] = strtolower((string) $this->input('status'));
        }
        if ($this->filled('slug')) {
            // нормализуем слаг — убираем лишние пробелы, приводим к нижнему регистру
            $merge['slug'] = strtolower(preg_replace('~\s+~', '-', trim((string) $this->input('slug'))));
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
        $id = $this->route('page')?->id ?? $this->route('id');

        $statusEnum = ['draft','published','archived'];

        return [
            'parent_id'    => ['nullable','integer','exists:pages,id', Rule::notIn([$id])],
            'author_id'    => ['nullable','integer','exists:users,id'],

            'title'        => ['required','string','max:255'],
            'slug'         => [
                'required','string','max:255',
                Rule::unique('pages', 'slug')->ignore($id),
            ],
            'excerpt'      => ['nullable','string'],
            'content'      => ['nullable','string'],

            'status'       => ['required', Rule::in($statusEnum)],
            'activity'    => ['boolean'],
            'published_at' => ['nullable','date'],

            'template'     => ['nullable','string','max:255'],
            'layout'       => ['nullable','string','max:255'],
            'locale'       => ['nullable','string','max:10'],

            'sort'         => ['nullable','integer','min:0'],

            'meta'         => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id'    => 'родительская страница',
            'author_id'    => 'автор',
            'title'        => 'заголовок',
            'slug'         => 'слаг',
            'excerpt'      => 'краткое описание',
            'content'      => 'контент',
            'status'       => 'статус',
            'activity'     => 'активность',
            'published_at' => 'дата публикации',
            'template'     => 'шаблон',
            'layout'       => 'макет',
            'locale'       => 'локаль',
            'sort'     => 'позиция',
            'meta'         => 'метаданные',
        ];
    }
}
