<?php

namespace App\Http\Resources\Admin\Market\MarketProductVariant;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductVariantResource extends JsonResource
{
    /**
     * Преобразование варианта товара в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Текущий перевод варианта с резервной локалью.
         */
        $currentTranslation = $this->whenLoaded(
            'translations',
            function () use (
                $currentLocale,
                $fallbackLocale
            ) {
                return $this->translations->firstWhere(
                    'locale',
                    $currentLocale
                )
                    ?: $this->translations->firstWhere(
                        'locale',
                        $fallbackLocale
                    )
                        ?: $this->translations->first();
            }
        );

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_product_id' =>
                (int) $this->market_product_id,

            'currency_id' => $this->currency_id !== null
                ? (int) $this->currency_id
                : null,

            /** Торговые идентификаторы */
            'code' => $this->code,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            /**
             * Собственные цены варианта.
             *
             * Если поле null, вариант наследует значение
             * от родительского товара.
             */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' =>
                $this->wholesale_min_quantity !== null
                    ? (int) $this->wholesale_min_quantity
                    : null,

            /**
             * Эффективные цены.
             *
             * Это собственное значение варианта либо
             * унаследованное значение родительского товара.
             */
            'effective_price' =>
                $this->effectivePrice(),

            'effective_old_price' =>
                $this->effectiveOldPrice(),

            'effective_purchase_price' =>
                $this->effectivePurchasePrice(),

            'effective_wholesale_price' =>
                $this->effectiveWholesalePrice(),

            'effective_wholesale_min_quantity' =>
                $this->effectiveWholesaleMinQuantity(),

            /** Остатки конкретного варианта */
            'quantity' => (int) $this->quantity,
            'in_stock' => (bool) $this->in_stock,
            'has_stock' => $this->hasStock(),

            /**
             * Собственные физические параметры.
             *
             * Nullable-значение означает наследование
             * параметра от товара.
             */
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,

            /** Эффективные физические параметры */
            'effective_weight' =>
                $this->effectiveWeight(),

            'effective_length' =>
                $this->effectiveLength(),

            'effective_width' =>
                $this->effectiveWidth(),

            'effective_height' =>
                $this->effectiveHeight(),

            /** Настройки варианта */
            'is_default' => (bool) $this->is_default,

            'sort' => (int) $this->sort,

            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            /** Статус публикации */
            'status' => $this->status,

            'is_published' =>
                $this->isPublished(),

            'is_published_now' =>
                $this->isPublishedNow(),

            /** Модерация */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_pending' => $this->isPending(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),

