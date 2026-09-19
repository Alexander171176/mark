<?php

namespace App\Http\Resources\Public\Market\MarketProduct;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductSharedResource extends JsonResource
{
    /**
     * Компактное представление товара
     * для Public списков и карточек.
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
             * Поля Public-сортировки.
             *
             * Нужны frontend-режиму для полной
             * симметрии с server-режимом.
             */
            'sort' =>
                $this->sort,

            'published_at' =>
                $this->published_at?->toISOString(),

            'created_at' =>
                $this->created_at?->toISOString(),

            /**
             * Цена.
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
             * Публичные флаги товара.
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

            /**
             * Поставил ли текущий пользователь
             * лайк этому товару.
             *
             * Значение подготавливается Public Controller
             * через withExists().
             */
            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            'rating_avg' =>
                $this->rating_avg,

            'rating_count' =>
                $this->rating_count,

            /**
             * Разрешённый перевод товара:
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
             * Изображения.
             *
             * Пока используем общий image resource,
             * как и для остальных Market-сущностей.
             */
            'images' =>
                MarketProductImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /**
             * Counts подготавливает Public Controller.
             */
            'variants_count' =>
                $this->whenCounted(
                    'publicVariants'
                ),

            'reviews_count' =>
                $this->whenCounted(
                    'reviews'
                ),
        ];
    }
}
