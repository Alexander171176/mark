<?php

namespace App\Http\Resources\Admin\Market\MarketProductVariant;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductVariantSharedResource extends JsonResource
{
    /**
     * Компактное представление варианта товара.
     *
     * Используется в Index и как вложенный ресурс. В отличие от полного
     * Resource не возвращает translations[]: только translation текущей
     * локали из уже загруженной коллекции.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $translation = $this->currentTranslation(
            $this->resource,
            $currentLocale,
            $fallbackLocale
        );

        return [
            'id' => (int) $this->id,
            'market_product_id' => (int) $this->market_product_id,
            'currency_id' => $this->currency_id !== null ? (int) $this->currency_id : null,

            'code' => $this->code,
            'sku' => $this->sku,
            'vendor_code' => $this->vendor_code,
            'barcode' => $this->barcode,

            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'wholesale_price' => $this->wholesale_price,
            'wholesale_min_quantity' => $this->wholesale_min_quantity !== null
                ? (int) $this->wholesale_min_quantity
                : null,

            'effective_price' => $this->effectiveValue('price'),
            'effective_old_price' => $this->effectiveValue('old_price'),
            'effective_purchase_price' => $this->effectiveValue('purchase_price'),
            'effective_wholesale_price' => $this->effectiveValue('wholesale_price'),
            'effective_wholesale_min_quantity' => $this->effectiveValue('wholesale_min_quantity'),

            'quantity' => (int) $this->quantity,
            'in_stock' => (bool) $this->in_stock,
            'has_stock' => $this->hasStock(),

            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,

            'effective_weight' => $this->effectiveValue('weight'),
            'effective_length' => $this->effectiveValue('length'),
            'effective_width' => $this->effectiveValue('width'),
            'effective_height' => $this->effectiveValue('height'),

            'is_default' => (bool) $this->is_default,
            'sort' => (int) $this->sort,

            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            'status' => $this->status,
            'is_published' => $this->isPublished(),
            'is_published_now' => $this->isPublishedNow(),

            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => $this->isPending(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            'has_own_price' => $this->hasOwnPrice(),
            'has_own_currency' => $this->hasOwnCurrency(),
            'has_own_dimensions' => $this->hasOwnDimensions(),

            'effective_currency_id' => $this->effectiveCurrencyIdSafe(),

            'display_title' => $this->displayTitleSafe(
                $translation,
                $currentLocale,
                $fallbackLocale
            ),

            /** Только текущий перевод. translations[] в SharedResource отсутствует. */
            'translation' => $translation
                ? new MarketProductVariantTranslationResource($translation)
                : null,

            'product' => $this->whenLoaded(
                'product',
                fn () => $this->product
                    ? new MarketProductSharedResource($this->product)
                    : null
            ),

            'currency' => $this->whenLoaded(
                'currency',
                fn () => $this->currency
                    ? new CurrencyResource($this->currency)
                    : null
            ),

            'effective_currency' => $this->effectiveCurrencyResource(),

            'moderator' => $this->compactModerator(),

            'values' => $this->whenLoaded(
                'values',
                fn () => $this->values
                    ->map(fn ($item) => $this->compactValue(
                        $item,
                        $currentLocale,
                        $fallbackLocale
                    ))
                    ->values()
            ),

            'image' => $this->whenLoaded(
                'images',
                function () {
                    $image = $this->images->first();

                    return $image
                        ? new MarketProductVariantImageResource($image)
                        : null;
                }
            ),

            'images' => MarketProductVariantImageResource::collection(
                $this->whenLoaded('images')
            ),

            'values_count' => $this->whenCounted('values'),
            'images_count' => $this->whenCounted('images'),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Перевод загруженной переводимой модели для текущей локали.
     */
    private function currentTranslation($model, string $locale, string $fallbackLocale)
    {
        if (! $model || ! $model->relationLoaded('translations')) {
            return null;
        }

        return $model->translations->firstWhere('locale', $locale)
            ?: $model->translations->firstWhere('locale', $fallbackLocale)
                ?: $model->translations->first();
    }

    /**
     * Эффективное наследуемое значение без lazy loading.
     */
    private function effectiveValue(string $field)
    {
        if ($this->{$field} !== null) {
            return $this->{$field};
        }

        if (! $this->relationLoaded('product') || ! $this->product) {
            return null;
        }

        return $this->product->{$field};
    }

