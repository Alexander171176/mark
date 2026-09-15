<?php

namespace App\Http\Resources\Public\Blog\BlogVideo;

use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogVideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Контроллер заранее загружает:
         *
         * current locale + fallback locale.
         *
         * Логику выбора эффективного перевода
         * держим в модели BlogVideo.
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

            'duration' => $this->duration !== null
                ? (int) $this->duration
                : null,

            'views' => (int) $this->views,

            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            /**
             * Источник видео.
             *
             * VideoPlayer получает уже
             * подготовленный Public-объект.
             */
            'source_type' =>
                $this->source_type,

            'video_url' =>
                $this->video_url,

            'embed_url' =>
                $this->embed_url,

            'embed_code' =>
                $this->embed_code,

            'external_video_id' =>
                $this->external_video_id,

            /**
             * Полный эффективный
             * Public-перевод:
             *
             * current locale -> fallback.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

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
             * Автор.
             *
             * Только публичные данные.
             */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => [
                    'id' =>
                        $this->owner?->id,

                    'name' =>
                        $this->owner?->name,

                    'profile_photo_url' =>
                        $this->owner
                            ?->profile_photo_url,
                ]
            ),

            /**
             * Изображения.
             *
             * Общий ImageResource сохраняем:
             * изображения работают через
             * общую Spatie Media архитектуру.
             */
            'images' =>
                BlogVideoImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Рекомендованные видео
             * в кратком Public-формате.
             */
            'related_videos' =>
                BlogVideoSharedResource::collection(
                    $this->whenLoaded(
                        'relatedVideos'
                    )
                ),

            /**
             * Public statistics.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () =>
                (int) $this->likes_count
            ),

            'comments_count' => $this->when(
                isset($this->comments_count),
                fn () =>
                (int) $this->comments_count
            ),

            /**
             * Лайк текущего пользователя.
             */
            'already_liked' =>
                (bool) (
                    $this->already_liked
                    ?? false
                ),

            /**
             * Даты нужны Show / SEO:
             *
             * created_at используется
             * как fallback для published_at;
             *
             * updated_at используется
             * для VideoObject.dateModified.
             */
            'created_at' =>
                $this->created_at
                    ?->toISOString(),

            'updated_at' =>
                $this->updated_at
                    ?->toISOString(),
        ];
    }
}
