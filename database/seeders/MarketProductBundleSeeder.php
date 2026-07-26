<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarketProductBundleSeeder extends Seeder
{
    /**
     * Заполнение комплектов товаров маркетплейса.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::query()->find(1)
                ?? User::query()->first();

            if (! $user) {
                $this->command?->warn(
                    'MarketProductBundleSeeder: пользователь не найден. Сидер остановлен.'
                );

                return;
            }

            /**
             * Загружаем товары вместе с вариантами,
             * чтобы использовать основной вариант в составе комплекта.
             */
            $products = MarketProduct::query()
                ->where('user_id', $user->id)
                ->with([
                    'variants' => function ($query): void {
                        $query
                            ->orderByDesc('is_default')
                            ->orderBy('sort')
                            ->orderBy('id');
                    },
                ])
                ->get()
                ->keyBy('url');

            if ($products->count() < 2) {
                $this->command?->warn(
                    'MarketProductBundleSeeder: недостаточно товаров. Сначала запустите MarketProductSeeder.'
                );

                return;
            }

            $defaultCurrency = Currency::query()
                ->where('is_default', true)
                ->first()
                ?? Currency::query()->active()->ordered()->first()
                ?? Currency::query()->first();

            foreach ($this->bundleBlueprints() as $index => $item) {
                /**
                 * Получаем только реально существующие товары
                 * из состава текущего комплекта.
                 */
                $bundleProducts = collect($item['items'])
                    ->map(
                        fn (array $bundleItem) =>
                        $products->get($bundleItem['product_url'])
                    )
                    ->filter()
                    ->values();

                if ($bundleProducts->count() < 2) {
                    $this->command?->warn(
                        "MarketProductBundleSeeder: комплект {$item['url']} пропущен — недостаточно товаров."
                    );

                    continue;
                }

                /**
                 * Компания и магазин указываются только тогда,
                 * когда они одинаковые у всех товаров комплекта.
                 */
                $companyId = $this->resolveCommonId(
                    products: $bundleProducts,
                    field: 'market_company_id'
                );

                $shopId = $this->resolveCommonId(
                    products: $bundleProducts,
                    field: 'market_shop_id'
                );

                /**
                 * Валюта также должна быть единой для всех позиций.
                 * При отсутствии общей валюты используется валюта по умолчанию.
                 */
                $currencyId = $this->resolveCommonId(
                    products: $bundleProducts,
                    field: 'currency_id'
                ) ?? $defaultCurrency?->id;

                $publishedAt = Carbon::now()
                    ->subDays($index)
                    ->startOfDay();

                $bundle = MarketProductBundle::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'url' => $item['url'],
                    ],
                    [
                        'market_company_id' => $companyId,
                        'market_shop_id' => $shopId,

                        'sku' => $item['sku'],
                        'vendor_code' => $item['vendor_code'],
                        'barcode' => $item['barcode'],

                        'currency_id' => $currencyId,
                        'calculate_price' => $item['calculate_price'],

                        /**
                         * Для автоматического режима поле price
                         * остаётся резервным сохранённым значением.
                         */
                        'price' => $item['price'],
                        'old_price' => $item['old_price'],
                        'purchase_price' => $item['purchase_price'],
                        'wholesale_price' => $item['wholesale_price'],
                        'wholesale_min_quantity' =>
                            $item['wholesale_min_quantity'],

                        'sort' => $index,
                        'activity' => true,

                        'left' => $index % 4 === 0,
                        'main' => $index % 3 === 0,
                        'right' => $index % 5 === 0,

                        'is_new' => $index < 3,
                        'is_hit' => in_array($index, [1, 3, 5], true),
                        'is_sale' => $item['old_price'] !== null,

                        'status' => 'published',

                        'moderation_status' => 1,
                        'moderated_by' => $user->id,
                        'moderated_at' => now(),
                        'moderation_note' => null,

                        'published_at' => $publishedAt,
                        'show_from_at' => $publishedAt,
                        'show_to_at' => Carbon::now()
                            ->addYear()
                            ->endOfDay(),

                        'views' => 100 + ($index * 47),
                        'likes_count' => 0,
                        'rating_avg' => 0,
                        'rating_count' => 0,
                    ]
                );

                $this->syncTranslations(
                    bundle: $bundle,
                    translations: $item['translations']
                );

                $this->syncItems(
                    bundle: $bundle,
                    items: $item['items'],
                    products: $products
                );

                /**
                 * Для автоматически рассчитываемых комплектов
                 * записываем демонстрационную старую цену,
                 * превышающую стоимость состава на 10%.
                 */
                if (
                    $bundle->calculate_price
                    && $item['old_price'] === null
                ) {
                    $this->setCalculatedOldPrice($bundle);
                }
            }

            $this->command?->info(
                'MarketProductBundleSeeder: комплекты, переводы и состав успешно созданы.'
            );
        });
    }

    /**
     * Создание или обновление переводов комплекта.
     */
    protected function syncTranslations(
        MarketProductBundle $bundle,
        array $translations
    ): void {
        foreach ($translations as $locale => $translation) {
            $bundle->translations()->updateOrCreate(
                [
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'],
                    'subtitle' => $translation['subtitle'] ?? null,
                    'short' => $translation['short'] ?? null,
                    'description' => $translation['description'] ?? null,

                    'meta_title' => $translation['meta_title']
                        ?? $translation['title'],

                    'meta_keywords' => $translation['meta_keywords']
                        ?? null,

                    'meta_desc' => $translation['meta_desc']
                        ?? $translation['short']
                            ?? null,
                ]
            );
        }
    }

    /**
     * Синхронизация состава комплекта.
     *
     * При повторном запуске сидера старый состав удаляется,
     * после чего создаётся заново без дублирования строк.
     */
    protected function syncItems(
        MarketProductBundle $bundle,
        array $items,
        Collection $products
    ): void {
        $bundle->items()->delete();

        foreach ($items as $index => $item) {
            $product = $products->get($item['product_url']);

            if (! $product instanceof MarketProduct) {
                continue;
            }

            $variant = $this->resolveVariant(
                product: $product,
                useVariant: (bool) ($item['use_variant'] ?? false)
            );

            $bundle->items()->create([
                'market_product_id' => $product->id,

                'market_product_variant_id' =>
                    $variant?->id,

                'quantity' => max(
                    1,
                    (int) ($item['quantity'] ?? 1)
                ),

                'unit_price' => $item['unit_price'] ?? null,
                'discount_type' => $item['discount_type'] ?? null,
                'discount_value' => $item['discount_value'] ?? null,

                'sort' => $index,
                'activity' => true,
            ]);
        }
    }

    /**
     * Получить основной вариант товара.
     *
     * Если основной вариант отсутствует,
     * используется первый вариант по сортировке.
     */
    protected function resolveVariant(
        MarketProduct $product,
        bool $useVariant
    ): ?MarketProductVariant {
        if (! $useVariant) {
            return null;
        }

        $variant = $product->variants
            ->first(
                fn (MarketProductVariant $variant) =>
                $variant->is_default
            )
            ?? $product->variants->first();

        return $variant instanceof MarketProductVariant
            ? $variant
            : null;
    }

    /**
     * Определить общее значение внешнего ключа
     * для всех товаров комплекта.
     */
    protected function resolveCommonId(
        Collection $products,
        string $field
    ): ?int {
        if ($products->isEmpty()) {
            return null;
        }

        $values = $products
            ->pluck($field)
            ->filter(
                fn ($value) => $value !== null
            )
            ->map(
                fn ($value) => (int) $value
            )
            ->unique()
            ->values();

        /**
         * Если хотя бы у одного товара значение отсутствует
         * или значения отличаются, общая связь не назначается.
         */
        if (
            $values->count() !== 1
            || $products->contains(
                fn (MarketProduct $product) =>
                    $product->{$field} === null
            )
        ) {
            return null;
        }

        return (int) $values->first();
    }

    /**
     * Установить старую цену для комплекта
     * с автоматическим расчётом стоимости.
     */
    protected function setCalculatedOldPrice(
        MarketProductBundle $bundle
    ): void {
        $bundle->load([
            'items.product.variants',
            'items.product.defaultVariant',
            'items.variant',
        ]);

        $calculatedPrice = $bundle->calculatedPrice();

        if ($calculatedPrice <= 0) {
            return;
        }

        $bundle->update([
            'old_price' => round(
                $calculatedPrice * 1.10,
                2
            ),
            'is_sale' => true,
        ]);
    }

    /**
     * Заготовки комплектов товаров.
     */
    protected function bundleBlueprints(): array
    {
        return [
            [
                'url' => 'productive-home-office-set',
                'sku' => 'BUNDLE-OFFICE-001',
                'vendor_code' => 'SET-HOME-OFFICE',
                'barcode' => '4870002000010',

                'calculate_price' => true,
                'price' => 0,
                'old_price' => null,
                'purchase_price' => null,
                'wholesale_price' => null,
                'wholesale_min_quantity' => null,

                'items' => [
                    [
                        'product_url' => 'gaming-laptop-15-pro',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 5,
                    ],
                    [
                        'product_url' => 'ergonomic-office-chair',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'fixed',
                        'discount_value' => 5000,
                    ],
                    [
                        'product_url' => 'wireless-headphones-pro',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 10,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Комплект для продуктивного домашнего офиса',
                        'subtitle' => 'Готовое рабочее место для дома',
                        'short' => 'Ноутбук, эргономичное кресло и беспроводные наушники.',
                        'description' => '<p>Комплект для комфортной удалённой работы, программирования и делового общения.</p>',
                        'meta_title' => 'Комплект для домашнего офиса',
                        'meta_keywords' => 'домашний офис, ноутбук, кресло, наушники',
                        'meta_desc' => 'Готовый комплект оборудования для продуктивной работы дома.',
                    ],
                    'kk' => [
                        'title' => 'Өнімді үй кеңсесіне арналған жиынтық',
                        'subtitle' => 'Үйге арналған дайын жұмыс орны',
                        'short' => 'Ноутбук, эргономикалық орындық және сымсыз құлаққап.',
                        'description' => '<p>Қашықтан жұмыс істеуге және бағдарламалауға арналған ыңғайлы жиынтық.</p>',
                        'meta_title' => 'Үй кеңсесіне арналған жиынтық',
                        'meta_keywords' => 'үй кеңсесі, ноутбук, орындық, құлаққап',
                        'meta_desc' => 'Үйде өнімді жұмыс істеуге арналған дайын жабдықтар жиынтығы.',
                    ],
                    'en' => [
                        'title' => 'Productive home office bundle',
                        'subtitle' => 'A complete workspace for home',
                        'short' => 'Laptop, ergonomic chair and wireless headphones.',
                        'description' => '<p>A comfortable bundle for remote work, programming and business communication.</p>',
                        'meta_title' => 'Home office equipment bundle',
                        'meta_keywords' => 'home office, laptop, chair, headphones',
                        'meta_desc' => 'A complete equipment bundle for productive work from home.',
                    ],
                ],
            ],

            [
                'url' => 'smart-clean-home-set',
                'sku' => 'BUNDLE-HOME-002',
                'vendor_code' => 'SET-SMART-CLEAN',
                'barcode' => '4870002000027',

                'calculate_price' => false,
                'price' => 249990,
                'old_price' => 279980,
                'purchase_price' => 205000,
                'wholesale_price' => 229990,
                'wholesale_min_quantity' => 2,

                'items' => [
                    [
                        'product_url' => 'robot-vacuum-cleaner',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                    [
                        'product_url' => 'air-purifier-smart',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Умный комплект для чистого дома',
                        'subtitle' => 'Чистые полы и свежий воздух',
                        'short' => 'Робот-пылесос и умный очиститель воздуха по специальной цене.',
                        'description' => '<p>Комплект помогает автоматизировать ежедневную уборку и поддерживать чистый воздух в помещении.</p>',
                        'meta_title' => 'Умный комплект для уборки дома',
                        'meta_keywords' => 'умный дом, робот-пылесос, очиститель воздуха',
                        'meta_desc' => 'Робот-пылесос и очиститель воздуха в одном комплекте.',
                    ],
                    'kk' => [
                        'title' => 'Таза үйге арналған ақылды жиынтық',
                        'subtitle' => 'Таза еден және таза ауа',
                        'short' => 'Робот-шаңсорғыш пен ақылды ауа тазартқыш арнайы бағамен.',
                        'description' => '<p>Жиынтық күнделікті тазалауды автоматтандыруға және ауаны тазартуға көмектеседі.</p>',
                        'meta_title' => 'Үй тазалауға арналған ақылды жиынтық',
                        'meta_keywords' => 'ақылды үй, робот-шаңсорғыш, ауа тазартқыш',
                        'meta_desc' => 'Робот-шаңсорғыш пен ауа тазартқыш бір жиынтықта.',
                    ],
                    'en' => [
                        'title' => 'Smart clean home bundle',
                        'subtitle' => 'Clean floors and fresh air',
                        'short' => 'Robot vacuum cleaner and smart air purifier at a special price.',
                        'description' => '<p>The bundle automates daily cleaning and helps maintain cleaner indoor air.</p>',
                        'meta_title' => 'Smart home cleaning bundle',
                        'meta_keywords' => 'smart home, robot vacuum, air purifier',
                        'meta_desc' => 'Robot vacuum cleaner and air purifier in one bundle.',
                    ],
                ],
            ],

            [
                'url' => 'home-cinema-set',
                'sku' => 'BUNDLE-CINEMA-003',
                'vendor_code' => 'SET-HOME-CINEMA',
                'barcode' => '4870002000034',

                'calculate_price' => true,
                'price' => 0,
                'old_price' => null,
                'purchase_price' => null,
                'wholesale_price' => null,
                'wholesale_min_quantity' => null,

                'items' => [
                    [
                        'product_url' => 'smart-led-television-55',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 7,
                    ],
                    [
                        'product_url' => 'wireless-headphones-pro',
                        'use_variant' => true,
                        'quantity' => 2,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 10,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Комплект домашнего кинотеатра',
                        'subtitle' => 'Большой экран и персональный звук',
                        'short' => 'Смарт-телевизор 55 дюймов и две пары беспроводных наушников.',
                        'description' => '<p>Комплект для просмотра фильмов, сериалов и спортивных трансляций дома.</p>',
                        'meta_title' => 'Комплект домашнего кинотеатра',
                        'meta_keywords' => 'домашний кинотеатр, телевизор, наушники',
                        'meta_desc' => 'Телевизор и беспроводные наушники для домашнего кинотеатра.',
                    ],
                    'kk' => [
                        'title' => 'Үй кинотеатрының жиынтығы',
                        'subtitle' => 'Үлкен экран және жеке дыбыс',
                        'short' => '55 дюймдік теледидар және екі сымсыз құлаққап.',
                        'description' => '<p>Үйде фильмдер мен спорттық хабарларды көруге арналған жиынтық.</p>',
                        'meta_title' => 'Үй кинотеатрының жиынтығы',
                        'meta_keywords' => 'үй кинотеатры, теледидар, құлаққап',
                        'meta_desc' => 'Үй кинотеатрына арналған теледидар мен сымсыз құлаққаптар.',
                    ],
                    'en' => [
                        'title' => 'Home cinema bundle',
                        'subtitle' => 'Large screen and personal audio',
                        'short' => '55-inch smart television and two pairs of wireless headphones.',
                        'description' => '<p>A bundle for movies, series and sports broadcasts at home.</p>',
                        'meta_title' => 'Home cinema bundle',
                        'meta_keywords' => 'home cinema, television, headphones',
                        'meta_desc' => 'Television and wireless headphones for a home cinema setup.',
                    ],
                ],
            ],

            [
                'url' => 'content-creator-set',
                'sku' => 'BUNDLE-CREATOR-004',
                'vendor_code' => 'SET-CONTENT-CREATOR',
                'barcode' => '4870002000041',

                'calculate_price' => false,
                'price' => 979990,
                'old_price' => 1049970,
                'purchase_price' => 825000,
                'wholesale_price' => 939990,
                'wholesale_min_quantity' => 2,

                'items' => [
                    [
                        'product_url' => 'compact-digital-camera',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                    [
                        'product_url' => 'gaming-laptop-15-pro',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                    [
                        'product_url' => 'wireless-headphones-pro',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Комплект создателя контента',
                        'subtitle' => 'Съёмка, монтаж и качественный звук',
                        'short' => 'Камера, производительный ноутбук и беспроводные наушники.',
                        'description' => '<p>Готовое решение для блогеров, фотографов, дизайнеров и видеомонтажёров.</p>',
                        'meta_title' => 'Комплект техники для создателя контента',
                        'meta_keywords' => 'камера, ноутбук, блогер, видеомонтаж',
                        'meta_desc' => 'Комплект техники для съёмки и обработки цифрового контента.',
                    ],
                    'kk' => [
                        'title' => 'Контент жасаушының жиынтығы',
                        'subtitle' => 'Түсіру, монтаждау және сапалы дыбыс',
                        'short' => 'Камера, өнімді ноутбук және сымсыз құлаққап.',
                        'description' => '<p>Блогерлерге, фотографтарға және бейне монтаждаушыларға арналған шешім.</p>',
                        'meta_title' => 'Контент жасаушыға арналған техника жиынтығы',
                        'meta_keywords' => 'камера, ноутбук, блогер, бейне монтаж',
                        'meta_desc' => 'Сандық контентті түсіруге және өңдеуге арналған техника.',
                    ],
                    'en' => [
                        'title' => 'Content creator bundle',
                        'subtitle' => 'Capture, editing and quality audio',
                        'short' => 'Camera, high-performance laptop and wireless headphones.',
                        'description' => '<p>A complete solution for bloggers, photographers, designers and video editors.</p>',
                        'meta_title' => 'Content creator equipment bundle',
                        'meta_keywords' => 'camera, laptop, blogger, video editing',
                        'meta_desc' => 'Equipment bundle for capturing and editing digital content.',
                    ],
                ],
            ],

            [
                'url' => 'professional-workshop-set',
                'sku' => 'BUNDLE-TOOLS-005',
                'vendor_code' => 'SET-WORKSHOP-PRO',
                'barcode' => '4870002000058',

                'calculate_price' => true,
                'price' => 0,
                'old_price' => null,
                'purchase_price' => null,
                'wholesale_price' => null,
                'wholesale_min_quantity' => null,

                'items' => [
                    [
                        'product_url' => 'professional-cordless-drill',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 5,
                    ],
                    [
                        'product_url' => 'professional-tool-set',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'fixed',
                        'discount_value' => 10000,
                    ],
                    [
                        'product_url' => 'portable-power-station',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => 'percent',
                        'discount_value' => 5,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Профессиональный комплект для мастерской',
                        'subtitle' => 'Инструменты и автономное питание',
                        'short' => 'Дрель, набор инструментов и портативная электростанция.',
                        'description' => '<p>Комплект для ремонтных, монтажных и выездных работ без привязки к электросети.</p>',
                        'meta_title' => 'Профессиональный комплект инструментов',
                        'meta_keywords' => 'мастерская, дрель, инструменты, электростанция',
                        'meta_desc' => 'Комплект инструментов и резервного питания для мастерской.',
                    ],
                    'kk' => [
                        'title' => 'Шеберханаға арналған кәсіби жиынтық',
                        'subtitle' => 'Құралдар және автономды қуат',
                        'short' => 'Бұрғы, құралдар жиынтығы және тасымалды электр станциясы.',
                        'description' => '<p>Электр желісінсіз жөндеу және монтаждау жұмыстарына арналған жиынтық.</p>',
                        'meta_title' => 'Кәсіби құралдар жиынтығы',
                        'meta_keywords' => 'шеберхана, бұрғы, құралдар, электр станциясы',
                        'meta_desc' => 'Шеберханаға арналған құралдар мен резервтік қуат жиынтығы.',
                    ],
                    'en' => [
                        'title' => 'Professional workshop bundle',
                        'subtitle' => 'Tools and independent power',
                        'short' => 'Cordless drill, professional tool set and portable power station.',
                        'description' => '<p>A bundle for repair, installation and field work away from the power grid.</p>',
                        'meta_title' => 'Professional workshop tool bundle',
                        'meta_keywords' => 'workshop, drill, tools, power station',
                        'meta_desc' => 'Tools and backup power equipment for professional workshop use.',
                    ],
                ],
            ],

            [
                'url' => 'active-outdoor-set',
                'sku' => 'BUNDLE-OUTDOOR-006',
                'vendor_code' => 'SET-ACTIVE-OUTDOOR',
                'barcode' => '4870002000065',

                'calculate_price' => false,
                'price' => 999990,
                'old_price' => 1039980,
                'purchase_price' => 840000,
                'wholesale_price' => 949990,
                'wholesale_min_quantity' => 2,

                'items' => [
                    [
                        'product_url' => 'electric-mountain-bike',
                        'use_variant' => true,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                    [
                        'product_url' => 'portable-power-station',
                        'use_variant' => false,
                        'quantity' => 1,
                        'unit_price' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                    ],
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Комплект для активного отдыха',
                        'subtitle' => 'Энергия для путешествий и приключений',
                        'short' => 'Электрический велосипед и портативная электростанция.',
                        'description' => '<p>Комплект для длительных поездок, отдыха на природе и автономных путешествий.</p>',
                        'meta_title' => 'Комплект для активного отдыха',
                        'meta_keywords' => 'электровелосипед, электростанция, путешествия',
                        'meta_desc' => 'Электровелосипед и портативное питание для активного отдыха.',
                    ],
                    'kk' => [
                        'title' => 'Белсенді демалысқа арналған жиынтық',
                        'subtitle' => 'Саяхат пен шытырман оқиғаға арналған қуат',
                        'short' => 'Электрлік велосипед және тасымалды электр станциясы.',
                        'description' => '<p>Ұзақ сапарларға және табиғатта автономды демалуға арналған жиынтық.</p>',
                        'meta_title' => 'Белсенді демалысқа арналған жиынтық',
                        'meta_keywords' => 'электрлік велосипед, электр станциясы, саяхат',
                        'meta_desc' => 'Белсенді демалысқа арналған велосипед пен тасымалды қуат.',
                    ],
                    'en' => [
                        'title' => 'Active outdoor bundle',
                        'subtitle' => 'Power for travel and adventure',
                        'short' => 'Electric mountain bike and portable power station.',
                        'description' => '<p>A bundle for long rides, outdoor recreation and independent travel.</p>',
                        'meta_title' => 'Active outdoor equipment bundle',
                        'meta_keywords' => 'electric bike, power station, travel',
                        'meta_desc' => 'Electric bike and portable power equipment for outdoor adventures.',
                    ],
                ],
            ],
        ];
    }
}
