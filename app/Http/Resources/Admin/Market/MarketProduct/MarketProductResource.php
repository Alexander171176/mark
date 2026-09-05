<?php

namespace App\Http\Resources\Admin\Market\MarketProduct;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketProductVariant\MarketProductVariantSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductResource extends JsonResource
{
    /**
     * Полное представление товара.
     *
     * Используется прежде всего на Edit/details.
     *
     * Важно:
     * - собственные translations могут быть загружены полностью;
     * - связанные мультиязычные сущности читают только уже загруженные translations;
     * - Resource не выполняет SQL самостоятельно;
     * - все тяжёлые связи появляются только через whenLoaded/whenCounted.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,

            /** Внешние ключи */
            'user_id' => $this->user_id !== null ? (int) $this->user_id : null,
            'market_company_id' => $this->market_company_id !== null ? (int) $this->market_company_id : null,
            'market_shop_id' => $this->market_shop_id !== null ? (int) $this->market_shop_id : null,
            'market_brand_id' => $this->market_brand_id !== null ? (int) $this->market_brand_id : null,
            'currency_id' => $this->currency_id !== null ? (int) $this->currency_id : null,

            /** Основные данные */
            'url' => $this->url,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            /** Цены */
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
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Публикация и окно показа */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Статистика */
            'views' => (int) $this->views,
            'likes_count' => (int) $this->likes_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => (int) $this->rating_count,

            /** Вычисляемые признаки */
            'has_stock' => $this->hasStock(),
            'has_old_price' => $this->hasOldPrice(),
            'has_wholesale_price' => $this->hasWholesalePrice(),

            'has_variants' => isset($this->variants_count)
                ? (int) $this->variants_count > 0
                : (
                $this->relationLoaded('variants')
                    ? $this->variants->isNotEmpty()
                    : false
                ),

            'has_available_variants' => isset($this->available_variants_count)
                ? (int) $this->available_variants_count > 0
                : (
                $this->relationLoaded('variants')
                    ? $this->variants->contains(
                    fn ($variant) => $variant->isActive() && $variant->hasStock()
                )
                    : false
                ),

            'is_active' => $this->isActive(),
            'is_published' => $this->isPublished(),
            'is_published_now' => $this->isPublishedNow(),

            /** Текущий перевод из уже загруженных translations */
            'translation' => $this->currentTranslation()
                ? new MarketProductTranslationResource($this->currentTranslation())
                : null,

            /**
             * Все собственные переводы.
             *
             * На Edit Controller загружает translations без locale-фильтра.
             */
            'translations' => MarketProductTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Владелец */
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

            /** Валюта */
            'currency' => $this->whenLoaded('currency', function () {
                return $this->currency
                    ? new CurrencyResource($this->currency)
                    : null;
            }),

            /** Компания */
            'company' => $this->whenLoaded('company', function () {
                if (! $this->company) {
                    return null;
                }

                return [
                    'id' => (int) $this->company->id,
                    'url' => $this->company->url,
                    'legal_name' => $this->company->legal_name,
                    'logo' => $this->company->logo,
                    'activity' => (bool) $this->company->activity,
                    'translation' => $this->relatedTranslation($this->company),
                ];
            }),

            /** Магазин */
            'shop' => $this->whenLoaded('shop', function () {
                if (! $this->shop) {
                    return null;
                }

                return [
                    'id' => (int) $this->shop->id,
                    'market_company_id' => $this->shop->market_company_id !== null
                        ? (int) $this->shop->market_company_id
                        : null,
                    'url' => $this->shop->url,
                    'logo' => $this->shop->logo,
                    'activity' => (bool) $this->shop->activity,
                    'translation' => $this->relatedTranslation($this->shop),
                ];
            }),

            /** Бренд */
            'brand' => $this->whenLoaded('brand', function () {
                if (! $this->brand) {
                    return null;
                }

                return [
                    'id' => (int) $this->brand->id,
                    'url' => $this->brand->url,
                    'website' => $this->brand->website,
                    'logo' => $this->brand->logo,
                    'activity' => (bool) $this->brand->activity,
                    'translation' => $this->relatedTranslation($this->brand),
                ];
            }),

