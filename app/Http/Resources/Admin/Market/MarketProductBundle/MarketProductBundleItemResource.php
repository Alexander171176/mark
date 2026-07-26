<?php

namespace App\Http\Resources\Admin\Market\MarketProductBundle;

use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Http\Resources\Admin\Market\MarketProductVariant\MarketProductVariantSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductBundleItemResource extends JsonResource
{
    /**
     * Преобразование позиции комплекта товаров в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Выбран ли у позиции конкретный вариант товара.
         */
        $hasVariant = $this->market_product_variant_id !== null;

        /**
         * Загружены ли необходимые отношения
         * для безопасного расчёта цены без дополнительных SQL-запросов.
         *
         * Собственная unit_price не требует загрузки товара или варианта.
         */
        $priceCalculationReady = $this->unit_price !== null
            || (
            $hasVariant
                ? $this->relationLoaded('variant')
                : $this->relationLoaded('product')
            );

        /**
         * Для расчёта доступного количества:
         *
         * - при выбранном варианте достаточно загрузить variant;
         * - без выбранного варианта дополнительно нужны варианты товара
         *   и основной вариант, поскольку товар может иметь варианты.
         */
        $availabilityCalculationReady = $hasVariant
            ? $this->relationLoaded('variant')
            : (
                $this->relationLoaded('product')
                && $this->product
                && $this->product->relationLoaded('variants')
                && $this->product->relationLoaded('defaultVariant')
            );

        /**
         * Расчётные цены позиции.
         *
         * null означает, что необходимые отношения
         * не были загружены контроллером.
         */
        $baseUnitPrice = $priceCalculationReady
            ? $this->baseUnitPrice()
            : null;

        $unitDiscountAmount = $priceCalculationReady
            ? $this->unitDiscountAmount()
            : null;

        $effectiveUnitPrice = $priceCalculationReady
            ? $this->effectiveUnitPrice()
            : null;

        $subtotalPrice = $priceCalculationReady
            ? $this->subtotalPrice()
            : null;

        $totalDiscountAmount = $priceCalculationReady
            ? $this->totalDiscountAmount()
            : null;

        $totalPrice = $priceCalculationReady
            ? $this->totalPrice()
            : null;

        /**
         * Расчётные данные наличия.
         */
        $availableQuantity = $availabilityCalculationReady
            ? $this->availableQuantity()
            : null;

        $requiredQuantity = max(
            1,
            (int) $this->quantity
        );

        /**
         * Перевод товара текущей локали.
         */
        $productTranslation = null;

        if (
            $this->relationLoaded('product')
            && $this->product
            && $this->product->relationLoaded('translations')
        ) {
            $productTranslation = $this->product
                ->translations
                ->firstWhere('locale', $currentLocale)
                ?: $this->product
                    ->translations
                    ->firstWhere('locale', $fallbackLocale)
                    ?: $this->product
                        ->translations
                        ->first();
        }

        /**
         * Перевод выбранного варианта текущей локали.
         */
        $variantTranslation = null;

        if (
            $this->relationLoaded('variant')
            && $this->variant
            && $this->variant->relationLoaded('translations')
        ) {
            $variantTranslation = $this->variant
                ->translations
                ->firstWhere('locale', $currentLocale)
                ?: $this->variant
                    ->translations
                    ->firstWhere('locale', $fallbackLocale)
                    ?: $this->variant
                        ->translations
                        ->first();
        }

        /**
         * Название позиции:
         * - название выбранного варианта;
         * - иначе название товара;
         * - иначе артикул или идентификатор.
         */
        $displayTitle = $variantTranslation?->title
            ?: $productTranslation?->title
                ?: (
                $this->relationLoaded('variant')
                && $this->variant
                    ? $this->variant->sku
                    : null
                )
                    ?: (
                    $this->relationLoaded('product')
                    && $this->product
                        ? $this->product->sku
                        : null
                    )
                        ?: "ID: {$this->id}";

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_product_bundle_id' =>
                (int) $this->market_product_bundle_id,

            'market_product_id' =>
                (int) $this->market_product_id,

            'market_product_variant_id' =>
                $this->market_product_variant_id !== null
                    ? (int) $this->market_product_variant_id
                    : null,

            /** Количество позиции в одном комплекте */
            'quantity' => (int) $this->quantity,

            /**
             * Индивидуальная цена позиции.
             *
             * null означает использование актуальной цены
             * товара или выбранного варианта.
             */
            'unit_price' => $this->unit_price,

            'has_custom_unit_price' =>
                $this->hasCustomUnitPrice(),

            /** Настройки скидки */
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,

            'has_discount' => $this->hasDiscount(),
            'has_fixed_discount' => $this->hasFixedDiscount(),
            'has_percent_discount' => $this->hasPercentDiscount(),

            /**
             * Базовая цена одной единицы
             * до применения скидки.
             */
            'base_unit_price' => $baseUnitPrice !== null
                ? number_format($baseUnitPrice, 2, '.', '')
                : null,

            /**
             * Скидка на одну единицу позиции.
             */
            'unit_discount_amount' =>
                $unitDiscountAmount !== null
                    ? number_format(
                    $unitDiscountAmount,
                    2,
                    '.',
                    ''
                )
                    : null,

            /**
             * Цена одной единицы после скидки.
             */
            'effective_unit_price' =>
                $effectiveUnitPrice !== null
                    ? number_format(
                    $effectiveUnitPrice,
                    2,
                    '.',
                    ''
                )
                    : null,

            /**
             * Общая стоимость позиции до скидки.
             */
            'subtotal_price' => $subtotalPrice !== null
                ? number_format($subtotalPrice, 2, '.', '')
                : null,

            /**
             * Общая скидка позиции.
             */
            'total_discount_amount' =>
                $totalDiscountAmount !== null
                    ? number_format(
                    $totalDiscountAmount,
                    2,
                    '.',
                    ''
                )
                    : null,

            /**
             * Итоговая стоимость позиции
             * с учётом количества и скидки.
             */
            'total_price' => $totalPrice !== null
                ? number_format($totalPrice, 2, '.', '')
                : null,

            /** Наличие */
            'available_quantity' => $availableQuantity,

            'required_quantity' => $requiredQuantity,

            'has_stock' => $availableQuantity !== null && $availableQuantity >= $requiredQuantity,

            /**
             * Количество комплектов, которое можно собрать
             * по остатку этой конкретной позиции.
             */
            'available_bundles_quantity' =>
                $availableQuantity !== null
                    ? intdiv(
                    $availableQuantity,
                    $requiredQuantity
                )
                    : null,

            /** Настройки отображения */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            /** Признаки позиции */
            'has_variant' => $this->hasVariant(),

            /**
             * Корректность принадлежности варианта товару.
             *
             * Проверяем только при загруженном варианте,
             * чтобы ресурс не выполнял отдельный SQL-запрос.
             */
            'variant_belongs_to_product' => $hasVariant
                ? (
                $this->relationLoaded('variant')
                    ? (
                    $this->variant !== null
                    && (int) $this->variant->market_product_id
                    === (int) $this->market_product_id
                )
                    : null
                )
                : true,

            /** Название позиции */
            'title' => $displayTitle,
            'display_title' => $displayTitle,

            /** Название родительского товара */
            'product_title' =>
                $productTranslation?->title,

            /** Название выбранного варианта */
            'variant_title' =>
                $variantTranslation?->title,

            /**
             * Компактные данные товара.
             *
             * В контроллере нужно заранее загрузить:
             * product.translations, product.currency,
             * product.images и при необходимости variants.
             */
            'product' => $this->whenLoaded(
                'product',
                fn () => $this->product
                    ? new MarketProductSharedResource(
                        $this->product
                    )
                    : null
            ),

            /**
             * Компактные данные выбранного варианта.
             */
            'variant' => $this->whenLoaded(
                'variant',
                fn () => $this->variant
                    ? new MarketProductVariantSharedResource(
                        $this->variant
                    )
                    : null
            ),

            /**
             * Комплект позиции.
             *
             * Используем компактный ресурс только тогда,
             * когда связь была специально загружена.
             */
            'bundle' => $this->whenLoaded(
                'bundle',
                fn () => $this->bundle
                    ? new MarketProductBundleSharedResource(
                        $this->bundle
                    )
                    : null
            ),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
