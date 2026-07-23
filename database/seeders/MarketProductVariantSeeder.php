<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantTranslation;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantValue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketProductVariantSeeder extends Seeder
{
    /**
     * Максимальное количество вариантов
     * для одного товара.
     */
    protected int $variantsPerProduct = 4;

    /**
     * Запуск сидера вариантов товаров.
     */
    public function run(): void
    {
        /**
         * Товары, для которых создаются варианты.
         */
        $products = MarketProduct::query()
            ->with([
                'translations',
                'currency',
            ])
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->command?->warn(
                'MarketProductVariantSeeder: товары не найдены.'
            );

            return;
        }

        /**
         * Характеристики, разрешённые
         * для формирования вариантов.
         *
         * Загружаем только характеристики,
         * у которых есть справочные значения.
         */
        $variantAttributes = MarketAttribute::query()
            ->where('use_for_variants', true)
            ->whereIn('type', [
                'select',
                'multiselect',
            ])
            ->with([
                'translations',
                'values' => function ($query) {
                    $query
                        ->where('activity', true)
                        ->orderBy('sort')
                        ->orderBy('id');
                },
                'values.translations',
            ])
            ->whereHas('values', function ($query) {
                $query->where('activity', true);
            })
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        if ($variantAttributes->isEmpty()) {
            $this->command?->warn(
                'MarketProductVariantSeeder: характеристики для вариантов не найдены. '
                . 'Установите use_for_variants = true и добавьте активные значения.'
            );

            return;
        }

        /**
         * Пользователь, который будет указан
         * как модератор вариантов.
         */
        $moderator = User::query()
            ->orderBy('id')
            ->first();

        /**
         * Поддерживаемые локали приложения.
         */
        $locales = config(
            'app.available_locales',
            ['ru']
        );

        DB::transaction(function () use (
            $products,
            $variantAttributes,
            $moderator,
            $locales
        ): void {
            foreach ($products as $productIndex => $product) {
                /**
                 * Формируем комбинации характеристик.
                 *
                 * Например:
                 *
                 * Цвет: Чёрный + Размер: M
                 * Цвет: Чёрный + Размер: L
                 * Цвет: Белый + Размер: M
                 */
                $combinations = $this->buildCombinations(
                    attributes: $variantAttributes,
                    limit: $this->variantsPerProduct
                );

                if ($combinations->isEmpty()) {
                    continue;
                }

                /**
                 * Перед повторным запуском снимаем флаг
                 * основного варианта со всех вариантов товара.
                 *
                 * Позже первый вариант снова будет назначен
                 * основным.
                 */
                MarketProductVariant::query()
                    ->where(
                        'market_product_id',
                        $product->id
                    )
                    ->update([
                        'is_default' => false,
                    ]);

                foreach (
                    $combinations as $variantIndex => $combination
                ) {
                    $createdAt = Carbon::now()
                        ->subDays(
                            ($productIndex * 5)
                            + $variantIndex
                            + 1
                        )
                        ->setTime(
                            9 + $variantIndex,
                            15
                        );

                    /**
                     * Стабильный код варианта.
                     *
                     * Пример:
                     * black-xl
                     */
                    $variantCode = $this->makeVariantCode(
                        $combination
                    );

                    /**
                     * SKU варианта.
                     *
                     * Пример:
                     * PRODUCT-SKU-BLACK-XL
                     */
                    $variantSku = $this->makeVariantSku(
                        product: $product,
                        combination: $combination,
                        fallbackIndex: $variantIndex + 1
                    );

                    /**
                     * Часть вариантов наследует цену товара,
                     * часть получает собственную цену.
                     *
                     * Это позволяет проверить в интерфейсе
                     * оба сценария.
                     */
                    $hasOwnPrice = $variantIndex % 2 !== 0;

                    $basePrice = (float) $product->price;

                    $variantPrice = $hasOwnPrice
                        ? round(
                            $basePrice
                            + (($variantIndex + 1) * 250),
                            2
                        )
                        : null;

                    $oldPrice = $variantPrice !== null
                        ? round($variantPrice * 1.10, 2)
                        : null;

                    /**
                     * Разные статусы создаются специально,
                     * чтобы проверить фильтрацию, модерацию
                     * и отображение в административной панели.
                     */
                    $moderationStatus = match (
                        $variantIndex % 4
                    ) {
                        2 => 0,
                        3 => 2,
                        default => 1,
                    };

                    $status = match ($moderationStatus) {
                        1 => 'published',
                        2 => 'archived',
                        default => 'draft',
                    };

                    $activity = $moderationStatus !== 2;

                    $quantity = match ($variantIndex % 4) {
                        0 => 25,
                        1 => 12,
                        2 => 3,
                        default => 0,
                    };

                    $inStock = $quantity > 0;

                    $variant = MarketProductVariant::query()
                        ->updateOrCreate(
                            [
                                'market_product_id' =>
                                    $product->id,

                                'code' => $variantCode,
                            ],
                            [
                                /**
                                 * Валюта не задаётся:
                                 * вариант наследует валюту товара.
                                 */
                                'currency_id' => null,

                                'sku' => $variantSku,

                                'vendor_code' =>
                                    $this->makeVendorCode(
                                        product: $product,
                                        combination: $combination
                                    ),

                                'barcode' =>
                                    $this->makeBarcode(
                                        productId: (int) $product->id,
                                        variantIndex: $variantIndex
                                    ),

                                /**
                                 * Цены.
                                 */
                                'price' => $variantPrice,
                                'old_price' => $oldPrice,

                                'purchase_price' =>
                                    $variantPrice !== null
                                        ? round(
                                        $variantPrice * 0.65,
                                        2
                                    )
                                        : null,

                                'wholesale_price' =>
                                    $variantPrice !== null
                                        ? round(
                                        $variantPrice * 0.90,
                                        2
                                    )
                                        : null,

                                'wholesale_min_quantity' =>
                                    $variantPrice !== null
                                        ? 5
                                        : null,

                                /**
                                 * Остаток.
                                 */
                                'quantity' => $quantity,
                                'in_stock' => $inStock,

                                /**
                                 * Физические параметры также
                                 * наследуются от товара.
                                 */
                                'weight' => null,
                                'length' => null,
                                'width' => null,
                                'height' => null,

                                /**
                                 * Первый вариант становится
                                 * основным вариантом товара.
                                 */
                                'is_default' =>
                                    $variantIndex === 0,

                                'sort' => $variantIndex,
                                'activity' => $activity,
                                'status' => $status,

                                /**
                                 * Модерация.
                                 */
                                'moderation_status' =>
                                    $moderationStatus,

                                'moderated_by' =>
                                    $moderationStatus !== 0
                                    && $moderator
                                        ? $moderator->id
                                        : null,

                                'moderated_at' =>
                                    $moderationStatus !== 0
                                        ? $createdAt
                                        ->copy()
                                        ->addHours(2)
                                        : null,

                                'moderation_note' =>
                                    $moderationStatus === 2
                                        ? 'Тестовый вариант отклонён для проверки административной фильтрации.'
                                        : null,

                                /**
                                 * Публикация.
                                 */
                                'published_at' =>
                                    $status === 'published'
                                        ? $createdAt
                                        : null,

                                'show_from_at' =>
                                    $status === 'published'
                                        ? $createdAt
                                        : null,

                                'show_to_at' => null,

                                'created_at' => $createdAt,
                                'updated_at' => $createdAt,
                            ]
                        );

                    /**
                     * Переводы варианта.
                     */
                    $this->seedTranslations(
                        variant: $variant,
                        product: $product,
                        combination: $combination,
                        locales: $locales,
                        createdAt: $createdAt
                    );

                    /**
                     * Значения характеристик,
                     * формирующие вариант.
                     */
                    $this->seedVariantValues(
                        variant: $variant,
                        combination: $combination,
                        createdAt: $createdAt
                    );
                }
            }
        });

        $this->command?->info(
            'Варианты товаров и значения характеристик успешно созданы.'
        );
    }

    /**
     * Сформировать комбинации значений характеристик.
     *
     * Используем не больше двух характеристик,
     * чтобы тестовая база не разрасталась слишком сильно.
     *
     * @return Collection<int, Collection<int, array<string, mixed>>>
     */
    protected function buildCombinations(
        Collection $attributes,
        int $limit
    ): Collection {
        $selectedAttributes = $attributes
            ->filter(
                fn (MarketAttribute $attribute) =>
                $attribute->values->isNotEmpty()
            )
            ->take(2)
            ->values();

        if ($selectedAttributes->isEmpty()) {
            return collect();
        }

        /**
         * Начальная пустая комбинация.
         */
        $combinations = collect([
            collect(),
        ]);

        foreach ($selectedAttributes as $attribute) {
            $nextCombinations = collect();

            /**
             * Берём до трёх значений каждой характеристики.
             */
            $attributeValues = $attribute->values
                ->take(3)
                ->values();

            foreach ($combinations as $combination) {
                foreach ($attributeValues as $attributeValue) {
                    $nextCombinations->push(
                        $combination->concat([
                            [
                                'attribute' => $attribute,
                                'value' => $attributeValue,
                            ],
                        ])
                    );
                }
            }

            $combinations = $nextCombinations;
        }

        return $combinations
            ->take($limit)
            ->values();
    }

    /**
     * Создать или обновить переводы варианта.
     *
     * @param array<int, string> $locales
     */
    protected function seedTranslations(
        MarketProductVariant $variant,
        MarketProduct $product,
        Collection $combination,
        array $locales,
        Carbon $createdAt
    ): void {
        foreach ($locales as $locale) {
            $productTitle = $this->productTitle(
                product: $product,
                locale: $locale
            );

            $combinationTitle = $combination
                ->map(
                    fn (array $item) =>
                    $this->attributeValueTitle(
                        item: $item,
                        locale: $locale
                    )
                )
                ->filter()
                ->implode(', ');

            $title = filled($productTitle)
                ? "{$productTitle} — {$combinationTitle}"
                : $combinationTitle;

            MarketProductVariantTranslation::query()
                ->updateOrCreate(
                    [
                        'market_product_variant_id' =>
                            $variant->id,

                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,

                        'subtitle' =>
                            "Вариант товара: {$combinationTitle}",

                        'short' =>
                            "Комбинация характеристик: {$combinationTitle}.",

                        'description' =>
                            "Тестовый вариант товара «{$productTitle}». "
                            . "Характеристики варианта: {$combinationTitle}.",

                        'meta_title' => $title,

                        'meta_keywords' =>
                            Str::lower(
                                str_replace(
                                    ', ',
                                    ',',
                                    $combinationTitle
                                )
                            ),

                        'meta_desc' =>
                            "Вариант товара {$productTitle}: {$combinationTitle}.",

                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
        }
    }

    /**
     * Создать значения характеристик варианта.
     */
    protected function seedVariantValues(
        MarketProductVariant $variant,
        Collection $combination,
        Carbon $createdAt
    ): void {
        /**
         * Удаляем старые строки, которых больше нет
         * в текущей комбинации.
         */
        $attributeIds = $combination
            ->map(
                fn (array $item) =>
                (int) $item['attribute']->id
            )
            ->values();

        MarketProductVariantValue::query()
            ->where(
                'market_product_variant_id',
                $variant->id
            )
            ->when(
                $attributeIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn(
                    'market_attribute_id',
                    $attributeIds
                )
            )
            ->delete();

        foreach ($combination as $index => $item) {
            MarketProductVariantValue::query()
                ->updateOrCreate(
                    [
                        'market_product_variant_id' =>
                            $variant->id,

                        'market_attribute_id' =>
                            $item['attribute']->id,
                    ],
                    [
                        'market_attribute_value_id' =>
                            $item['value']->id,

                        'sort' => $index,

                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
        }
    }

    /**
     * Получить название товара по локали.
     */
    protected function productTitle(
        MarketProduct $product,
        string $locale
    ): string {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $product->translations
            ->firstWhere('locale', $locale)
            ?: $product->translations
                ->firstWhere(
                    'locale',
                    $fallbackLocale
                )
                ?: $product->translations->first();

        return $translation?->title
            ?: "Товар #{$product->id}";
    }

    /**
     * Получить название значения характеристики
     * для конкретной локали.
     */
    protected function attributeValueTitle(
        array $item,
        string $locale
    ): string {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $value = $item['value'];

        $translation = $value->translations
            ->firstWhere('locale', $locale)
            ?: $value->translations
                ->firstWhere(
                    'locale',
                    $fallbackLocale
                )
                ?: $value->translations->first();

        return $translation?->title
            ?: $value->code
                ?: "Значение #{$value->id}";
    }

    /**
     * Сформировать стабильный код комбинации.
     */
    protected function makeVariantCode(
        Collection $combination
    ): string {
        $code = $combination
            ->map(
                fn (array $item) =>
                $item['value']->code
                    ?: "value-{$item['value']->id}"
            )
            ->filter()
            ->implode('-');

        return Str::slug($code);
    }

    /**
     * Сформировать SKU варианта.
     */
    protected function makeVariantSku(
        MarketProduct $product,
        Collection $combination,
        int $fallbackIndex
    ): string {
        $productSku = filled($product->sku)
            ? $product->sku
            : "PRODUCT-{$product->id}";

        $combinationSku = $combination
            ->map(
                fn (array $item) =>
                $item['value']->code
                    ?: $item['value']->id
            )
            ->filter()
            ->implode('-');

        $sku = "{$productSku}-{$combinationSku}";

        if (! filled($combinationSku)) {
            $sku = "{$productSku}-VARIANT-{$fallbackIndex}";
        }

        return Str::upper(
            Str::limit(
                Str::slug($sku, '-'),
                100,
                ''
            )
        );
    }

    /**
     * Сформировать артикул поставщика.
     */
    protected function makeVendorCode(
        MarketProduct $product,
        Collection $combination
    ): string {
        $base = filled($product->vendor_code)
            ? $product->vendor_code
            : "VENDOR-{$product->id}";

        $suffix = $combination
            ->map(
                fn (array $item) =>
                $item['value']->code
                    ?: $item['value']->id
            )
            ->filter()
            ->implode('-');

        return Str::upper(
            Str::limit(
                Str::slug(
                    "{$base}-{$suffix}",
                    '-'
                ),
                100,
                ''
            )
        );
    }

    /**
     * Сформировать тестовый уникальный штрихкод.
     *
     * Поле в БД не является уникальным,
     * но сидер создаёт стабильное значение.
     */
    protected function makeBarcode(
        int $productId,
        int $variantIndex
    ): string {
        return sprintf(
            '29%08d%02d',
            $productId,
            $variantIndex + 1
        );
    }
}
