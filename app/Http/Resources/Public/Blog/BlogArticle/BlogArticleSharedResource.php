<?php

namespace App\Http\Resources\Public\Blog\BlogArticle;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * В публичных запросах translations
         * содержит максимум:
         *
         * - текущую локаль;
         * - fallback ru.
         */
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

            /**
             * Основные публичные поля.
             */
            'sort' => (int) $this->sort,
            'url' => $this->url,
            'views' => (int) $this->views,

            /**
             * Даты.
             *
             * published_at / created_at нужны
             * frontend-сортировке.
             */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'created_at' =>
                $this->created_at?->toISOString(),

            /**
             * Перевод текущей локали
             * или fallback ru.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'pseudonym' => $translation->pseudonym,
                ]
                : null,

            /**
             * Автор.
             *
             * Email нужен frontend-поиску.
             */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
                        'email' => $this->owner?->email,
                        'profile_photo_url' =>
                            $this->owner?->profile_photo_url,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * Контроллер должен заранее
             * загрузить images.media.
             */
            'images' => BlogArticleImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Лайки.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            /**
             * Лайк текущего пользователя.
             *
             * Добавляется через WithUserLikesTrait
             * с помощью withExists().
             */
            'already_liked' =>
                (bool) ($this->already_liked ?? false),
        ];
    }
}