    /**
     * ID эффективной валюты без lazy loading.
     */
    private function effectiveCurrencyIdSafe(): ?int
    {
        if ($this->currency_id !== null) {
            return (int) $this->currency_id;
        }

        if (! $this->relationLoaded('product') || ! $this->product) {
            return null;
        }

        return $this->product->currency_id !== null
            ? (int) $this->product->currency_id
            : null;
    }

    /**
     * Эффективная валюта только из уже загруженных relations.
     */
    private function effectiveCurrencyResource()
    {
        if ($this->currency_id !== null) {
            if (! $this->relationLoaded('currency') || ! $this->currency) {
                return null;
            }

            return new CurrencyResource($this->currency);
        }

        if (
            ! $this->relationLoaded('product')
            || ! $this->product
            || ! $this->product->relationLoaded('currency')
            || ! $this->product->currency
        ) {
            return null;
        }

        return new CurrencyResource($this->product->currency);
    }

    /**
     * Отображаемое название без lazy loading.
     */
    private function displayTitleSafe($translation, string $locale, string $fallbackLocale): string
    {
        if (filled($translation?->title)) {
            return $translation->title;
        }

        $productTitle = null;

        if ($this->relationLoaded('product') && $this->product) {
            $productTranslation = $this->currentTranslation(
                $this->product,
                $locale,
                $fallbackLocale
            );

            $productTitle = $productTranslation?->title;
        }

        $suffix = $this->code ?: $this->sku;

        if (filled($productTitle) && filled($suffix)) {
            return "{$productTitle} — {$suffix}";
        }

        return $productTitle
            ?: $suffix
                ?: "ID: {$this->id}";
    }

    /**
     * Компактное значение характеристики варианта.
     */
    private function compactValue($item, string $locale, string $fallbackLocale): array
    {
        $attribute = $item->relationLoaded('attribute')
            ? $item->attribute
            : null;

        $attributeValue = $item->relationLoaded('attributeValue')
            ? $item->attributeValue
            : null;

        $attributeTranslation = $this->currentTranslation(
            $attribute,
            $locale,
            $fallbackLocale
        );

        $valueTranslation = $this->currentTranslation(
            $attributeValue,
            $locale,
            $fallbackLocale
        );

        $displayValue = filled($attributeTranslation?->title)
        && filled($valueTranslation?->title)
            ? "{$attributeTranslation->title}: {$valueTranslation->title}"
            : ($valueTranslation?->title ?: $attributeTranslation?->title);

        return [
            'id' => (int) $item->id,
            'market_product_variant_id' => (int) $item->market_product_variant_id,
            'market_attribute_id' => (int) $item->market_attribute_id,
            'market_attribute_value_id' => (int) $item->market_attribute_value_id,
            'sort' => (int) $item->sort,
            'display_value' => $displayValue,

            'attribute' => $attribute ? [
                'id' => (int) $attribute->id,
                'code' => $attribute->code,
                'type' => $attribute->type,
                'unit' => $attribute->unit,
                'color' => $attribute->color,
                'icon' => $attribute->icon,
                'use_for_variants' => (bool) $attribute->use_for_variants,
                'translation' => $attributeTranslation ? [
                    'locale' => $attributeTranslation->locale,
                    'title' => $attributeTranslation->title,
                    'subtitle' => $attributeTranslation->subtitle,
                    'short' => $attributeTranslation->short,
                ] : null,
            ] : null,

            'attribute_value' => $attributeValue ? [
                'id' => (int) $attributeValue->id,
                'market_attribute_id' => (int) $attributeValue->market_attribute_id,
                'code' => $attributeValue->code,
                'color' => $attributeValue->color,
                'icon' => $attributeValue->icon,
                'translation' => $valueTranslation ? [
                    'locale' => $valueTranslation->locale,
                    'title' => $valueTranslation->title,
                    'subtitle' => $valueTranslation->subtitle,
                    'short' => $valueTranslation->short,
                ] : null,
            ] : null,
        ];
    }

    /**
     * Компактный модератор.
     */
    private function compactModerator(): ?array
    {
        if (! $this->relationLoaded('moderator') || ! $this->moderator) {
            return null;
        }

        return [
            'id' => (int) $this->moderator->id,
            'name' => $this->moderator->name,
            'email' => $this->moderator->email,
            'profile_photo_url' => $this->moderator->profile_photo_url,
        ];
    }

}
