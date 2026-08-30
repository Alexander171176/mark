<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeGroup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeGroupSharedResource extends JsonResource
{
    /**
     * Компактный ресурс группы характеристик.
     *
     * Используется преимущественно
     * для Index / списков / связанных данных.
     *
     * Controller должен заранее загрузить:
     * - translations — только текущую локаль;
     * - owner;
     * - attributes_count при необходимости.
     *
     * Ресурс не выполняет SQL-запросов
     * и не выполняет fallback-запросов.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslation();

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            /** Основные данные */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderation_note' =>
                $this->moderation_note,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            /** Период публикации */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Перевод текущей локали */
            'translation' => $translation
                ? new MarketAttributeGroupTranslationResource(
                    $translation
                )
                : null,

            /** Количество характеристик */
            'attributes_count' =>
                $this->whenCounted(
                    'attributes'
                ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => $this->owner
                    ? [
                        'id' => (int) $this->owner->id,
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,

                        'profile_photo_url' =>
                            $this->owner->profile_photo_url,
                    ]
                    : null
            ),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получить перевод только из уже
     * загруженной коллекции translations.
     *
     * Для Index Controller загружает
     * translations с ограничением currentLocale,
     * поэтому первый элемент коллекции
     * является нужным переводом.
     *
     * Метод не выполняет SQL.
     */
    protected function loadedTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}
