<?php

namespace App\Http\Resources\Admin\Blog\BlogTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Для Admin Index relation translations
         * предварительно ограничивается выбранной локалью.
         *
         * В других административных сценариях
         * relation может содержать все локали.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Основные поля.
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'icon' => $this->icon,
            'slug' => $this->slug,
            'views' => (int) $this->views,

            /**
             * Перевод выбранной локали.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
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
             * Модератор.
             *
             * Пока нужен frontend-поиску Index.
             */
            'moderator' => $this->whenLoaded(
                'moderator',
                function () {
                    return [
                        'id' => $this->moderator?->id,
                        'name' => $this->moderator?->name,
                        'email' => $this->moderator?->email,
                    ];
                }
            ),

            /**
             * Количество статей.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
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
