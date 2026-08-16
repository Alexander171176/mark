<?php

namespace App\Http\Resources\Public\Blog\BlogArticle;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleImageResource;
use App\Http\Resources\Public\Blog\BlogTag\BlogTagSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $locale
            )
                ?: $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            )
                ?: $this->translations->first()
            )
            : null;

        return [
            'id' => $this->id,

            'sort' => (int) $this->sort,
            'url' => $this->url,
            'views' => (int) $this->views,

            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            /**
             * Полный публичный перевод.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,

                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'pseudonym' => $translation->pseudonym,

                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
                        'profile_photo_url' =>
                            $this->owner?->profile_photo_url,
                    ];
                }
            ),

            'images' => BlogArticleImageResource::collection(
                $this->whenLoaded('images')
            ),

            'tags' => BlogTagSharedResource::collection(
                $this->whenLoaded('tags')
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
