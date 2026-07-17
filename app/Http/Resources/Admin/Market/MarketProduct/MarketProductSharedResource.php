<?php

namespace App\Http\Resources\Admin\Market\MarketProduct;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductSharedResource extends JsonResource
{
    /**
     * Компактное представление товара.
     *
     * Используется:
     * - в списках товаров;
     * - в рекомендуемых товарах;
     * - в категориях;
     * - в тегах;
     * - в брендах;
     * - в компаниях;
     * - в магазинах.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $currentTranslation = $this->whenLoaded(
            'translations',
            function () use ($currentLocale, $fallbackLocale) {
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
            'id' => (int) $this->id,

            /** Основные внешние ключи */
            'market_company_id' => $this->market_company_id !== null
                ? (int) $this->market_company_id
                : null,

            'market_shop_id' => $this->market_shop_id !== null
                ? (int) $this->market_shop_id
                : null,

            'market_brand_id' => $this->market_brand_id !== null
                ? (int) $this->market_brand_id
                : null,

            'currency_id' => $this->currency_id !== null
                ? (int) $this->currency_id
                : null,

            /** Основные данные товара */
            'url' => $this->url,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            /**
             * Цены
             */
            'price' => $this->price,
            'old_price' => $this->old_price,

            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' => $this->wholesale_min_quantity !== null
                ? (int) $this->wholesale_min_quantity
                : null,

            /** Остатки */
            'quantity' => (int) $this->quantity,
            'in_stock' => (bool) $this->in_stock,
            'has_stock' => $this->hasStock(),

            /** Маркетинговые признаки */
            'is_new' => (bool) $this->is_new,
            'is_hit' => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,

            /** Статус и видимость */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'status' => $this->status,

            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            /** Счётчики и рейтинг */
            'views' => (int) $this->views,
            'likes_count' => (int) $this->likes_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => (int) $this->rating_count,

            /** Вычисляемые признаки */
            'has_old_price' => $this->hasOldPrice(),
            'has_wholesale_price' => $this->hasWholesalePrice(),

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketProductTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы.
             *
             * Они попадут в ответ только при eager loading translations.
             */
            'translations' => MarketProductTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Валюта */
            'currency' => $this->whenLoaded(
                'currency',
                fn () => $this->currency
                    ? new CurrencyResource($this->currency)
                    : null
            ),

            /**
             * Главное изображение.
             *
             * Первое изображение определяется порядком pivot.order.
             */
            'image' => $this->whenLoaded('images', function () {
                $image = $this->images->first();

                return $image
                    ? new MarketProductImageResource($image)
                    : null;
            }),

            /**
             * Галерея.
             *
             * Оставляем её доступной, но она появится только при загрузке images.
             */
            'images' => MarketProductImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Компактная компания */
            'company' => $this->whenLoaded(
                'company',
                function () use ($currentLocale, $fallbackLocale) {
                    if (! $this->company) {
                        return null;
                    }

                    $translation = $this->company->relationLoaded('translations')
                        ? (
                        $this->company->translations->firstWhere(
                            'locale',
                            $currentLocale
                        )
                            ?: $this->company->translations->firstWhere(
                            'locale',
                            $fallbackLocale
                        )
                            ?: $this->company->translations->first()
                        )
                        : null;

                    return [
                        'id' => (int) $this->company->id,
                        'url' => $this->company->url,
                        'title' => $translation?->title,
                        'legal_name' => $this->company->legal_name,
                        'logo' => $this->company->logo,
                    ];
                }
            ),

            /** Компактный магазин */
            'shop' => $this->whenLoaded(
                'shop',
                function () use ($currentLocale, $fallbackLocale) {
                    if (! $this->shop) {
                        return null;
                    }

                    $translation = $this->shop->relationLoaded('translations')
                        ? (
                        $this->shop->translations->firstWhere(
                            'locale',
                            $currentLocale
                        )
                            ?: $this->shop->translations->firstWhere(
                            'locale',
                            $fallbackLocale
                        )
                            ?: $this->shop->translations->first()
                        )
                        : null;

                    return [
                        'id' => (int) $this->shop->id,
                        'url' => $this->shop->url,
                        'title' => $translation?->title,
                        'logo' => $this->shop->logo,
                    ];
                }
            ),

            /** Компактный бренд */
            'brand' => $this->whenLoaded(
                'brand',
                function () use ($currentLocale, $fallbackLocale) {
                    if (! $this->brand) {
                        return null;
                    }

                    $translation = $this->brand->relationLoaded('translations')
                        ? (
                        $this->brand->translations->firstWhere(
                            'locale',
                            $currentLocale
                        )
                            ?: $this->brand->translations->firstWhere(
                            'locale',
                            $fallbackLocale
                        )
                            ?: $this->brand->translations->first()
                        )
                        : null;

                    return [
                        'id' => (int) $this->brand->id,
                        'url' => $this->brand->url,
                        'title' => $translation?->title,
                        'logo' => $this->brand->logo,
                    ];
                }
            ),

            /**
             * Pivot-данные.
             *
             * Появляются, когда товар загружен через belongsToMany:
             * relatedProducts, categories и другие pivot-связи.
             */
            'pivot' => $this->when(
                $this->pivot !== null,
                function () {
                    return [
                        'type' => $this->pivot->type ?? null,

                        'main' => isset($this->pivot->main)
                            ? (bool) $this->pivot->main
                            : null,

                        'order' => isset($this->pivot->order)
                            ? (int) $this->pivot->order
                            : null,

                        'activity' => isset($this->pivot->activity)
                            ? (bool) $this->pivot->activity
                            : null,
                    ];
                }
            ),

            /** Counts */
            'images_count' => $this->whenCounted('images'),
            'categories_count' => $this->whenCounted('categories'),
            'tags_count' => $this->whenCounted('tags'),
            'reviews_count' => $this->whenCounted('reviews'),
            'related_products_count' => $this->whenCounted('relatedProducts'),

            /** Состояние для текущего пользователя */
            'already_liked' => (bool) ($this->already_liked ?? false),

            /** Даты */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
