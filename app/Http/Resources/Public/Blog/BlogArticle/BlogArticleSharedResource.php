<?php

namespace App\Http\Resources\Public\Blog\BlogArticle;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleImageResource;
use App\Http\Resources\Public\Blog\BlogRubric\BlogRubricSharedResource;
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
             *
             * sort нужен frontend-сортировке.
             */
            'sort' =>
                (int) $this->sort,

            'url' =>
                $this->url,

            'views' =>
                (int) $this->views,

            /**
             * Даты.
             *
             * published_at / created_at
             * нужны frontend-сортировке
             * dateAsc / dateDesc.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            'created_at' =>
                $this->created_at?->toISOString(),

            /**
             * Компактный Public-перевод.
             *
             * description нужен
             * frontend-поиску.
             *
             * pseudonym нужен карточкам
             * как публичное имя автора.
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
                ]
                : null,

            /**
             * Автор.
             *
             * Только данные,
             * реально используемые Public UI.
             *
             * Email не публикуем.
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
             * Controller должен заранее
             * загрузить images.media.
             */
            'images' =>
                BlogArticleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Публичные рубрики.
             *
             * Они нужны frontend-поиску:
             *
             * rubrics[].translation.title
             *
             * Resource не загружает relation
             * самостоятельно и поэтому
             * не создаёт N+1.
             */
            'rubrics' =>
                BlogRubricSharedResource::collection(
                    $this->whenLoaded(
                        'rubrics'
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
