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

            'duration' => $this->duration !== null
                ? (int) $this->duration
                : null,

            'views' => (int) $this->views,

            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            /**
             * Источник видео.
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
             * Полный Public-перевод.
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
             */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => [
                    'id' =>
                        $this->owner?->id,

                    'name' =>
                        $this->owner?->name,

                    'profile_photo_url' =>
                        $this->owner?->profile_photo_url,
                ]
            ),

            /**
             * Изображения.
             */
            'images' => BlogVideoImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Связанные видео уже
             * в кратком Public-формате.
             */
            'related_videos' =>
                BlogVideoSharedResource::collection(
                    $this->whenLoaded(
                        'relatedVideos'
                    )
                ),

            /**
             * Counts.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'comments_count' => $this->when(
                isset($this->comments_count),
                fn () => (int) $this->comments_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}
