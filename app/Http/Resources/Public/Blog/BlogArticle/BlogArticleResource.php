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

        /**
         * Public Controller заранее загружает
         * только текущую локаль + fallback.
         *
         * Сам fallback централизован
         * в модели BlogArticle.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            : null;

        return [
            'id' => $this->id,

            /**
             * Основные публичные поля.
             */
            'url' => $this->url,
            'views' => (int) $this->views,

            /**
             * Даты нужны:
             *
             * - SEO;
             * - BlogPosting;
             * - article:published_time;
             * - article:modified_time.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),

            /**
             * Полный Public-перевод
             * для страницы Show + SEO.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,

                    'pseudonym' =>
                        $translation->pseudonym,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_desc' =>
                        $translation->meta_desc,
                ]
                : null,

            /**
             * Публичные данные автора.
             *
             * Email наружу не отдаём.
             */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' =>
                            $this->owner?->id,

                        'name' =>
                            $this->owner?->name,

                        'profile_photo_url' =>
                            $this->owner
                                ?->profile_photo_url,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * Controller заранее загружает
             * images.media.
             */
            'images' =>
                BlogArticleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Только публичные теги.
             *
             * Public Controller обязан
             * применить к relation forPublic().
             */
            'tags' =>
                BlogTagSharedResource::collection(
                    $this->whenLoaded(
                        'tags'
                    )
                ),

            /**
             * Лайки.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () =>
                (int) $this->likes_count
            ),

            /**
             * Лайк текущего пользователя.
             *
             * Добавляется Controller
             * через WithUserLikesTrait.
             */
            'already_liked' =>
                (bool) (
                    $this->already_liked
                    ?? false
                ),
        ];
    }
}
