<?php

namespace App\Http\Resources\Public\Blog\BlogVideo;

use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogVideoSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Public-запрос заранее загружает
         * максимум две локали:
         *
         * - current locale;
         * - fallback locale.
         *
         * Эффективный перевод выбирает
         * сама модель BlogVideo.
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
             * sort нужен frontend-режиму
             * для локальной сортировки.
             */
            'sort' =>
                (int) $this->sort,

            'url' =>
                $this->url,

            'duration' =>
                $this->duration !== null
                    ? (int) $this->duration
                    : null,

            'views' =>
                (int) $this->views,

            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            /**
             * Источник видео.
             *
             * VideoPlayer может использовать
             * SharedResource напрямую:
             *
             * Index;
             * related videos;
             * SectionVideoList.
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
             * Эффективный перевод:
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

                    'pseudonym' =>
                        $translation->pseudonym,
                ]
                : null,

            /**
             * Автор.
             *
             * Email в Public не передаём.
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
             * Контроллер заранее
             * загружает images.media.
             */
            'images' =>
                BlogVideoImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Статистика.
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
             * Нужен frontend-сортировке
             * как fallback для published_at.
             */
            'created_at' =>
                $this->created_at
                    ?->toISOString(),
        ];
    }
}
