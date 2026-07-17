<?php

namespace App\Http\Resources\Admin\Market\MarketProduct;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategoryResource;
use App\Http\Resources\Admin\Market\MarketTag\MarketTagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductResource extends JsonResource
{
    /**
     * Преобразование товара в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Определяем перевод текущей локали.
         * Коллекция translations должна быть загружена в контроллере.
         */
        $currentTranslation = $this->whenLoaded(
            'translations',
            function () use ($currentLocale, $fallbackLocale) {
                return $this->translations->firstWhere('locale', $currentLocale)
                    ?: $this->translations->firstWhere('locale', $fallbackLocale)
                        ?: $this->translations->first();
            }
        );

        return [
            'id' => (int) $this->id,

            /** Внешние ключи */
            'user_id' => (int) $this->user_id,

            'market_company_id' => $this->market_company_id
                ? (int) $this->market_company_id
                : null,

            'market_shop_id' => $this->market_shop_id
                ? (int) $this->market_shop_id
                : null,

            'market_brand_id' => $this->market_brand_id
                ? (int) $this->market_brand_id
                : null,

            'currency_id' => $this->currency_id
                ? (int) $this->currency_id
                : null,

            /** Основные данные */
            'url' => $this->url,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            /**
             * Цены
             *
             * decimal-cast в модели возвращает строку.
             * Это сохраняет точность денежных значений.
             */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' => $this->wholesale_min_quantity !== null
                ? (int) $this->wholesale_min_quantity
                : null,

            /** Остатки */
            'quantity' => (int) $this->quantity,
            'in_stock' => (bool) $this->in_stock,

            /** Физические параметры */
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /** Маркетинговые признаки */
            'is_new' => (bool) $this->is_new,
            'is_hit' => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,

            /** Статус публикации */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by
                ? (int) $this->moderated_by
                : null,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Публикация и окно показа */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики и рейтинг */
            'views' => (int) $this->views,
            'likes_count' => (int) $this->likes_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => (int) $this->rating_count,

            /** Вычисляемые признаки */
            'has_stock' => $this->hasStock(),
            'has_old_price' => $this->hasOldPrice(),
            'has_wholesale_price' => $this->hasWholesalePrice(),

            'is_active' => $this->isActive(),
            'is_published' => $this->isPublished(),
            'is_published_now' => $this->isPublishedNow(),

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketProductTranslationResource($currentTranslation)
                : null,

            /** Все переводы */
            'translations' => MarketProductTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Владелец товара */
            'owner' => $this->whenLoaded('owner', function () {
                if (! $this->owner) {
                    return null;
                }

                return [
                    'id' => (int) $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                    'profile_photo_url' => $this->owner->profile_photo_url,
                ];
            }),

            /** Модератор */
            'moderator' => $this->whenLoaded('moderator', function () {
                if (! $this->moderator) {
                    return null;
                }

                return [
                    'id' => (int) $this->moderator->id,
                    'name' => $this->moderator->name,
                    'email' => $this->moderator->email,
                ];
            }),

            /** Валюта товара */
            'currency' => new CurrencyResource(
                $this->whenLoaded('currency')
            ),

            /** Компания-поставщик */
            'company' => $this->whenLoaded('company', function () use (
                $currentLocale,
                $fallbackLocale
            ) {
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
                    'legal_name' => $this->company->legal_name,
                    'title' => $translation?->title,
                    'logo' => $this->company->logo,
                    'activity' => (bool) $this->company->activity,
                ];
            }),

            /** Магазин */
            'shop' => $this->whenLoaded('shop', function () use (
                $currentLocale,
                $fallbackLocale
            ) {
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
                    'activity' => (bool) $this->shop->activity,
                ];
            }),

            /** Бренд */
            'brand' => $this->whenLoaded('brand', function () use (
                $currentLocale,
                $fallbackLocale
            ) {
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
                    'website' => $this->brand->website,
                    'activity' => (bool) $this->brand->activity,
                ];
            }),

            /** Изображения */
            'images' => MarketProductImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Категории */
            'categories' => MarketCategoryResource::collection(
                $this->whenLoaded('categories')
            ),

            /** Основные категории */
            'main_categories' => MarketCategoryResource::collection(
                $this->whenLoaded('mainCategories')
            ),

            /** Теги */
            'tags' => MarketTagResource::collection(
                $this->whenLoaded('tags')
            ),

            /** Характеристики товара. */
            'attribute_values' => $this->whenLoaded(
                'attributeValues',
                function () use ($currentLocale, $fallbackLocale) {
                    return $this->attributeValues
                        ->map(function ($item) use (
                            $currentLocale,
                            $fallbackLocale
                        ) {
                            /**
                             * Перевод характеристики:
                             * например «Цвет», «Мощность», «Материал».
                             */
                            $attributeTranslation = null;

                            if (
                                $item->relationLoaded('attribute')
                                && $item->attribute
                                && $item->attribute->relationLoaded('translations')
                            ) {
                                $attributeTranslation =
                                    $item->attribute->translations
                                        ->firstWhere('locale', $currentLocale)
                                        ?: $item->attribute->translations
                                        ->firstWhere('locale', $fallbackLocale)
                                        ?: $item->attribute->translations->first();
                            }

                            /**
                             * Перевод справочного значения:
                             * например «Красный», «Синий», «Металл».
                             */
                            $valueTranslation = null;

                            if (
                                $item->relationLoaded('attributeValue')
                                && $item->attributeValue
                                && $item->attributeValue
                                    ->relationLoaded('translations')
                            ) {
                                $valueTranslation =
                                    $item->attributeValue->translations
                                        ->firstWhere('locale', $currentLocale)
                                        ?: $item->attributeValue->translations
                                        ->firstWhere('locale', $fallbackLocale)
                                        ?: $item->attributeValue
                                            ->translations
                                            ->first();
                            }

                            return [
                                'id' => (int) $item->id,

                                'market_attribute_id' =>
                                    (int) $item->market_attribute_id,

                                'market_attribute_value_id' =>
                                    $item->market_attribute_value_id !== null
                                        ? (int) $item->market_attribute_value_id
                                        : null,

                                /**
                                 * Фактические значения характеристики.
                                 */
                                'value_string' => $item->value_string,
                                'value_number' => $item->value_number,
                                'value_boolean' => $item->value_boolean,
                                'value_date' => $item->value_date?->format('Y-m-d'),
                                'value_json' => $item->value_json,

                                'unit' => $item->unit,
                                'order' => (int) $item->order,
                                'activity' => (bool) $item->activity,

                                /**
                                 * Компактная характеристика.
                                 */
                                'attribute' => $item->relationLoaded('attribute')
                                && $item->attribute
                                    ? [
                                        'id' => (int) $item->attribute->id,
                                        'title' => $attributeTranslation?->title,
                                        'subtitle' =>
                                            $attributeTranslation?->subtitle,
                                        'short' => $attributeTranslation?->short,
                                    ]
                                    : null,

                                /**
                                 * Компактное справочное значение.
                                 */
                                'attribute_value' =>
                                    $item->relationLoaded('attributeValue')
                                    && $item->attributeValue
                                        ? [
                                        'id' =>
                                            (int) $item->attributeValue->id,

                                        'market_attribute_id' =>
                                            (int) $item->attributeValue
                                                ->market_attribute_id,

                                        'title' => $valueTranslation?->title,
                                        'subtitle' =>
                                            $valueTranslation?->subtitle,
                                        'short' => $valueTranslation?->short,
                                    ]
                                        : null,
                            ];
                        })
                        ->values();
                }
            ),

            /** Связанные товары */
            'related_products' => MarketProductSharedResource::collection(
                $this->whenLoaded('relatedProducts')
            ),

            /**
             * Counts, если они загружены через withCount().
             */
            'images_count' => $this->whenCounted('images'),
            'categories_count' => $this->whenCounted('categories'),
            'tags_count' => $this->whenCounted('tags'),
            'attribute_values_count' => $this->whenCounted('attributeValues'),
            'reviews_count' => $this->whenCounted('reviews'),
            'likes_relation_count' => $this->whenCounted('likes'),
            'related_products_count' => $this->whenCounted('relatedProducts'),

            /**
             * Данные текущего пользователя.
             *
             * Поле уже можно передавать из контроллера через setAttribute()
             * либо через добавление вычисленного значения к Resource.
             */
            'already_liked' => (bool) ($this->already_liked ?? false),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
