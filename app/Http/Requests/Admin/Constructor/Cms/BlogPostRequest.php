<?php

namespace App\Http\Requests\Admin\Constructor\Cms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['title','slug','excerpt','cover_image_url','locale'] as $f) {
            if ($this->filled($f)) {
                $merge[$f] = trim((string) $this->input($f));
            }
        }

        if ($this->filled('status')) {
            $merge['status'] = strtolower((string) $this->input('status'));
        }
        if ($this->filled('slug')) {
            $slug = preg_replace('~\s+~', '-', (string) $this->input('slug'));
            $merge['slug'] = strtolower(trim($slug, '-'));
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
        $id = $this->route('blog_post')?->id ?? $this->route('id');

        $statusEnum = ['draft','published','archived'];

        return [
            'author_id'       => ['nullable','integer','exists:users,id'],

            'title'           => ['required','string','max:255'],
            'slug'            => ['required','string','max:255', Rule::unique('blog_posts','slug')->ignore($id)],
            'excerpt'         => ['nullable','string'],
            'content'         => ['nullable','string'],

            'status'          => ['required', Rule::in($statusEnum)],
            'activity'        => ['boolean'],
            'published_at'    => ['nullable','date'],

            'cover_image_url' => ['nullable','url','max:2048'],
            'reading_time'    => ['nullable','integer','min:0'],

            'meta'            => ['nullable','array'],
            'locale'          => ['nullable','string','max:10'],
        ];
    }

    public function attributes(): array
    {
        return [
            'author_id'       => 'автор',
            'title'           => 'заголовок',
            'slug'            => 'слаг',
            'excerpt'         => 'краткое описание',
            'content'         => 'контент',
            'status'          => 'статус',
            'activity'        => 'активность',
            'published_at'    => 'дата публикации',
            'cover_image_url' => 'URL обложки',
            'reading_time'    => 'время чтения',
            'meta'            => 'метаданные',
            'locale'          => 'локаль',
        ];
    }
}
