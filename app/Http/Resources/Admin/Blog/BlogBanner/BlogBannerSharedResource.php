<?php

namespace App\Http\Resources\Admin\Blog\BlogBanner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBannerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Для Admin Index translations
         * заранее ограничивается выбранной локалью.
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

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /**
             * Служебный комментарий.
             *
             * Нужен frontend-поиску.
             */
            'comment' => $this->comment,

            /**
             * Перевод выбранной локали.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'link' => $translation->link,
                    'short' => $translation->short,
                ]
                : null,

            /**
             * Модерация.
             */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' =>
                $this->moderated_by,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /**
             * Владелец.
             *
             * Нужен отображению,
             * frontend-поиску и сортировке.
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
             * Email нужен frontend-поиску.
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
             * Контроллер обязан заранее
             * загрузить images.media.
             */
            'images' =>
                BlogBannerImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /**
             * Cover.
             */
            'cover_thumb_url' =>
                $cover?->thumb_url,

            'cover_webp_url' =>
                $cover?->webp_url,

            'cover_image_url' =>
                $cover?->image_url,

            /**
             * Count нужен frontend-сортировке.
             */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
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
