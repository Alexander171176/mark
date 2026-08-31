<?php

namespace App\Http\Resources\Admin\Market\MarketCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCategoryResource extends JsonResource
{
    /**
     * Полный ресурс категории.
     *
     * Используется для Edit/details.
     * Ресурс не выполняет SQL самостоятельно.
     */
    public function toArray(Request $request): array
    {
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
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Окно публикации */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,

            'images_count' => $this->whenCounted('images'),
            'children_count' => $this->whenCounted('children'),
            'products_count' => $this->whenCounted('products'),

            /** Текущий перевод */
            'translation' => $this->currentTranslation(),

            /** Все переводы */
            'translations' => MarketCategoryTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Родитель */
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent
                    ? new MarketCategorySharedResource($this->parent)
                    : null;
            }),

            /** Дети */
            'children' => MarketCategorySharedResource::collection(
                $this->whenLoaded('children')
            ),

            /** Рекурсивное дерево */
            'children_recursive' => MarketCategorySharedResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /** Модератор */
            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                    'email' => $this->moderator?->email,
                ];
            }),

            /** Изображения */
            'images' => MarketCategoryImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из уже загруженных translations.
     *
     * Для полного Edit Resource translations обычно
     * загружаются целиком, поэтому здесь допустим fallback.
     */
    private function currentTranslation(): ?MarketCategoryTranslationResource
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $translation = $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallbackLocale)
                ?: $this->translations->first();

        return $translation
            ? new MarketCategoryTranslationResource($translation)
            : null;
    }
}
