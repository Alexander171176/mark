<?php

namespace App\Http\Resources\Admin\Market\MarketAttribute;

use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeSharedResource extends JsonResource
{
    /**
     * Компактный ресурс характеристики
     * для Admin Index и связанных списков.
     *
     * Controller должен заранее загрузить:
     * - translations только для currentLocale;
     * - group.translations только для currentLocale;
     * - owner при необходимости;
     * - values_count при необходимости.
     *
     * SharedResource:
     * - не выполняет SQL;
     * - не выполняет fallback;
     * - не содержит translations[];
     * - не содержит moderator.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslation();

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_attribute_group_id' => $this->market_attribute_group_id !== null
                ? (int) $this->market_attribute_group_id
                : null,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            /** Основные данные */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,
            'type' => $this->type,
            'unit' => $this->unit,

            /** Настройки */
            'required' => (bool) $this->required,
            'filterable' => (bool) $this->filterable,
            'use_for_variants' => (bool) $this->use_for_variants,
            'visible' => (bool) $this->visible,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Перевод только currentLocale.
             *
             * Controller уже ограничивает
             * translations нужной локалью.
             */
            'translation' => $translation
                ? new MarketAttributeTranslationResource($translation)
                : null,

            /**
             * Группа характеристики.
             *
             * Controller должен загрузить
             * group.translations только currentLocale.
             */
            'group' => $this->whenLoaded(
                'group',
                fn () => $this->group
                    ? new MarketAttributeGroupSharedResource($this->group)
                    : null
            ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => $this->owner
                    ? [
                        'id' => (int) $this->owner->id,
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,
                        'profile_photo_url' => $this->owner->profile_photo_url,
                    ]
                    : null
            ),

            /** Количество значений */
            'values_count' => $this->whenCounted('values'),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение перевода из уже загруженной
     * Controller коллекции translations.
     *
     * Для Index Controller загружает только
     * currentLocale, поэтому первый элемент
     * является нужным переводом.
     *
     * Никакого fallback и SQL здесь нет.
     */
    protected function loadedTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}
