<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeValue;

use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeValueSharedResource extends JsonResource
{
    /**
     * Компактный ресурс значения характеристики
     * для списков и связанных сущностей.
     *
     * Controller должен заранее загрузить:
     * - translations, ограниченные currentLocale;
     * - attribute.translations, ограниченные currentLocale;
     * - moderator, если он требуется списку.
     *
     * Ресурс не выполняет SQL
     * и не реализует fallback локали.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslation();

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_attribute_id' => $this->market_attribute_id !== null
                ? (int) $this->market_attribute_id
                : null,

            /** Основные данные значения */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_active' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Период публикации */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /**
             * Перевод текущей локали.
             *
             * Controller заранее ограничивает
             * translations текущей локалью.
             */
            'translation' => $translation
                ? new MarketAttributeValueTranslationResource($translation)
                : null,

            /**
             * Родительская характеристика.
             *
             * Её translations также должны быть
             * заранее ограничены currentLocale.
             */
            'attribute' => $this->whenLoaded(
                'attribute',
                fn () => $this->attribute
                    ? new MarketAttributeSharedResource($this->attribute)
                    : null
            ),

            /**
             * Модератор нужен Index для одинакового
             * frontend/server поиска.
             */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => $this->moderator
                    ? [
                        'id' => (int) $this->moderator->id,
                        'name' => $this->moderator->name,
                        'email' => $this->moderator->email,
                    ]
                    : null
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение перевода только
     * из уже загруженной коллекции.
     *
     * Controller гарантирует,
     * что translations содержит
     * только currentLocale.
     *
     * Метод SQL-запросов не выполняет.
     */
    protected function loadedTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}
