<?php

namespace App\Http\Resources\Admin\Market\MarketProductBundle;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductBundleResource extends JsonResource
{
    /**
     * Полное представление комплекта товаров.
     *
     * Resource не выполняет дополнительных SQL-запросов:
     * все связанные данные читаются только из заранее загруженных relations/counts.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentTranslation = $this->currentTranslation();

        $itemsLoaded = $this->relationLoaded('items')
            || $this->relationLoaded('activeItems');

        $itemsCount = $this->itemsCountSafe();
        $activeItemsCount = $this->activeItemsCountSafe();

        $calculatedPrice = $itemsLoaded
            ? $this->calculatedPrice()
            : null;

        $effectivePrice = $this->usesManualPrice()
            ? round((float) $this->price, 2)
            : $calculatedPrice;

        $availableQuantity = $itemsLoaded
            ? $this->availableQuantity()
            : null;

        return [
            /** Идентификаторы */
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

            /** Формирование цены */
            'calculate_price' => (bool) $this->calculate_price,
            'uses_calculated_price' => $this->usesCalculatedPrice(),
            'uses_manual_price' => $this->usesManualPrice(),

            /** Сохранённые цены */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'wholesale_price' => $this->wholesale_price,
            'wholesale_min_quantity' => $this->wholesale_min_quantity !== null
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
            'has_old_price' => $this->hasOldPriceSafe($effectivePrice),
            'saving_amount' => $this->savingAmountSafe($effectivePrice),
            'saving_percent' => $this->savingPercentSafe($effectivePrice),
            'has_wholesale_price' => $this->hasWholesalePrice(),

            /** Наличие */
            'available_quantity' => $availableQuantity,
            'has_stock' => $availableQuantity !== null
                && $availableQuantity > 0,

            /** Сортировка и активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_active' => $this->isActive(),

            /** Размещение */
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
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,
            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Окно публикации */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Статистика */
            'views' => (int) $this->views,
            'likes_count' => (int) $this->likes_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => (int) $this->rating_count,

            /** Состав */
            'has_items' => $itemsCount !== null
                && $itemsCount > 0,
            'has_active_items' => $activeItemsCount !== null
                && $activeItemsCount > 0,

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketProductBundleTranslationResource($currentTranslation)
                : null,

            /** Все собственные переводы нужны Edit / TranslationTabs */
            'translations' => MarketProductBundleTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                fn() => $this->compactUser($this->owner)
            ),

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn() => $this->compactUser($this->moderator)
            ),

            /** Валюта */
            'currency' => new CurrencyResource(
                $this->whenLoaded('currency')
            ),

            /** Связанные переводимые сущности */
            'company' => $this->whenLoaded(
                'company',
                fn() => $this->compactCompany($this->company)
            ),
            'shop' => $this->whenLoaded(
                'shop',
                fn() => $this->compactShop($this->shop)
            ),

            /** Состав */
            'items' => MarketProductBundleItemResource::collection(
                $this->whenLoaded('items')
            ),
            'active_items' => MarketProductBundleItemResource::collection(
                $this->whenLoaded('activeItems')
            ),

            /** Изображения */
            'images' => MarketProductBundleImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Счётчики */
            'items_count' => $this->whenCounted('items'),
            'active_items_count' => $this->when(
                isset($this->active_items_count),
                fn() => (int) $this->active_items_count
            ),
            'images_count' => $this->whenCounted('images'),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Текущий перевод только из уже загруженной коллекции translations. */
    private function currentTranslation(): mixed
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->first();
    }

    /** Количество всех позиций без SQL. */
    private function itemsCountSafe(): ?int
    {
        if (isset($this->items_count)) {
            return (int) $this->items_count;
        }

        return $this->relationLoaded('items')
            ? $this->items->count()
            : null;
    }

    /** Количество активных позиций без SQL. */
    private function activeItemsCountSafe(): ?int
    {
        if (isset($this->active_items_count)) {
            return (int) $this->active_items_count;
        }

        if ($this->relationLoaded('activeItems')) {
            return $this->activeItems->count();
        }

        return $this->relationLoaded('items')
            ? $this->items->where('activity', true)->count()
            : null;
    }

    /** Есть ли старая цена относительно фактической цены. */
    private function hasOldPriceSafe(?float $effectivePrice): bool
    {
        return $effectivePrice !== null
            && $this->old_price !== null
            && (float) $this->old_price > $effectivePrice;
    }

    /** Размер экономии без побочных эффектов. */
    private function savingAmountSafe(?float $effectivePrice): string
    {
        if (! $this->hasOldPriceSafe($effectivePrice)) {
            return '0.00';
        }

        return number_format(
            (float) $this->old_price - $effectivePrice,
            2,
            '.',
            ''
        );
    }

    /** Процент экономии без побочных эффектов. */
    private function savingPercentSafe(?float $effectivePrice): float
    {
        if (
            ! $this->hasOldPriceSafe($effectivePrice)
            || (float) $this->old_price <= 0
        ) {
            return 0.0;
        }

        return round(
            (
                (
                    (float) $this->old_price
                    - $effectivePrice
                )
                / (float) $this->old_price
            ) * 100,
            2
        );
    }

    /** Компактный пользователь. */
    private function compactUser(mixed $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
        ];
    }

    /** Компактная компания с вложенным translation. */
    private function compactCompany(mixed $company): ?array
    {
        if (! $company) {
            return null;
        }

        return [
            'id' => (int) $company->id,
            'url' => $company->url,
            'legal_name' => $company->legal_name,
            'logo' => $company->logo,
            'activity' => (bool) $company->activity,
            'translation' => $this->compactTranslation($company),
        ];
    }

    /** Компактный магазин с вложенным translation. */
    private function compactShop(mixed $shop): ?array
    {
        if (! $shop) {
            return null;
        }

        return [
            'id' => (int) $shop->id,
            'url' => $shop->url,
            'logo' => $shop->logo,
            'activity' => (bool) $shop->activity,
            'translation' => $this->compactTranslation($shop),
        ];
    }

    /** Текущий перевод связанной сущности только из loaded translations. */
    private function compactTranslation(mixed $relation): ?array
    {
        if (
            ! $relation
            || ! $relation->relationLoaded('translations')
        ) {
            return null;
        }

        $translation = $relation->translations
            ->firstWhere('locale', app()->getLocale())
            ?: $relation->translations->first();

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
}
