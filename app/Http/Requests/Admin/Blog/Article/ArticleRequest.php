<?php

namespace App\Http\Requests\Admin\Blog\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeArticle = $this->route('article');
        $articleId = is_object($routeArticle) ? ($routeArticle->id ?? null) : $routeArticle;

        $userId = (int) ($this->input('user_id') ?: ($this->user()?->id ?? 0));

        return [
            'sort'     => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],
            'left'     => ['required', 'boolean'],
            'main'     => ['required', 'boolean'],
            'right'    => ['required', 'boolean'],

            // moderation
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],

            // content
            'img'      => ['nullable', 'string'], // TEXT в миграции
            'locale'   => ['required', 'string', 'max:10'],

            'title' => [
                'required', 'string', 'max:255',
                Rule::unique('articles')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($articleId),
            ],

            'url' => [
                'required', 'string', 'max:500',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('articles')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($articleId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // вместо author
            'pseudonym' => ['nullable', 'string', 'max:255'],

            'published_at' => ['nullable', 'date'],

            // окно показа
            'show_from_at' => ['nullable', 'date'],
            'show_to_at'   => ['nullable', 'date', 'after_or_equal:show_from_at'],

            // views обычно системное поле (счётчик)
            // 'views' => ['nullable', 'integer', 'min:0'],

            'meta_title'    => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string'],

            // rubrics
            'rubrics'      => ['nullable', 'array'],
            'rubrics.*.id' => ['required_with:rubrics', 'integer', 'exists:rubrics,id'],

            // tags
            'tags'      => ['nullable', 'array'],
            'tags.*.id' => ['required_with:tags', 'integer', 'exists:tags,id'],

            // related articles
            'related_articles'      => ['nullable', 'array'],
            'related_articles.*.id' => ['required_with:related_articles', 'integer', 'exists:articles,id'],

            // videos (статья -> видео)
            'videos'      => ['nullable', 'array'],
            'videos.*.id' => ['required_with:videos', 'integer', 'exists:videos,id'],

            /**
             * === ИЗОБРАЖЕНИЯ НЕ ТРОГАЮ (как ты просил) ===
             */
            'images'             => ['nullable','array'],
            'images.*.id'        => [
                'nullable','integer',
                Rule::exists('article_images','id'),
                Rule::prohibitedIf(fn() => $this->isMethod('POST')),
            ],
            'images.*.order'     => ['nullable','integer','min:0'],
            'images.*.alt'       => ['nullable','string','max:255'],
            'images.*.caption'   => ['nullable','string','max:255'],
            'images.*.file'      => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages'      => ['sometimes','array'],
            'deletedImages.*'    => ['integer','exists:article_images,id'],
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
            'left'     => filter_var($this->input('left'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'main'     => filter_var($this->input('main'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'right'    => filter_var($this->input('right'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,

            'locale' => is_string($this->input('locale')) ? trim($this->input('locale')) : $this->input('locale'),
            'title'  => is_string($this->input('title')) ? trim($this->input('title')) : $this->input('title'),
            'url'    => is_string($this->input('url')) ? trim($this->input('url')) : $this->input('url'),

            'subtitle'  => is_string($this->input('subtitle')) ? trim($this->input('subtitle')) : $this->input('subtitle'),
            'short'     => is_string($this->input('short')) ? trim($this->input('short')) : $this->input('short'),
            'pseudonym' => is_string($this->input('pseudonym')) ? trim($this->input('pseudonym')) : $this->input('pseudonym'),
        ]);
    }
}