            /** Изображения */
            'images' => MarketProductImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Категории */
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories
                    ->map(fn ($category) => $this->compactCategory($category))
                    ->values();
            }),

            /** Основные категории */
            'main_categories' => $this->whenLoaded('mainCategories', function () {
                return $this->mainCategories
                    ->map(fn ($category) => $this->compactCategory($category))
                    ->values();
            }),

            /** Теги */
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags
                    ->map(fn ($tag) => $this->compactTag($tag))
                    ->values();
            }),

            /** Характеристики */
            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return $this->attributeValues
                    ->map(fn ($item) => $this->compactAttributeValue($item))
                    ->values();
            }),

            /** Все варианты товара */
            'variants' => MarketProductVariantSharedResource::collection(
                $this->whenLoaded('variants')
            ),

            /** Основной вариант */
            'default_variant' => $this->whenLoaded('defaultVariant', function () {
                return $this->defaultVariant
                    ? new MarketProductVariantSharedResource($this->defaultVariant)
                    : null;
            }),

            /** Публичные варианты */
            'public_variants' => MarketProductVariantSharedResource::collection(
                $this->whenLoaded('publicVariants')
            ),

            /** Связанные товары */
            'related_products' => MarketProductSharedResource::collection(
                $this->whenLoaded('relatedProducts')
            ),

            /** Счётчики */
            'images_count' => $this->whenCounted('images'),
            'categories_count' => $this->whenCounted('categories'),
            'tags_count' => $this->whenCounted('tags'),
            'attribute_values_count' => $this->whenCounted('attributeValues'),
            'variants_count' => $this->whenCounted('variants'),

            'available_variants_count' => $this->when(
                isset($this->available_variants_count),
                fn () => (int) $this->available_variants_count
            ),

            'reviews_count' => $this->whenCounted('reviews'),

            /**
             * Не смешиваем scalar market_products.likes_count
             * с relation count.
             */
            'likes_relation_count' => $this->whenCounted('likes'),

            'related_products_count' => $this->whenCounted('relatedProducts'),

            /** Состояние текущего пользователя */
            'already_liked' => (bool) ($this->already_liked ?? false),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод самого товара.
     *
     * Full Resource обычно получает все собственные translations,
     * поэтому здесь разрешён fallback.
     */
    private function currentTranslation()
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        return $this->translations->firstWhere('locale', $currentLocale)
            ?: $this->translations->firstWhere('locale', $fallbackLocale)
                ?: $this->translations->first();
    }

    /**
     * Компактный nested-перевод связанной сущности.
     *
     * Никаких запросов к БД — только уже загруженная relation translations.
     */
    private function relatedTranslation($model): ?array
    {
        if (! $model || ! $model->relationLoaded('translations')) {
            return null;
        }

        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $translation = $model->translations->firstWhere('locale', $currentLocale)
            ?: $model->translations->firstWhere('locale', $fallbackLocale)
                ?: $model->translations->first();

        if (! $translation) {
            return null;
        }

        return [
            'locale' => $translation->locale,
            'title' => $translation->title,
            'subtitle' => $translation->subtitle,
            'short' => $translation->short,
        ];
    }

    /** Компактная категория */
    private function compactCategory($category): array
    {
        return [
            'id' => (int) $category->id,
            'parent_id' => $category->parent_id !== null ? (int) $category->parent_id : null,
            'url' => $category->url,
            'icon' => $category->icon,
            'activity' => (bool) $category->activity,
            'translation' => $this->relatedTranslation($category),
            'pivot' => $this->compactPivot($category),
        ];
    }

    /** Компактный тег */
    private function compactTag($tag): array
    {
        return [
            'id' => (int) $tag->id,
            'url' => $tag->url,
            'icon' => $tag->icon,
            'color' => $tag->color,
            'activity' => (bool) $tag->activity,
            'translation' => $this->relatedTranslation($tag),
            'pivot' => $this->compactPivot($tag),
        ];
    }

    /** Компактная характеристика товара */
    private function compactAttributeValue($item): array
    {
        return [
            'id' => (int) $item->id,

            'market_attribute_id' => (int) $item->market_attribute_id,

            'market_attribute_value_id' => $item->market_attribute_value_id !== null
                ? (int) $item->market_attribute_value_id
                : null,

            'value_string' => $item->value_string,
            'value_number' => $item->value_number,
            'value_boolean' => $item->value_boolean,
            'value_date' => $item->value_date?->format('Y-m-d'),
            'value_json' => $item->value_json,

            'unit' => $item->unit,
            'order' => (int) $item->order,
            'activity' => (bool) $item->activity,

            'attribute' => $item->relationLoaded('attribute') && $item->attribute
                ? [
                    'id' => (int) $item->attribute->id,
                    'type' => $item->attribute->type,
                    'code' => $item->attribute->code,
                    'unit' => $item->attribute->unit,
                    'translation' => $this->relatedTranslation($item->attribute),
                ]
                : null,

            'attribute_value' => $item->relationLoaded('attributeValue') && $item->attributeValue
                ? [
                    'id' => (int) $item->attributeValue->id,
                    'market_attribute_id' => (int) $item->attributeValue->market_attribute_id,
                    'translation' => $this->relatedTranslation($item->attributeValue),
                ]
                : null,
        ];
    }

    /** Pivot belongsToMany */
    private function compactPivot($model): ?array
    {
        if (! $model->pivot) {
            return null;
        }

        return [
            'type' => $model->pivot->type ?? null,
            'main' => isset($model->pivot->main) ? (bool) $model->pivot->main : null,
            'order' => isset($model->pivot->order) ? (int) $model->pivot->order : null,
            'activity' => isset($model->pivot->activity) ? (bool) $model->pivot->activity : null,
        ];
    }
}
