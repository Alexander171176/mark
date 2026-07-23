<?php

namespace App\Http\Resources\Admin\Market\MarketAttribute;

use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
use App\Http\Resources\Admin\Market\MarketAttributeValue\MarketAttributeValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeResource extends JsonResource
{
    /**
     * Преобразование характеристики маркетплейса в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        /**
         * Текущий перевод характеристики с резервной локалью.
         */
        $currentTranslation = $this->whenLoaded(
            'translations',
            function () use ($currentLocale) {
                return $this->translations->firstWhere(
                    'locale',
                    $currentLocale
                )
                    ?: $this->translations->firstWhere(
                        'locale',
                        config('app.fallback_locale', 'ru')
                    )
                        ?: $this->translations->first();
            }
        );

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_attribute_group_id' =>
                $this->market_attribute_group_id !== null
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

            /** Настройки характеристики */
            'required' => (bool) $this->required,
            'filterable' => (bool) $this->filterable,

            /**
             * Используется ли характеристика
             * для формирования вариантов товара.
             */
            'use_for_variants' => (bool) $this->use_for_variants,
            'is_used_for_variants' => (bool) $this->use_for_variants,

            'visible' => (bool) $this->visible,

            /** Отображение, сортировка и активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_active' => (bool) $this->activity,

            /** Статус публикации */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' =>
                (int) $this->moderation_status === 0,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'is_rejected' =>
                (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' => $this->moderation_note,

            /** Дата публикации и окно показа */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketAttributeTranslationResource(
                    $currentTranslation
                )
                : null,

            /** Все переводы */
            'translations' =>
                MarketAttributeTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /**
             * Значения характеристики.
             *
             * Например:
             * - цвет: чёрный, белый, красный;
             * - размер: S, M, L, XL;
             * - память: 128 ГБ, 256 ГБ.
             *
             * Поле появится только при загруженной связи values.
             */
            'values' =>
                MarketAttributeValueResource::collection(
                    $this->whenLoaded('values')
                ),

            /** Счётчики */
            'values_count' =>
                $this->whenCounted('values'),

            /**
             * Количество использований характеристики
             * в вариантах товаров.
             */
            'variant_values_count' =>
                $this->whenCounted('variantValues'),

            /**
             * Характеристика фактически используется
             * хотя бы в одном варианте.
             *
             * Поле появится только при загруженном счётчике.
             */
            'is_used_in_variants' => $this->when(
                isset($this->variant_values_count),
                fn () => (int) $this->variant_values_count > 0
            ),

            /** Группа характеристики */
            'group' => new MarketAttributeGroupSharedResource(
                $this->whenLoaded('group')
            ),

            /** Создатель характеристики */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    if (! $this->owner) {
                        return null;
                    }

                    return [
                        'id' => (int) $this->owner->id,
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,
                        'profile_photo_url' =>
                            $this->owner->profile_photo_url,
                    ];
                }
            ),

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                function () {
                    if (! $this->moderator) {
                        return null;
                    }

                    return [
                        'id' => (int) $this->moderator->id,
                        'name' => $this->moderator->name,
                        'email' => $this->moderator->email,
                        'profile_photo_url' =>
                            $this->moderator->profile_photo_url,
                    ];
                }
            ),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
