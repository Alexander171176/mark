<?php

namespace App\Http\Resources\Admin\Market\MarketCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCategoryResource extends JsonResource
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

            /** Дерево */
            'parent_id' => $this->parent_id,
            'level' => (int) $this->level,
            'is_root' => $this->parent_id === null,

            /** Основные данные */
            'url' => $this->url,
            'icon' => $this->icon,

            /** Отображение */
            'in_menu' => (bool) $this->in_menu,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketCategoryTranslationResource($currentTranslation)
                : null,

            /** Все переводы */
            'translations' => MarketCategoryTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Relations */
            'parent' => new MarketCategorySharedResource(
                $this->whenLoaded('parent')
            ),

            'children' => MarketCategorySharedResource::collection(
                $this->whenLoaded('children')
            ),

            'children_recursive' => MarketCategorySharedResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),

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

            /** Изображения */
            'images' => MarketCategoryImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Counts */
            'images_count' => $this->whenCounted('images'),
            'children_count' => $this->whenCounted('children'),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
