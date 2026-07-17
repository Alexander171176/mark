<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Market\MarketTag\MarketTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketProductSeeder extends Seeder
{
    /** Заполнение товаров маркетплейса. */
    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::query()->find(1)
                ?? User::query()->first();

            if (! $user) {
                $this->command?->warn(
                    'MarketProductSeeder: пользователь не найден. Сидер остановлен.'
                );

                return;
            }

            $companies = MarketCompany::query()
                ->orderBy('id')
                ->get();

            $shops = MarketShop::query()
                ->orderBy('id')
                ->get();

            $brands = MarketBrand::query()
                ->orderBy('id')
                ->get();

            $categories = MarketCategory::query()
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $tags = MarketTag::query()
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $currency = Currency::query()
                ->where('is_default', true)
                ->first()
                ?? Currency::query()->active()->ordered()->first()
                ?? Currency::query()->first();

            $products = collect();

            foreach ($this->productBlueprints() as $index => $item) {
                $shop = $shops->isNotEmpty()
                    ? $shops[$index % $shops->count()]
                    : null;

                /*
                 * Если выбран магазин, используем его компанию.
                 * Иначе берём компанию из общего списка.
                 */
                $companyId = $shop?->market_company_id;

                if (! $companyId && $companies->isNotEmpty()) {
                    $companyId = $companies[
                    $index % $companies->count()
                    ]->id;
                }

                $brandId = $brands->isNotEmpty()
                    ? $brands[$index % $brands->count()]->id
                    : null;

                $publishedAt = Carbon::now()
                    ->subDays($index)
                    ->startOfDay();

                $product = MarketProduct::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'url' => $item['url'],
                    ],
                    [
                        'market_company_id' => $companyId,
                        'market_shop_id' => $shop?->id,
                        'market_brand_id' => $brandId,

                        'sku' => $item['sku'],
                        'vendor_code' => $item['vendor_code'],
                        'barcode' => $item['barcode'],

                        'currency_id' => $currency?->id,

                        'price' => $item['price'],
                        'old_price' => $item['old_price'],
                        'purchase_price' => $item['purchase_price'],

                        'wholesale_price' => $item['wholesale_price'],
                        'wholesale_min_quantity' =>
                            $item['wholesale_min_quantity'],

                        'quantity' => $item['quantity'],
                        'in_stock' => $item['quantity'] > 0,

                        'weight' => $item['weight'],
                        'length' => $item['length'],
                        'width' => $item['width'],
                        'height' => $item['height'],

                        'sort' => $index,
                        'activity' => true,

                        'left' => $index % 5 === 0,
                        'main' => $index % 4 === 0,
                        'right' => $index % 6 === 0,

                        'is_new' => $index < 4,
                        'is_hit' => in_array($index, [1, 4, 7, 10], true),
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

                        'views' => 50 + ($index * 37),
                        'likes_count' => 0,
                        'rating_avg' => 0,
                        'rating_count' => 0,
                    ]
                );

                $this->syncTranslations(
                    product: $product,
                    translations: $item['translations']
                );

                $this->syncCategories(
                    product: $product,
                    categories: $categories,
                    index: $index
                );

                $this->syncTags(
                    product: $product,
                    tags: $tags,
                    index: $index
                );

                $products->push($product);
            }

            $this->syncRelatedProducts($products);

            $this->command?->info(
                'MarketProductSeeder: товары и переводы успешно созданы.'
            );
        });
    }

    /** Создание или обновление переводов товара. */
    protected function syncTranslations(
        MarketProduct $product,
        array $translations
    ): void {
        foreach ($translations as $locale => $translation) {
            $product->translations()->updateOrCreate(
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
     * Привязка категорий.
     *
     * Первая категория становится основной.
     */
    protected function syncCategories(
        MarketProduct $product,
                      $categories,
        int $index
    ): void {
        if ($categories->isEmpty()) {
            return;
        }

        $selectedCategories = collect([
            $categories[$index % $categories->count()],
            $categories[($index + 1) % $categories->count()],
        ])
            ->filter()
            ->unique('id')
            ->values();

        $syncData = [];

        foreach ($selectedCategories as $order => $category) {
            $syncData[$category->id] = [
                'main' => $order === 0,
                'order' => $order,
            ];
        }

        $product->categories()->sync($syncData);
    }

    /** Привязка тегов. */
    protected function syncTags(
        MarketProduct $product,
                      $tags,
        int $index
    ): void {
        if ($tags->isEmpty()) {
            return;
        }

        $selectedTags = collect([
            $tags[$index % $tags->count()],
            $tags[($index + 1) % $tags->count()],
            $tags[($index + 2) % $tags->count()],
        ])
            ->filter()
            ->unique('id')
            ->values();

        $syncData = [];

        foreach ($selectedTags as $order => $tag) {
            $syncData[$tag->id] = [
                'order' => $order,
            ];
        }

        $product->tags()->sync($syncData);
    }

    /** Связанные и рекомендуемые товары. */
    protected function syncRelatedProducts($products): void
    {
        if ($products->count() < 2) {
            return;
        }

        foreach ($products->values() as $index => $product) {
            $candidates = collect([
                [
                    'product' => $products[
                    ($index + 1) % $products->count()
                    ],
                    'type' => 'related',
                ],
                [
                    'product' => $products[
                    ($index + 2) % $products->count()
                    ],
                    'type' => 'similar',
                ],
                [
                    'product' => $products[
                    ($index + 3) % $products->count()
                    ],
                    'type' => 'accessory',
                ],
            ])
                ->filter(
                    fn (array $item) =>
                        $item['product']->id !== $product->id
                )
                ->unique(
                    fn (array $item) =>
                        $item['product']->id . ':' . $item['type']
                )
                ->values();

            $syncData = [];

            foreach ($candidates as $order => $candidate) {
                $syncData[$candidate['product']->id] = [
                    'type' => $candidate['type'],
                    'order' => $order,
                    'activity' => true,
                ];
            }

            /*
             * В belongsToMany sync() ключом является ID товара.
             * Поэтому один и тот же товар здесь используется один раз.
             */
            $product->relatedProducts()->sync($syncData);
        }
    }

    /** Заготовки товаров на трёх языках. */
    protected function productBlueprints(): array
    {
        return [
            [
                'url' => 'professional-cordless-drill',
                'sku' => 'TOOL-DRILL-001',
                'vendor_code' => 'PD-18V-001',
                'barcode' => '4870001000011',

                'price' => 89990,
                'old_price' => 99990,
                'purchase_price' => 65000,
                'wholesale_price' => 82990,
                'wholesale_min_quantity' => 3,

                'quantity' => 24,

                'weight' => 2.150,
                'length' => 32.00,
                'width' => 10.00,
                'height' => 28.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Профессиональная аккумуляторная дрель',
                        'subtitle' => 'Мощный инструмент для дома и мастерской',
                        'short' => 'Аккумуляторная дрель 18 В с двумя батареями и кейсом.',
                        'description' => '<p>Надёжная аккумуляторная дрель для монтажных, ремонтных и строительных работ.</p><p>Комплектуется двумя аккумуляторами, зарядным устройством и прочным кейсом.</p>',
                        'meta_title' => 'Купить профессиональную аккумуляторную дрель',
                        'meta_keywords' => 'дрель, аккумуляторная дрель, инструмент',
                        'meta_desc' => 'Профессиональная аккумуляторная дрель 18 В с комплектом батарей.',
                    ],
                    'kk' => [
                        'title' => 'Кәсіби аккумуляторлы бұрғы',
                        'subtitle' => 'Үй мен шеберханаға арналған қуатты құрал',
                        'short' => 'Екі аккумуляторы және қорабы бар 18 В бұрғы.',
                        'description' => '<p>Монтаждау, жөндеу және құрылыс жұмыстарына арналған сенімді аккумуляторлы бұрғы.</p>',
                        'meta_title' => 'Кәсіби аккумуляторлы бұрғы сатып алу',
                        'meta_keywords' => 'бұрғы, аккумуляторлы бұрғы, құрал',
                        'meta_desc' => 'Екі аккумуляторы бар кәсіби 18 В бұрғы.',
                    ],
                    'en' => [
                        'title' => 'Professional cordless drill',
                        'subtitle' => 'Powerful tool for home and workshop',
                        'short' => '18V cordless drill with two batteries and carrying case.',
                        'description' => '<p>A reliable cordless drill for installation, repair and construction work.</p>',
                        'meta_title' => 'Buy a professional cordless drill',
                        'meta_keywords' => 'drill, cordless drill, power tool',
                        'meta_desc' => 'Professional 18V cordless drill supplied with two batteries.',
                    ],
                ],
            ],

            [
                'url' => 'smart-led-television-55',
                'sku' => 'TV-SMART-055',
                'vendor_code' => 'SMART-4K-55',
                'barcode' => '4870001000028',

                'price' => 279990,
                'old_price' => 319990,
                'purchase_price' => 225000,
                'wholesale_price' => 259990,
                'wholesale_min_quantity' => 2,

                'quantity' => 12,

                'weight' => 14.500,
                'length' => 123.00,
                'width' => 8.00,
                'height' => 72.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Смарт-телевизор LED 55 дюймов',
                        'subtitle' => 'Большой экран с разрешением 4K',
                        'short' => 'Современный Smart TV с Wi-Fi, HDR и голосовым управлением.',
                        'description' => '<p>Телевизор с экраном 55 дюймов, разрешением 4K и поддержкой современных потоковых сервисов.</p>',
                        'meta_title' => 'Смарт-телевизор 55 дюймов 4K',
                        'meta_keywords' => 'телевизор, smart tv, 4k, led',
                        'meta_desc' => 'Смарт-телевизор 55 дюймов с 4K, HDR и Wi-Fi.',
                    ],
                    'kk' => [
                        'title' => '55 дюймдік LED смарт-теледидар',
                        'subtitle' => '4K ажыратымдылығы бар үлкен экран',
                        'short' => 'Wi-Fi, HDR және дауыстық басқаруы бар заманауи Smart TV.',
                        'description' => '<p>55 дюймдік экраны және 4K ажыратымдылығы бар заманауи теледидар.</p>',
                        'meta_title' => '55 дюймдік 4K смарт-теледидар',
                        'meta_keywords' => 'теледидар, smart tv, 4k, led',
                        'meta_desc' => '4K, HDR және Wi-Fi қолдауы бар 55 дюймдік теледидар.',
                    ],
                    'en' => [
                        'title' => '55-inch LED smart television',
                        'subtitle' => 'Large screen with 4K resolution',
                        'short' => 'Modern Smart TV with Wi-Fi, HDR and voice control.',
                        'description' => '<p>A 55-inch television with 4K resolution and modern streaming services.</p>',
                        'meta_title' => '55-inch 4K smart television',
                        'meta_keywords' => 'television, smart tv, 4k, led',
                        'meta_desc' => '55-inch Smart TV with 4K, HDR and Wi-Fi.',
                    ],
                ],
            ],

            [
                'url' => 'ergonomic-office-chair',
                'sku' => 'FURN-CHAIR-003',
                'vendor_code' => 'ERGONOMIC-BLACK',
                'barcode' => '4870001000035',

                'price' => 74990,
                'old_price' => null,
                'purchase_price' => 51000,
                'wholesale_price' => 68990,
                'wholesale_min_quantity' => 5,

                'quantity' => 30,

                'weight' => 16.800,
                'length' => 68.00,
                'width' => 65.00,
                'height' => 118.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Эргономичное офисное кресло',
                        'subtitle' => 'Комфорт для продолжительной работы',
                        'short' => 'Кресло с регулируемой спинкой, подлокотниками и поясничной поддержкой.',
                        'description' => '<p>Эргономичное кресло обеспечивает правильную посадку и комфорт в течение рабочего дня.</p>',
                        'meta_title' => 'Эргономичное офисное кресло',
                        'meta_keywords' => 'офисное кресло, мебель, эргономика',
                        'meta_desc' => 'Удобное офисное кресло с регулировками и поясничной поддержкой.',
                    ],
                    'kk' => [
                        'title' => 'Эргономикалық кеңсе орындығы',
                        'subtitle' => 'Ұзақ жұмыс істеуге арналған жайлылық',
                        'short' => 'Арқалығы, шынтақ тірегі және бел тірегі реттелетін орындық.',
                        'description' => '<p>Эргономикалық орындық жұмыс күні бойы дұрыс отыруды және жайлылықты қамтамасыз етеді.</p>',
                        'meta_title' => 'Эргономикалық кеңсе орындығы',
                        'meta_keywords' => 'кеңсе орындығы, жиһаз, эргономика',
                        'meta_desc' => 'Реттелетін және бел тірегі бар ыңғайлы кеңсе орындығы.',
                    ],
                    'en' => [
                        'title' => 'Ergonomic office chair',
                        'subtitle' => 'Comfort for long working sessions',
                        'short' => 'Chair with adjustable backrest, armrests and lumbar support.',
                        'description' => '<p>An ergonomic chair designed to provide proper posture and comfort throughout the working day.</p>',
                        'meta_title' => 'Ergonomic office chair',
                        'meta_keywords' => 'office chair, furniture, ergonomics',
                        'meta_desc' => 'Comfortable office chair with multiple adjustments and lumbar support.',
                    ],
                ],
            ],

            [
                'url' => 'automatic-coffee-machine',
                'sku' => 'HOME-COFFEE-004',
                'vendor_code' => 'COFFEE-PRO-15',
                'barcode' => '4870001000042',

                'price' => 189990,
                'old_price' => 209990,
                'purchase_price' => 151000,
                'wholesale_price' => 176990,
                'wholesale_min_quantity' => 3,

                'quantity' => 15,

                'weight' => 8.700,
                'length' => 42.00,
                'width' => 25.00,
                'height' => 37.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Автоматическая кофемашина',
                        'subtitle' => 'Свежий кофе одним нажатием',
                        'short' => 'Кофемашина с кофемолкой, капучинатором и автоматическими программами.',
                        'description' => '<p>Автоматическая кофемашина готовит эспрессо, американо и молочные напитки из свежемолотого зерна.</p>',
                        'meta_title' => 'Автоматическая кофемашина для дома',
                        'meta_keywords' => 'кофемашина, кофе, капучино',
                        'meta_desc' => 'Автоматическая кофемашина со встроенной кофемолкой.',
                    ],
                    'kk' => [
                        'title' => 'Автоматты кофе машинасы',
                        'subtitle' => 'Бір батырмамен жаңа дайындалған кофе',
                        'short' => 'Кофе тартқышы, капучинаторы және автоматты бағдарламалары бар машина.',
                        'description' => '<p>Автоматты кофе машинасы жаңа тартылған дәннен кофе сусындарын дайындайды.</p>',
                        'meta_title' => 'Үйге арналған автоматты кофе машинасы',
                        'meta_keywords' => 'кофе машинасы, кофе, капучино',
                        'meta_desc' => 'Кіріктірілген кофе тартқышы бар автоматты кофе машинасы.',
                    ],
                    'en' => [
                        'title' => 'Automatic coffee machine',
                        'subtitle' => 'Fresh coffee at the touch of a button',
                        'short' => 'Coffee machine with grinder, milk frother and automatic programs.',
                        'description' => '<p>An automatic machine for espresso, americano and milk-based drinks made from freshly ground beans.</p>',
                        'meta_title' => 'Automatic coffee machine for home',
                        'meta_keywords' => 'coffee machine, coffee, cappuccino',
                        'meta_desc' => 'Automatic coffee machine with an integrated grinder.',
                    ],
                ],
            ],

            [
                'url' => 'gaming-laptop-15-pro',
                'sku' => 'PC-LAPTOP-005',
                'vendor_code' => 'GAMING-15-PRO',
                'barcode' => '4870001000059',

                'price' => 649990,
                'old_price' => null,
                'purchase_price' => 540000,
                'wholesale_price' => 619990,
                'wholesale_min_quantity' => 2,

                'quantity' => 8,

                'weight' => 2.350,
                'length' => 36.00,
                'width' => 25.00,
                'height' => 2.40,

                'translations' => [
                    'ru' => [
                        'title' => 'Игровой ноутбук 15 Pro',
                        'subtitle' => 'Высокая производительность для игр и работы',
                        'short' => 'Мощный ноутбук с производительным процессором и дискретной графикой.',
                        'description' => '<p>Игровой ноутбук подходит для современных игр, программирования, дизайна и видеомонтажа.</p>',
                        'meta_title' => 'Игровой ноутбук 15 Pro',
                        'meta_keywords' => 'игровой ноутбук, компьютер, gpu',
                        'meta_desc' => 'Производительный игровой ноутбук с дискретной видеокартой.',
                    ],
                    'kk' => [
                        'title' => '15 Pro ойын ноутбугы',
                        'subtitle' => 'Ойын мен жұмысқа арналған жоғары өнімділік',
                        'short' => 'Қуатты процессоры және дискретті графикасы бар ноутбук.',
                        'description' => '<p>Ойын ноутбугы заманауи ойындар, бағдарламалау және бейне өңдеуге арналған.</p>',
                        'meta_title' => '15 Pro ойын ноутбугы',
                        'meta_keywords' => 'ойын ноутбугы, компьютер, gpu',
                        'meta_desc' => 'Дискретті бейне картасы бар өнімді ойын ноутбугы.',
                    ],
                    'en' => [
                        'title' => 'Gaming laptop 15 Pro',
                        'subtitle' => 'High performance for gaming and work',
                        'short' => 'Powerful laptop with a high-performance processor and dedicated graphics.',
                        'description' => '<p>A gaming laptop suitable for modern games, programming, design and video editing.</p>',
                        'meta_title' => 'Gaming laptop 15 Pro',
                        'meta_keywords' => 'gaming laptop, computer, gpu',
                        'meta_desc' => 'High-performance gaming laptop with dedicated graphics.',
                    ],
                ],
            ],

            [
                'url' => 'robot-vacuum-cleaner',
                'sku' => 'HOME-ROBOT-006',
                'vendor_code' => 'ROBOT-CLEAN-X2',
                'barcode' => '4870001000066',

                'price' => 159990,
                'old_price' => 179990,
                'purchase_price' => 125000,
                'wholesale_price' => 147990,
                'wholesale_min_quantity' => 3,

                'quantity' => 21,

                'weight' => 3.900,
                'length' => 35.00,
                'width' => 35.00,
                'height' => 9.50,

                'translations' => [
                    'ru' => [
                        'title' => 'Робот-пылесос с влажной уборкой',
                        'subtitle' => 'Автоматическая чистота каждый день',
                        'short' => 'Умный робот-пылесос с навигацией, картой помещения и влажной уборкой.',
                        'description' => '<p>Робот-пылесос самостоятельно строит карту помещения и выполняет сухую и влажную уборку.</p>',
                        'meta_title' => 'Робот-пылесос с влажной уборкой',
                        'meta_keywords' => 'робот-пылесос, уборка, умный дом',
                        'meta_desc' => 'Умный робот-пылесос с навигацией и влажной уборкой.',
                    ],
                    'kk' => [
                        'title' => 'Ылғалды тазалауы бар робот-шаңсорғыш',
                        'subtitle' => 'Күн сайын автоматты тазалық',
                        'short' => 'Навигациясы және бөлме картасы бар ақылды робот-шаңсорғыш.',
                        'description' => '<p>Робот бөлменің картасын жасап, құрғақ және ылғалды тазалау жүргізеді.</p>',
                        'meta_title' => 'Ылғалды тазалауы бар робот-шаңсорғыш',
                        'meta_keywords' => 'робот-шаңсорғыш, тазалау, ақылды үй',
                        'meta_desc' => 'Навигациясы және ылғалды тазалауы бар робот-шаңсорғыш.',
                    ],
                    'en' => [
                        'title' => 'Robot vacuum cleaner with mopping',
                        'subtitle' => 'Automatic cleaning every day',
                        'short' => 'Smart robot vacuum with navigation, room mapping and wet cleaning.',
                        'description' => '<p>The robot creates a room map and performs both vacuuming and mopping.</p>',
                        'meta_title' => 'Robot vacuum cleaner with mopping',
                        'meta_keywords' => 'robot vacuum, cleaning, smart home',
                        'meta_desc' => 'Smart robot vacuum cleaner with navigation and wet cleaning.',
                    ],
                ],
            ],

            [
                'url' => 'wireless-headphones-pro',
                'sku' => 'AUDIO-HEAD-007',
                'vendor_code' => 'SOUND-PRO-ANC',
                'barcode' => '4870001000073',

                'price' => 69990,
                'old_price' => 79990,
                'purchase_price' => 47000,
                'wholesale_price' => 63990,
                'wholesale_min_quantity' => 5,

                'quantity' => 40,

                'weight' => 0.280,
                'length' => 19.00,
                'width' => 17.00,
                'height' => 8.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Беспроводные наушники Pro',
                        'subtitle' => 'Чистый звук и активное шумоподавление',
                        'short' => 'Полноразмерные Bluetooth-наушники с ANC и длительной автономностью.',
                        'description' => '<p>Беспроводные наушники обеспечивают детальный звук и комфорт при длительном использовании.</p>',
                        'meta_title' => 'Беспроводные наушники Pro с ANC',
                        'meta_keywords' => 'наушники, bluetooth, anc',
                        'meta_desc' => 'Bluetooth-наушники с активным шумоподавлением.',
                    ],
                    'kk' => [
                        'title' => 'Pro сымсыз құлаққаптары',
                        'subtitle' => 'Таза дыбыс және белсенді шуды басу',
                        'short' => 'ANC және ұзақ жұмыс уақыты бар Bluetooth құлаққаптары.',
                        'description' => '<p>Сымсыз құлаққаптар анық дыбыс пен ұзақ пайдалануда жайлылық береді.</p>',
                        'meta_title' => 'ANC бар Pro сымсыз құлаққаптары',
                        'meta_keywords' => 'құлаққап, bluetooth, anc',
                        'meta_desc' => 'Белсенді шуды басуы бар Bluetooth құлаққаптары.',
                    ],
                    'en' => [
                        'title' => 'Wireless headphones Pro',
                        'subtitle' => 'Clear sound and active noise cancellation',
                        'short' => 'Over-ear Bluetooth headphones with ANC and long battery life.',
                        'description' => '<p>Wireless headphones delivering detailed sound and comfort during extended use.</p>',
                        'meta_title' => 'Wireless headphones Pro with ANC',
                        'meta_keywords' => 'headphones, bluetooth, anc',
                        'meta_desc' => 'Bluetooth headphones with active noise cancellation.',
                    ],
                ],
            ],

            [
                'url' => 'electric-mountain-bike',
                'sku' => 'SPORT-BIKE-008',
                'vendor_code' => 'E-MTB-500',
                'barcode' => '4870001000080',

                'price' => 589990,
                'old_price' => null,
                'purchase_price' => 475000,
                'wholesale_price' => 559990,
                'wholesale_min_quantity' => 2,

                'quantity' => 6,

                'weight' => 24.500,
                'length' => 185.00,
                'width' => 72.00,
                'height' => 110.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Электрический горный велосипед',
                        'subtitle' => 'Свобода движения по городу и бездорожью',
                        'short' => 'Горный электровелосипед с мощным мотором и ёмким аккумулятором.',
                        'description' => '<p>Электровелосипед рассчитан на городские поездки, пересечённую местность и продолжительные маршруты.</p>',
                        'meta_title' => 'Электрический горный велосипед',
                        'meta_keywords' => 'электровелосипед, велосипед, спорт',
                        'meta_desc' => 'Горный электровелосипед с мощным мотором.',
                    ],
                    'kk' => [
                        'title' => 'Электрлік тау велосипеді',
                        'subtitle' => 'Қалада және жолсыз жерде еркін қозғалыс',
                        'short' => 'Қуатты қозғалтқышы және сыйымды аккумуляторы бар велосипед.',
                        'description' => '<p>Электрлік велосипед қалаға және күрделі жерлерде жүруге арналған.</p>',
                        'meta_title' => 'Электрлік тау велосипеді',
                        'meta_keywords' => 'электрлік велосипед, велосипед, спорт',
                        'meta_desc' => 'Қуатты қозғалтқышы бар тау электровелосипеді.',
                    ],
                    'en' => [
                        'title' => 'Electric mountain bike',
                        'subtitle' => 'Freedom for city and off-road riding',
                        'short' => 'Mountain e-bike with a powerful motor and high-capacity battery.',
                        'description' => '<p>An electric bicycle designed for urban travel, trails and longer routes.</p>',
                        'meta_title' => 'Electric mountain bike',
                        'meta_keywords' => 'electric bike, bicycle, sport',
                        'meta_desc' => 'Mountain electric bike with a powerful motor.',
                    ],
                ],
            ],

            [
                'url' => 'air-purifier-smart',
                'sku' => 'HOME-AIR-009',
                'vendor_code' => 'AIR-CLEAN-450',
                'barcode' => '4870001000097',

                'price' => 119990,
                'old_price' => 139990,
                'purchase_price' => 89000,
                'wholesale_price' => 109990,
                'wholesale_min_quantity' => 4,

                'quantity' => 18,

                'weight' => 7.200,
                'length' => 29.00,
                'width' => 29.00,
                'height' => 58.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Умный очиститель воздуха',
                        'subtitle' => 'Чистый воздух для дома и офиса',
                        'short' => 'Очиститель с HEPA-фильтром, датчиком качества воздуха и Wi-Fi.',
                        'description' => '<p>Устройство удаляет пыль, аллергены и мелкие частицы, автоматически регулируя мощность.</p>',
                        'meta_title' => 'Умный очиститель воздуха с HEPA',
                        'meta_keywords' => 'очиститель воздуха, hepa, климат',
                        'meta_desc' => 'Очиститель воздуха с HEPA-фильтром и Wi-Fi.',
                    ],
                    'kk' => [
                        'title' => 'Ақылды ауа тазартқыш',
                        'subtitle' => 'Үй мен кеңсеге арналған таза ауа',
                        'short' => 'HEPA сүзгісі, ауа сапасы датчигі және Wi-Fi бар құрылғы.',
                        'description' => '<p>Құрылғы шаңды, аллергендерді және ұсақ бөлшектерді жояды.</p>',
                        'meta_title' => 'HEPA сүзгісі бар ақылды ауа тазартқыш',
                        'meta_keywords' => 'ауа тазартқыш, hepa, климат',
                        'meta_desc' => 'HEPA сүзгісі және Wi-Fi бар ауа тазартқыш.',
                    ],
                    'en' => [
                        'title' => 'Smart air purifier',
                        'subtitle' => 'Clean air for home and office',
                        'short' => 'Air purifier with HEPA filter, air quality sensor and Wi-Fi.',
                        'description' => '<p>The device removes dust, allergens and fine particles while automatically adjusting power.</p>',
                        'meta_title' => 'Smart air purifier with HEPA',
                        'meta_keywords' => 'air purifier, hepa, climate',
                        'meta_desc' => 'Air purifier with HEPA filtration and Wi-Fi.',
                    ],
                ],
            ],

            [
                'url' => 'compact-digital-camera',
                'sku' => 'PHOTO-CAMERA-010',
                'vendor_code' => 'CAM-4K-COMPACT',
                'barcode' => '4870001000103',

                'price' => 329990,
                'old_price' => null,
                'purchase_price' => 275000,
                'wholesale_price' => 309990,
                'wholesale_min_quantity' => 2,

                'quantity' => 9,

                'weight' => 0.620,
                'length' => 13.00,
                'width' => 8.00,
                'height' => 9.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Компактная цифровая камера',
                        'subtitle' => 'Качественные фотографии и видео 4K',
                        'short' => 'Камера с быстрым автофокусом, стабилизацией и поворотным экраном.',
                        'description' => '<p>Компактная камера подходит для путешествий, блогов, семейной и предметной съёмки.</p>',
                        'meta_title' => 'Компактная цифровая камера 4K',
                        'meta_keywords' => 'камера, фотоаппарат, 4k',
                        'meta_desc' => 'Компактная камера с автофокусом и записью видео 4K.',
                    ],
                    'kk' => [
                        'title' => 'Ықшам сандық камера',
                        'subtitle' => 'Сапалы фотосуреттер және 4K бейне',
                        'short' => 'Жылдам автофокусы, тұрақтандыруы және бұрылмалы экраны бар камера.',
                        'description' => '<p>Ықшам камера саяхатқа, блогқа және заттарды түсіруге арналған.</p>',
                        'meta_title' => 'Ықшам 4K сандық камера',
                        'meta_keywords' => 'камера, фотоаппарат, 4k',
                        'meta_desc' => 'Автофокусы және 4K бейне жазуы бар ықшам камера.',
                    ],
                    'en' => [
                        'title' => 'Compact digital camera',
                        'subtitle' => 'High-quality photos and 4K video',
                        'short' => 'Camera with fast autofocus, stabilization and a vari-angle display.',
                        'description' => '<p>A compact camera suitable for travel, blogging, family and product photography.</p>',
                        'meta_title' => 'Compact 4K digital camera',
                        'meta_keywords' => 'camera, photography, 4k',
                        'meta_desc' => 'Compact camera with fast autofocus and 4K video recording.',
                    ],
                ],
            ],

            [
                'url' => 'portable-power-station',
                'sku' => 'POWER-STATION-011',
                'vendor_code' => 'POWER-1000-W',
                'barcode' => '4870001000110',

                'price' => 449990,
                'old_price' => 489990,
                'purchase_price' => 365000,
                'wholesale_price' => 424990,
                'wholesale_min_quantity' => 2,

                'quantity' => 7,

                'weight' => 11.800,
                'length' => 39.00,
                'width' => 27.00,
                'height' => 28.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Портативная электростанция',
                        'subtitle' => 'Резервное питание дома и в поездках',
                        'short' => 'Мобильная станция мощностью 1000 Вт с несколькими типами разъёмов.',
                        'description' => '<p>Портативная электростанция обеспечивает резервное питание техники, инструментов и мобильных устройств.</p>',
                        'meta_title' => 'Портативная электростанция 1000 Вт',
                        'meta_keywords' => 'электростанция, аккумулятор, питание',
                        'meta_desc' => 'Мобильная электростанция мощностью 1000 Вт.',
                    ],
                    'kk' => [
                        'title' => 'Тасымалды электр станциясы',
                        'subtitle' => 'Үйде және сапарда резервтік қуат',
                        'short' => 'Бірнеше қосқышы бар 1000 Вт мобильді станция.',
                        'description' => '<p>Тасымалды электр станциясы техника мен мобильді құрылғыларды қуаттандырады.</p>',
                        'meta_title' => '1000 Вт тасымалды электр станциясы',
                        'meta_keywords' => 'электр станциясы, аккумулятор, қуат',
                        'meta_desc' => 'Қуаты 1000 Вт мобильді электр станциясы.',
                    ],
                    'en' => [
                        'title' => 'Portable power station',
                        'subtitle' => 'Backup power at home and on trips',
                        'short' => '1000W mobile power station with multiple connector types.',
                        'description' => '<p>A portable power station for backup power of appliances, tools and mobile devices.</p>',
                        'meta_title' => '1000W portable power station',
                        'meta_keywords' => 'power station, battery, backup power',
                        'meta_desc' => 'Portable mobile power station rated at 1000 watts.',
                    ],
                ],
            ],

            [
                'url' => 'professional-tool-set',
                'sku' => 'TOOL-SET-012',
                'vendor_code' => 'TOOLS-108',
                'barcode' => '4870001000127',

                'price' => 129990,
                'old_price' => 149990,
                'purchase_price' => 96000,
                'wholesale_price' => 119990,
                'wholesale_min_quantity' => 4,

                'quantity' => 26,

                'weight' => 9.600,
                'length' => 47.00,
                'width' => 35.00,
                'height' => 11.00,

                'translations' => [
                    'ru' => [
                        'title' => 'Профессиональный набор инструментов',
                        'subtitle' => 'Всё необходимое в одном кейсе',
                        'short' => 'Комплект из 108 предметов для ремонта автомобиля, дома и техники.',
                        'description' => '<p>Набор включает торцевые головки, трещотки, ключи, отвёртки и вспомогательные инструменты.</p>',
                        'meta_title' => 'Профессиональный набор инструментов 108 предметов',
                        'meta_keywords' => 'инструменты, набор инструментов, ремонт',
                        'meta_desc' => 'Профессиональный комплект инструментов из 108 предметов.',
                    ],
                    'kk' => [
                        'title' => 'Кәсіби құралдар жиынтығы',
                        'subtitle' => 'Барлық қажетті құралдар бір қорапта',
                        'short' => 'Автокөлік пен үйді жөндеуге арналған 108 заттан тұратын жиынтық.',
                        'description' => '<p>Жиынтықта бастиектер, кілттер, бұрауыштар және қосымша құралдар бар.</p>',
                        'meta_title' => '108 заттан тұратын кәсіби құралдар жиынтығы',
                        'meta_keywords' => 'құралдар, құралдар жиынтығы, жөндеу',
                        'meta_desc' => '108 заттан тұратын кәсіби құралдар жинағы.',
                    ],
                    'en' => [
                        'title' => 'Professional tool set',
                        'subtitle' => 'Everything required in one case',
                        'short' => '108-piece set for automotive, household and equipment repairs.',
                        'description' => '<p>The set includes sockets, ratchets, wrenches, screwdrivers and auxiliary tools.</p>',
                        'meta_title' => 'Professional 108-piece tool set',
                        'meta_keywords' => 'tools, tool set, repair',
                        'meta_desc' => 'Professional tool kit containing 108 pieces.',
                    ],
                ],
            ],
        ];
    }
}