            'moderated_by' =>
                $this->moderated_by !== null
                    ? (int) $this->moderated_by
                    : null,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /** Публикация и окно показа */
            'published_at' =>
                $this->published_at?->format('Y-m-d\TH:i'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Признаки наследования */
            'has_own_price' =>
                $this->hasOwnPrice(),

            'has_own_currency' =>
                $this->hasOwnCurrency(),

            'has_own_dimensions' =>
                $this->hasOwnDimensions(),

            /** Эффективная валюта */
            'effective_currency_id' =>
                $this->effectiveCurrencyId(),

            /**
             * Отображаемое название варианта.
             *
             * При отсутствии перевода будет сформировано
             * из названия товара и code или sku.
             */
            'display_title' =>
                $this->getDisplayTitle(
                    locale: $currentLocale,
                    fallback: $fallbackLocale
                ),

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketProductVariantTranslationResource(
                    $currentTranslation
                )
                : null,

            /** Все переводы */
            'translations' =>
                MarketProductVariantTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /** Родительский товар */
            'product' => new MarketProductSharedResource(
                $this->whenLoaded('product')
            ),

            /**
             * Собственная валюта варианта.
             *
             * Может быть null, если используется валюта товара.
             */
            'currency' => $this->whenLoaded(
                'currency',
                fn () => $this->currency
                    ? new CurrencyResource($this->currency)
                    : null
            ),

            /**
             * Эффективная валюта варианта.
             *
             * Возвращает валюту варианта или валюту товара.
             */
            'effective_currency' => $this->when(
                $this->relationLoaded('currency')
                && $this->relationLoaded('product'),
                function () {
                    $currency = $this->effectiveCurrency();

                    return $currency
                        ? new CurrencyResource($currency)
                        : null;
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

            /**
             * Значения характеристик варианта.
             *
             * Пример:
             * Цвет → Чёрный
             * Размер → XL
             */
            'values' => $this->whenLoaded(
                'values',
                function () use (
                    $currentLocale,
                    $fallbackLocale
                ) {
                    return $this->values
                        ->map(function ($item) use (
                            $currentLocale,
                            $fallbackLocale
                        ) {
                            $attributeTranslation = null;
                            $valueTranslation = null;

                            /**
                             * Перевод характеристики.
                             */
                            if (
                                $item->relationLoaded('attribute')
                                && $item->attribute
                                && $item->attribute
                                    ->relationLoaded('translations')
                            ) {
                                $attributeTranslation =
                                    $item->attribute->translations
                                        ->firstWhere(
                                            'locale',
                                            $currentLocale
                                        )
                                        ?: $item->attribute
                                        ->translations
                                        ->firstWhere(
                                            'locale',
                                            $fallbackLocale
                                        )
                                        ?: $item->attribute
                                            ->translations
                                            ->first();
                            }

                            /**
                             * Перевод выбранного значения.
                             */
                            if (
                                $item->relationLoaded('attributeValue')
                                && $item->attributeValue
                                && $item->attributeValue
                                    ->relationLoaded('translations')
                            ) {
                                $valueTranslation =
                                    $item->attributeValue->translations
                                        ->firstWhere(
                                            'locale',
                                            $currentLocale
                                        )
                                        ?: $item->attributeValue
                                        ->translations
                                        ->firstWhere(
                                            'locale',
                                            $fallbackLocale
                                        )
                                        ?: $item->attributeValue
                                            ->translations
                                            ->first();
                            }

                            return [
                                'id' => (int) $item->id,

                                'market_product_variant_id' =>
                                    (int) $item
                                        ->market_product_variant_id,

                                'market_attribute_id' =>
                                    (int) $item->market_attribute_id,

                                'market_attribute_value_id' =>
                                    (int) $item
                                        ->market_attribute_value_id,

                                'sort' => (int) $item->sort,

                                /**
                                 * Готовая отображаемая пара:
                                 * Цвет: Чёрный.
                                 */
                                'display_value' =>
                                    filled($attributeTranslation?->title)
                                    && filled($valueTranslation?->title)
                                        ? "{$attributeTranslation->title}: {$valueTranslation->title}"
                                        : (
                                    $valueTranslation?->title
                                        ?: $attributeTranslation?->title
                                    ),

                                /** Компактная характеристика */
                                'attribute' => (
                                    $item->relationLoaded('attribute')
                                    && $item->attribute
                                )
                                    ? [
                                        'id' =>
                                            (int) $item->attribute->id,

                                        'code' =>
                                            $item->attribute->code,

                                        'type' =>
                                            $item->attribute->type,

                                        'unit' =>
                                            $item->attribute->unit,

                                        'color' =>
                                            $item->attribute->color,

                                        'icon' =>
                                            $item->attribute->icon,

                                        'use_for_variants' =>
                                            (bool) $item
                                                ->attribute
                                                ->use_for_variants,

                                        'title' =>
                                            $attributeTranslation?->title,

                                        'subtitle' =>
                                            $attributeTranslation?->subtitle,

                                        'short' =>
                                            $attributeTranslation?->short,
                                    ]
                                    : null,

                                /** Компактное значение характеристики */
                                'attribute_value' => (
                                    $item->relationLoaded(
                                        'attributeValue'
                                    )
                                    && $item->attributeValue
                                )
                                    ? [
                                        'id' =>
                                            (int) $item
                                                ->attributeValue
                                                ->id,

                                        'market_attribute_id' =>
                                            (int) $item
                                                ->attributeValue
                                                ->market_attribute_id,

                                        'code' =>
                                            $item->attributeValue->code,

                                        'color' =>
                                            $item->attributeValue->color,

                                        'icon' =>
                                            $item->attributeValue->icon,

                                        'title' =>
                                            $valueTranslation?->title,

                                        'subtitle' =>
                                            $valueTranslation?->subtitle,

                                        'short' =>
                                            $valueTranslation?->short,
                                    ]
                                    : null,

                                'created_at' =>
                                    $item->created_at?->toISOString(),

                                'updated_at' =>
                                    $item->updated_at?->toISOString(),
                            ];
                        })
                        ->values();
                }
            ),

            /** Изображения варианта */
            'images' =>
                MarketProductVariantImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /**
             * Главное изображение варианта.
             *
             * Первое изображение определяется pivot.order.
             */
            'image' => $this->whenLoaded(
                'images',
                function () {
                    $image = $this->images->first();

                    return $image
                        ? new MarketProductVariantImageResource(
                            $image
                        )
                        : null;
                }
            ),

            /** Счётчики */
            'values_count' =>
                $this->whenCounted('values'),

            'images_count' =>
                $this->whenCounted('images'),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
