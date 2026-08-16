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

            'duration' => $this->duration !== null
                ? (int) $this->duration
                : null,

            'views' => (int) $this->views,

            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            /**
             * Источник видео.
             *
             * VideoPlayer работает уже
             * с готовым публичным объектом.
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
             * Текущий перевод
             * или fallback ru.
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
             * Email оставляем,
             * потому что Public Index
             * использует его в frontend-поиске.
             */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => [
                    'id' =>
                        $this->owner?->id,

                    'name' =>
                        $this->owner?->name,

                    'email' =>
                        $this->owner?->email,

                    'profile_photo_url' =>
                        $this->owner?->profile_photo_url,
                ]
            ),

            /**
             * Изображения.
             *
             * Контроллер обязан заранее
             * загрузить images.media.
             */
            'images' => BlogVideoImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Статистика.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'comments_count' => $this->when(
                isset($this->comments_count),
                fn () => (int) $this->comments_count
            ),

            /**
             * Признак лайка текущего пользователя.
             */
            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            /**
             * Нужен frontend-сортировке
             * как fallback для published_at.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}
