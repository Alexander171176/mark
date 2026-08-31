<?php

namespace App\Http\Resources\Admin\Market\MarketCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCategorySharedResource extends JsonResource
{
    /**
     * Компактный ресурс категории для Index, дерева и select.
     *
     * Ресурс не выполняет SQL-запросов и работает
     * только с уже загруженными relations.
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
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Окно публикации */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,
            'children_count' => $this->whenCounted('children'),
            'images_count' => $this->whenCounted('images'),
            'products_count' => $this->whenCounted('products'),

            /** Перевод текущей локали */
            'translation' => $this->currentTranslation(),

            /** Родитель */
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent
                    ? new self($this->parent)
                    : null;
            }),

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /** Изображения */
            'images' => MarketCategoryImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Дочерние категории */
            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из уже загруженной коллекции.
     *
     * В Index Controller relation translations
     * должна быть ограничена currentLocale.
     */
    private function currentTranslation(): ?MarketCategoryTranslationResource
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $translation = $this->translations->first();

        return $translation
            ? new MarketCategoryTranslationResource($translation)
            : null;
    }
}
