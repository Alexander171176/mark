<?php

namespace App\Http\Resources\Admin\Blog\BlogVideo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogVideoSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * В Admin Index translations заранее
         * ограничивается выбранной локалью.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение по pivot order.
         */
        $cover = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Основные поля.
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'is_private' => (bool) $this->is_private,
            'is_public' => (bool) $this->is_public,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'url' => $this->url,

            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'show_from_at' =>
                $this->show_from_at?->toISOString(),

            'show_to_at' =>
                $this->show_to_at?->toISOString(),

            'duration' => $this->duration !== null
                ? (int) $this->duration
                : null,

            /**
             * Источник видео.
             */
            'source_type' => $this->source_type,
            'video_url' => $this->video_url,
            'embed_url' => $this->embed_url,
            'embed_code' => $this->embed_code,
            'external_video_id' => $this->external_video_id,

            'views' => (int) $this->views,

            /**
             * Перевод выбранной локали.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'pseudonym' => $translation->pseudonym,
                ]
                : null,

            /**
             * Модерация.
             */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /**
             * Владелец.
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
             * Модератор.
             *
             * Нужен frontend-поиску.
             */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => [
                    'id' =>
                        $this->moderator?->id,

                    'name' =>
                        $this->moderator?->name,

                    'email' =>
                        $this->moderator?->email,
                ]
            ),

            /**
             * Изображения.
             *
             * Контроллер обязан загрузить
             * images.media.
             */
            'images' => BlogVideoImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Cover для мест, где удобнее
             * получить его напрямую.
             */
            'cover_thumb_url' =>
                $cover?->thumb_url,

            'cover_webp_url' =>
                $cover?->webp_url,

            'cover_image_url' =>
                $cover?->image_url,

            /**
             * Counts.
             */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'comments_count' => $this->when(
                isset($this->comments_count),
                fn () => (int) $this->comments_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            'related_videos_count' => $this->when(
                isset($this->related_videos_count),
                fn () => (int) $this->related_videos_count
            ),

            /**
             * Даты нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
