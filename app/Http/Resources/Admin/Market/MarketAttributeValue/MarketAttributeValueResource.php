<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeValue;

use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeValueResource extends JsonResource
{
    /**
     * Преобразование значения характеристики в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        /**
         * Текущий перевод значения характеристики
         * с резервной локалью.
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

            'market_attribute_id' =>
                $this->market_attribute_id !== null
                    ? (int) $this->market_attribute_id
                    : null,

            /** Основные данные значения */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

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
                ? new MarketAttributeValueTranslationResource(
                    $currentTranslation
                )
                : null,

            /** Все переводы */
            'translations' =>
                MarketAttributeValueTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /**
             * Количество использований значения
             * в вариантах товаров.
             */
            'variant_values_count' =>
                $this->whenCounted('variantValues'),

            /**
             * Используется ли значение хотя бы
             * в одном варианте товара.
             *
             * Поле возвращается при загруженном
             * счётчике variantValues.
             */
            'is_used_in_variants' => $this->when(
                isset($this->variant_values_count),
                fn () => (int) $this->variant_values_count > 0
            ),

            /** Родительская характеристика */
            'attribute' => new MarketAttributeSharedResource(
                $this->whenLoaded('attribute')
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
