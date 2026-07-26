<?php

namespace App\Http\Resources\Admin\Market\MarketProductBundle;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductBundleSharedResource extends JsonResource
{
    /**
     * Компактное представление комплекта товаров.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Текущий перевод с резервной локалью.
         */
        $currentTranslation = null;

        if ($this->relationLoaded('translations')) {
            $currentTranslation = $this->translations
                ->firstWhere('locale', $currentLocale)
                ?: $this->translations
                    ->firstWhere('locale', $fallbackLocale)
                    ?: $this->translations->first();
        }

        /**
         * Загружены ли позиции комплекта для расчётов.
         */
        $itemsLoaded = $this->relationLoaded('items')
            || $this->relationLoaded('activeItems');

        /**
         * Количество всех позиций.
         */
        $itemsCount = isset($this->items_count)
            ? (int) $this->items_count
            : (
            $this->relationLoaded('items')
                ? $this->items->count()
                : null
            );

        /**
         * Количество активных позиций.
         */
        $activeItemsCount = isset($this->active_items_count)
            ? (int) $this->active_items_count
            : (
            $this->relationLoaded('activeItems')
                ? $this->activeItems->count()
                : (
            $this->relationLoaded('items')
                ? $this->items
                ->where('activity', true)
                ->count()
                : null
            )
            );

        /**
         * Расчётная цена состава.
         *
         * Вычисляется только при загруженных позициях,
         * чтобы ресурс не создавал дополнительные SQL-запросы.
         */
        $calculatedPrice = $itemsLoaded
            ? $this->calculatedPrice()
            : null;

        /**
         * Фактическая цена комплекта.
         */
        $effectivePrice = $this->usesManualPrice()
            ? round((float) $this->price, 2)
            : $calculatedPrice;

        /**
         * Расчётное количество доступных комплектов.
         */
        $availableQuantity = $itemsLoaded
            ? $this->availableQuantity()
            : null;

        /**
         * Главное изображение комплекта.
         */
        $mainImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'user_id' => (int) $this->user_id,

            'market_company_id' => $this->market_company_id !== null
                ? (int) $this->market_company_id
                : null,

            'market_shop_id' => $this->market_shop_id !== null
                ? (int) $this->market_shop_id
                : null,

            'currency_id' => $this->currency_id !== null
                ? (int) $this->currency_id
                : null,

            /** Основные данные */
            'url' => $this->url,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            /** Режим формирования цены */
            'calculate_price' => (bool) $this->calculate_price,

            'uses_calculated_price' =>
                $this->usesCalculatedPrice(),

            'uses_manual_price' =>
                $this->usesManualPrice(),

            /** Сохранённые цены */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' =>
                $this->wholesale_min_quantity !== null
                    ? (int) $this->wholesale_min_quantity
                    : null,

            /** Расчётные цены */
            'calculated_price' => $calculatedPrice !== null
                ? number_format($calculatedPrice, 2, '.', '')
                : null,

            'effective_price' => $effectivePrice !== null
                ? number_format($effectivePrice, 2, '.', '')
                : null,

            /** Экономия */
            'has_old_price' => $effectivePrice !== null
                && $this->old_price !== null
                && (float) $this->old_price > $effectivePrice,

            'saving_amount' => $effectivePrice !== null
            && $this->old_price !== null
            && (float) $this->old_price > $effectivePrice
                ? number_format(
                    (float) $this->old_price - $effectivePrice,
                    2,
                    '.',
                    ''
                )
                : '0.00',

            'saving_percent' => $effectivePrice !== null
            && $this->old_price !== null
            && (float) $this->old_price > 0
            && (float) $this->old_price > $effectivePrice
                ? round(
                    (
                        (
                            (float) $this->old_price
                            - $effectivePrice
                        )
                        / (float) $this->old_price
                    ) * 100,
                    2
                )
                : 0.0,

            'has_wholesale_price' =>
                $this->hasWholesalePrice(),

            /** Наличие */
            'available_quantity' => $availableQuantity,

            'has_stock' => $availableQuantity !== null && $availableQuantity > 0,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            /** Рекламные позиции */
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /** Маркетинговые признаки */
            'is_new' => (bool) $this->is_new,
            'is_hit' => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,

            /** Публикация */
            'status' => $this->status,
            'is_published' => $this->isPublished(),
            'is_published_now' => $this->isPublishedNow(),

            /** Модерация */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            /** Статистика */
            'views' => (int) $this->views,
            'likes_count' => (int) $this->likes_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => (int) $this->rating_count,

            /** Сведения о составе */
            'has_items' => $itemsCount !== null && $itemsCount > 0,

            'has_active_items' => $activeItemsCount !== null && $activeItemsCount > 0,

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketProductBundleTranslationResource(
                    $currentTranslation
                )
                : null,

            /**
             * Название для селектов и компактных списков.
             */
            'title' => $currentTranslation?->title,

            'display_title' => $currentTranslation?->title
                ?: $this->sku
                    ?: $this->url
                        ?: "ID: {$this->id}",

            /** Валюта */
            'currency' => new CurrencyResource(
                $this->whenLoaded('currency')
            ),

            /** Компания-поставщик */
            'company' => $this->whenLoaded(
                'company',
                function () use (
                    $currentLocale,
                    $fallbackLocale
                ) {
                    if (! $this->company) {
                        return null;
                    }

                    $translation = null;

                    if (
                        $this->company
                            ->relationLoaded('translations')
                    ) {
                        $translation = $this->company
                            ->translations
                            ->firstWhere(
                                'locale',
                                $currentLocale
                            )
                            ?: $this->company
                                ->translations
                                ->firstWhere(
                                    'locale',
                                    $fallbackLocale
                                )
                                ?: $this->company
                                    ->translations
                                    ->first();
                    }

                    return [
                        'id' => (int) $this->company->id,
                        'url' => $this->company->url,
                        'legal_name' =>
                            $this->company->legal_name,
                        'title' => $translation?->title,
                    ];
                }
            ),

            /** Магазин */
            'shop' => $this->whenLoaded(
                'shop',
                function () use (
                    $currentLocale,
                    $fallbackLocale
                ) {
                    if (! $this->shop) {
                        return null;
                    }

                    $translation = null;

                    if (
                        $this->shop
                            ->relationLoaded('translations')
                    ) {
                        $translation = $this->shop
                            ->translations
                            ->firstWhere(
                                'locale',
                                $currentLocale
                            )
                            ?: $this->shop
                                ->translations
                                ->firstWhere(
                                    'locale',
                                    $fallbackLocale
                                )
                                ?: $this->shop
                                    ->translations
                                    ->first();
                    }

                    return [
                        'id' => (int) $this->shop->id,
                        'url' => $this->shop->url,
                        'title' => $translation?->title,
                    ];
                }
            ),

            /** Главное изображение */
            'image' => $mainImage
                ? new MarketProductBundleImageResource(
                    $mainImage
                )
                : null,

            /** Счётчики */
            'items_count' => $this->whenCounted('items'),

            'active_items_count' => $this->when(
                isset($this->active_items_count),
                fn () => (int) $this->active_items_count
            ),

            'images_count' =>
                $this->whenCounted('images'),
        ];
    }
}
