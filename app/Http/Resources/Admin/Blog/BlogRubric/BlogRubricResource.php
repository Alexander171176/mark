<?php

namespace App\Http\Resources\Admin\Blog\BlogRubric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogRubricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $currentTranslation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Дерево
             */
            'parent_id' => $this->parent_id,
            'level' => $this->level,
            'in_menu' => (bool) $this->in_menu,

            /**
             * Общие поля
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'icon' => $this->icon,
            'url' => $this->url,
            'views' => (int) $this->views,

            /**
             * Модерация
             */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Текущий перевод (для удобства фронта)
             */
            'translation' => $currentTranslation
                ? new BlogRubricTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => BlogRubricTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Relations
             */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),

            /**
             * Изображения
             */
            'images' => BlogRubricImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->whenCounted('images'),

            /**
             * Дети
             */
            'children' => BlogRubricResource::collection($this->whenLoaded('children')),

            /**
             * Количество статей
             */
            'articles_count' => (int) ($this->articles_count ?? 0),

            /**
             * Timestamps
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
