<?php

namespace App\Http\Resources\Admin\Blog\BlogRubric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogRubricSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Основной перевод из уже загруженной коллекции.
         *
         * Index:
         * translations обычно содержит только текущую локаль.
         *
         * Create/Edit:
         * translations может содержать все локали.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->firstWhere(
            'locale',
            app()->getLocale()
        )
            ?? $this->translations->firstWhere(
            'locale',
            config('app.fallback_locale', 'ru')
        )
            ?? $this->translations->first()
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Дерево.
             */
            'parent_id' => $this->parent_id,
            'level' => (int) $this->level,

            /**
             * Основные поля.
             */
            'in_menu' => (bool) $this->in_menu,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'url' => $this->url,
            'icon' => $this->icon,
            'views' => (int) $this->views,

            /**
             * Основной перевод.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
                ]
                : null,

            /**
             * Все фактически загруженные переводы.
             *
             * Index:
             * обычно один перевод текущей локали.
             *
             * Create/Edit:
             * все переводы для динамического
             * переключения языка связанных сущностей.
             */
            'translations' => BlogRubricTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Модерация.
             */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Владелец.
             */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /**
             * Модератор.
             */
            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                    'email' => $this->moderator?->email,
                ];
            }),

            /**
             * Родительская рубрика.
             */
            'parent' => $this->whenLoaded(
                'parent',
                function () {
                    if (!$this->parent) {
                        return null;
                    }

                    $translation = $this->parent->relationLoaded('translations')
                        ? (
                        $this->parent->translations->firstWhere(
                            'locale',
                            app()->getLocale()
                        )
                            ?: $this->parent->translations->firstWhere(
                            'locale',
                            config('app.fallback_locale', 'ru')
                        )
                            ?: $this->parent->translations->first()
                        )
                        : null;

                    return [
                        'id' =>
                            $this->parent->id,

                        'translation' => $translation
                            ? [
                                'locale' =>
                                    $translation->locale,

                                'title' =>
                                    $translation->title,
                            ]
                            : null,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * media должен быть заранее eager-loaded
             * через images.media.
             */
            'images' => BlogRubricImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Counts.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            /**
             * Рекурсивные дети.
             */
            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            /**
             * Timestamps.
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
