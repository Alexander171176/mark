<?php

namespace App\Http\Resources\Admin\Market\MarketRecentlyViewedProduct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketRecentlyViewedProductResource extends JsonResource
{
    /**
     * Преобразование записи истории просмотра в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $product = $this->whenLoaded('product');

        /**
         * Если товар был удалён,
         * сама запись истории всё равно
         * может быть корректно представлена.
         */
        if (!$product) {
            return [
                'id' => (int) $this->id,
                'user_id' => (int) $this->user_id,
                'market_product_id' => (int) $this->market_product_id,

                'viewed_at' => $this->viewed_at?->toISOString(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),

                'product' => null,
            ];
        }

        /** Перевод товара с fallback */
        $translation = $product->translations
            ->firstWhere('locale', $locale)
            ?: $product->translations
                ->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                ?: $product->translations->first();

        /** Основное изображение */
        $image = $product->images->first();

        /** Перевод бренда */
        $brandTranslation = $product->brand
            ? (
            $product->brand->translations
                ->firstWhere('locale', $locale)
                ?: $product->brand->translations
                ->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                ?: $product->brand->translations->first()
            )
            : null;

        /** Перевод компании */
        $companyTranslation = $product->company
            ? (
            $product->company->translations
                ->firstWhere('locale', $locale)
                ?: $product->company->translations
                ->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                ?: $product->company->translations->first()
            )
            : null;

        /** Перевод магазина */
        $shopTranslation = $product->shop
            ? (
            $product->shop->translations
                ->firstWhere('locale', $locale)
                ?: $product->shop->translations
                ->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                ?: $product->shop->translations->first()
            )
            : null;

        return [
            /** Запись истории */
            'id' => (int) $this->id,

            'user_id' => (int) $this->user_id,
            'market_product_id' => (int) $this->market_product_id,

            'viewed_at' => $this->viewed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /** Товар */
            'product' => [
                'id' => (int) $product->id,

                'url' => $product->url,
                'sku' => $product->sku,
                'vendor_code' => $product->vendor_code,
                'barcode' => $product->barcode,

                'title' => $translation?->title,
                'subtitle' => $translation?->subtitle,
                'short' => $translation?->short,

                'price' => $product->price,
                'old_price' => $product->old_price,
                'quantity' => (int) ($product->quantity ?? 0),

                'activity' => (bool) $product->activity,
                'status' => $product->status,
                'moderation_status' => (int) $product->moderation_status,

                'views' => (int) ($product->views ?? 0),

                'images_count' => (int) ($product->images_count ?? 0),
                'variants_count' => (int) ($product->variants_count ?? 0),
                'reviews_count' => (int) ($product->reviews_count ?? 0),
                'likes_count' => (int) ($product->likes_count ?? 0),

                /** Основное изображение */
                'image' => $image
                    ? [
                        'id' => (int) $image->id,
                        'url' => $image->url
                            ?? $image->original_url
                                ?? $image->full_url
                                ?? null,
                    ]
                    : null,

                /** Валюта */
                'currency' => $product->currency
                    ? [
                        'id' => (int) $product->currency->id,
                        'code' => $product->currency->code,
                        'symbol' => $product->currency->symbol ?? null,
                    ]
                    : null,

                /** Бренд */
                'brand' => $product->brand
                    ? [
                        'id' => (int) $product->brand->id,
                        'title' => $brandTranslation?->title,
                    ]
                    : null,

                /** Компания */
                'company' => $product->company
                    ? [
                        'id' => (int) $product->company->id,
                        'title' => $companyTranslation?->title,
                    ]
                    : null,

                /** Магазин */
                'shop' => $product->shop
                    ? [
                        'id' => (int) $product->shop->id,
                        'title' => $shopTranslation?->title,
                    ]
                    : null,
            ],
        ];
    }
}
