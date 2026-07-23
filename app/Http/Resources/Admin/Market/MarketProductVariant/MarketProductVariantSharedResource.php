<?php

namespace App\Http\Resources\Admin\Market\MarketProductVariant;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductVariantSharedResource extends JsonResource
{
    /**
     * Компактное представление варианта товара.
     *
     * Используется:
     * - внутри ресурса товара;
     * - в списках вариантов;
     * - в корзине;
     * - в заказах;
     * - в комплектах товаров;
     * - при выборе варианта пользователем.
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
             * Null означает наследование значения от товара.
             */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' =>
                $this->wholesale_min_quantity !== null
                    ? (int) $this->wholesale_min_quantity
                    : null,

            /** Эффективные цены */
            'effective_price' =>
                $this->effectivePrice(),

            'effective_old_price' =>
                $this->effectiveOldPrice(),

            'effective_wholesale_price' =>
                $this->effectiveWholesalePrice(),

            'effective_wholesale_min_quantity' =>
                $this->effectiveWholesaleMinQuantity(),

            /** Остаток варианта */
            'quantity' => (int) $this->quantity,
            'in_stock' => (bool) $this->in_stock,
            'has_stock' => $this->hasStock(),

            /** Основной вариант */
            'is_default' => (bool) $this->is_default,

            /** Сортировка и состояние */
            'sort' => (int) $this->sort,

            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            'status' => $this->status,

            'moderation_status' =>
                (int) $this->moderation_status,

            'is_pending' => $this->isPending(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),

            /** Публикация */
            'is_published' =>
                $this->isPublished(),

            'is_published_now' =>
                $this->isPublishedNow(),

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
             * Отображаемое название.
             *
             * Если собственного перевода нет,
             * используется название товара и code или sku.
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

            /**
             * Все переводы.
             *
             * Возвращаются только при eager loading translations.
             */
            'translations' =>
                MarketProductVariantTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /** Собственная валюта варианта */
            'currency' => $this->whenLoaded(
                'currency',
                fn () => $this->currency
                    ? new CurrencyResource($this->currency)
                    : null
            ),

            /**
             * Значения характеристик варианта.
             *
             * Например:
             * - Цвет → Чёрный;
             * - Размер → XL.
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
                             * Перевод значения характеристики.
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

                                'market_attribute_id' =>
                                    (int) $item->market_attribute_id,

                                'market_attribute_value_id' =>
                                    (int) $item
                                        ->market_attribute_value_id,

                                'sort' => (int) $item->sort,

                                /**
                                 * Готовое текстовое представление:
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

                                        'title' =>
                                            $attributeTranslation?->title,
                                    ]
                                    : null,

                                /** Компактное значение */
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

                                        'code' =>
                                            $item->attributeValue->code,

                                        'color' =>
                                            $item->attributeValue->color,

                                        'icon' =>
                                            $item->attributeValue->icon,

                                        'title' =>
                                            $valueTranslation?->title,
                                    ]
                                    : null,
                            ];
                        })
                        ->values();
                }
            ),

            /**
             * Главное изображение.
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

            /**
             * Галерея варианта.
             *
             * Появляется только при загрузке images.
             */
            'images' =>
                MarketProductVariantImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /** Счётчики */
            'values_count' =>
                $this->whenCounted('values'),

            'images_count' =>
                $this->whenCounted('images'),

            /** Даты */
            'published_at' =>
                $this->published_at?->format('Y-m-d\TH:i'),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
