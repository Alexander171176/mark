<?php

namespace App\Http\Resources\Admin\Market\MarketAttribute;

use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
use App\Http\Resources\Admin\Market\MarketAttributeValue\MarketAttributeValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeResource extends JsonResource
{
    /**
     * Полный ресурс характеристики маркетплейса.
     *
     * Используется преимущественно
     * для Edit / Details.
     *
     * Controller при необходимости должен заранее загрузить:
     * - translations — все переводы характеристики;
     * - group.translations;
     * - owner;
     * - moderator;
     * - values;
     * - values_count;
     * - variant_values_count.
     *
     * Ресурс самостоятельно SQL-запросов не выполняет.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_attribute_group_id' => $this->market_attribute_group_id !== null
                ? (int) $this->market_attribute_group_id
                : null,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            /** Основные данные характеристики */
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

            /** Совместимые вычисляемые флаги */
            'is_used_for_variants' => (bool) $this->use_for_variants,

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

            /** Текущий перевод */
            'translation' => $translation
                ? new MarketAttributeTranslationResource($translation)
                : null,

            /**
             * Все переводы.
             *
             * Edit Controller загружает
             * полную коллекцию translations.
             */
            'translations' => MarketAttributeTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Значения характеристики.
             *
             * Поле присутствует только,
             * если relation values загружена Controller.
             */
            'values' => MarketAttributeValueResource::collection(
                $this->whenLoaded('values')
            ),

            /** Счётчики */
            'values_count' => $this->whenCounted('values'),

            'variant_values_count' => $this->whenCounted(
                'variantValues'
            ),

            /**
             * Используется ли характеристика
             * хотя бы в одном варианте товара.
             *
             * Поле присутствует только,
             * если загружен variant_values_count.
             */
            'is_used_in_variants' => $this->when(
                isset($this->variant_values_count),
                fn () => (int) $this->variant_values_count > 0
            ),

            /**
             * Группа характеристики.
             *
             * MarketAttributeGroupSharedResource
             * читает только уже загруженный translation.
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

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => $this->moderator
                    ? [
                        'id' => (int) $this->moderator->id,
                        'name' => $this->moderator->name,
                        'email' => $this->moderator->email,
                        'profile_photo_url' => $this->moderator->profile_photo_url,
                    ]
                    : null
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из уже загруженной
     * полной коллекции translations.
     *
     * Порядок:
     * - текущая локаль;
     * - fallback locale;
     * - первый доступный перевод.
     *
     * Метод SQL-запросов не выполняет.
     */
    protected function currentTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', $fallbackLocale)
            ?? $this->translations->first();
    }
}
