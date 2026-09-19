<?php

namespace App\Http\Resources\Public\Market\MarketProduct;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductImageResource;
use App\Http\Resources\Public\Market\MarketCategory\MarketCategorySharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductResource extends JsonResource
{
    /**
     * Полное представление товара
     * для Public Show.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            : null;

        $brand = $this->relationLoaded('brand')
            ? $this->brand
            : null;

        $brandTranslation = $brand
        && $brand->relationLoaded('translations')
            ? $brand->translations
                ->firstWhere('locale', $locale)
                ?: $brand->translations
                    ->firstWhere(
                        'locale',
                        $fallbackLocale
                    )
            : null;

        return [
            'id' =>
                $this->id,

            'url' =>
                $this->url,

            'sku' =>
                $this->sku,

            'vendor_code' =>
                $this->vendor_code,

            'barcode' =>
                $this->barcode,

            /**
             * Цена.
             *
             * purchase_price намеренно
             * отсутствует в Public.
             */
            'price' =>
                $this->price,

            'old_price' =>
                $this->old_price,

            /**
             * Наличие.
             */
            'quantity' =>
                $this->quantity,

            'in_stock' =>
                (bool) $this->in_stock,

            /**
             * Физические параметры.
             */
            'weight' =>
                $this->weight,

            'length' =>
                $this->length,

            'width' =>
                $this->width,

            'height' =>
                $this->height,

            /**
             * Публичные флаги.
             */
            'is_new' =>
                (bool) $this->is_new,

            'is_hit' =>
                (bool) $this->is_hit,

            'is_sale' =>
                (bool) $this->is_sale,

            /**
             * Публичная статистика.
             */
            'views' =>
                $this->views,

            'likes_count' =>
                $this->likes_count,

            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            'rating_avg' =>
                $this->rating_avg,

            'rating_count' =>
                $this->rating_count,

            /**
             * Полный разрешённый перевод:
             * current → fallback → null.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_description' =>
                        $translation->meta_description,
                ]
                : null,

            /**
             * Публичный бренд.
             */
            'brand' => $brand
                ? [
                    'id' =>
                        $brand->id,

                    'url' =>
                        $brand->url,

                    'translation' =>
                        $brandTranslation
                            ? [
                            'locale' =>
                                $brandTranslation->locale,

                            'title' =>
                                $brandTranslation->title,
                        ]
                            : null,
                ]
                : null,

            /**
             * Валюта.
             */
            'currency' =>
                CurrencyResource::make(
                    $this->whenLoaded('currency')
                ),

            /**
             * Изображения товара.
             */
            'images' =>
                MarketProductImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /**
             * Публичные категории товара.
             */
            'categories' =>
                MarketCategorySharedResource::collection(
                    $this->whenLoaded('categories')
                ),

            /**
             * Counts.
             */
            'variants_count' =>
                $this->whenCounted(
                    'publicVariants'
                ),

            'reviews_count' =>
                $this->whenCounted(
                    'reviews'
                ),

            'images_count' =>
                $this->whenCounted(
                    'images'
                ),

            'categories_count' =>
                $this->whenCounted(
                    'categories'
                ),
        ];
    }
}
