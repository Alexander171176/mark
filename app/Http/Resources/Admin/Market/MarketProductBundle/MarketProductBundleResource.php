<?php

namespace App\Http\Resources\Admin\Market\MarketProductBundle;

use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductBundleResource extends JsonResource
{
    /**
     * Преобразование комплекта товаров в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Перевод текущей локали с резервной локалью.
         *
         * Не используем whenLoaded() для промежуточной переменной,
         * чтобы не получить объект MissingValue вместо перевода.
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
         * Возможность безопасно выполнять расчёты состава
         * без дополнительных запросов к базе данных.
         */
        $itemsLoaded = $this->relationLoaded('items')
            || $this->relationLoaded('activeItems');

        /**
         * Количество позиций без дополнительного SQL-запроса.
         */
        $itemsCount = isset($this->items_count)
            ? (int) $this->items_count
            : (
            $this->relationLoaded('items')
                ? $this->items->count()
                : null
            );

        /**
         * Количество активных позиций без дополнительного запроса.
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
         * Расчётные данные состава.
         *
         * Вычисляются только при загруженных позициях,
         * товарах и вариантах.
         */
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
            /** Основной идентификатор */
            'id' => (int) $this->id,

            /** Внешние ключи */
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

            /**
             * Сохранённые денежные значения.
             *
             * Decimal-cast возвращает строки и сохраняет
             * точность денежных данных.
             */
            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'wholesale_price' => $this->wholesale_price,

            'wholesale_min_quantity' =>
                $this->wholesale_min_quantity !== null
                    ? (int) $this->wholesale_min_quantity
                    : null,

            /**
             * Вычисляемая стоимость состава.
             *
             * null означает, что состав не был загружен
             * для выполнения расчёта.
             */
            'calculated_price' => $calculatedPrice !== null
                ? number_format($calculatedPrice, 2, '.', '')
                : null,

            /**
             * Фактическая цена комплекта:
             * - рассчитанная по составу;
             * - либо введённая вручную.
             */
            'effective_price' => $effectivePrice !== null
                ? number_format($effectivePrice, 2, '.', '')
                : null,

            /** Экономия относительно старой цены */
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

            /** Расчётное наличие комплекта */
            'available_quantity' => $availableQuantity,

            'has_stock' => $availableQuantity !== null && $availableQuantity > 0,

            /** Сортировка и активность */
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

            /** Статус публикации */
            'status' => $this->status,

            'is_published' =>
                $this->isPublished(),

            'is_published_now' =>
                $this->isPublishedNow(),

            /** Модерация */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_pending' =>
                (int) $this->moderation_status === 0,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'is_rejected' =>
                (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /** Публикация и окно показа */
            'published_at' =>
                $this->published_at?->format('Y-m-d\TH:i'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

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

            /** Все переводы */
            'translations' =>
                MarketProductBundleTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /** Создатель / владелец комплекта */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    if (! $this->owner) {
                        return null;
                    }

                    return [
                        'id' => (int) $this->owner->id,
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,
                        'profile_photo_url' =>
                            $this->owner->profile_photo_url,
                    ];
                }
            ),

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                function () {
                    if (! $this->moderator) {
                        return null;
                    }

                    return [
                        'id' => (int) $this->moderator->id,
                        'name' => $this->moderator->name,
                        'email' => $this->moderator->email,
                        'profile_photo_url' =>
                            $this->moderator->profile_photo_url,
                    ];
                }
            ),

            /** Валюта комплекта */
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
                        'logo' => $this->company->logo,
                        'activity' =>
                            (bool) $this->company->activity,
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
                        'logo' => $this->shop->logo,
                        'activity' =>
                            (bool) $this->shop->activity,
                    ];
                }
            ),

            /** Все позиции комплекта */
            'items' => MarketProductBundleItemResource::collection(
                $this->whenLoaded('items')
            ),

            /** Только активные позиции комплекта */
            'active_items' =>
                MarketProductBundleItemResource::collection(
                    $this->whenLoaded('activeItems')
                ),

            /** Изображения комплекта */
            'images' =>
                MarketProductBundleImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /** Счётчики связей */
            'items_count' => $this->whenCounted('items'),

            'active_items_count' => $this->when(
                isset($this->active_items_count),
                fn () => (int) $this->active_items_count
            ),

            'images_count' =>
                $this->whenCounted('images'),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
