<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketTag\MarketTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketTagSeeder extends Seeder
{
    /**
     * Запуск сидера.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->tags() as $index => $item) {
                $tag = MarketTag::updateOrCreate(
                    [
                        'url' => $item['url'],
                    ],
                    [
                        'user_id' => 1,

                        'icon' => null,
                        'color' => $item['color'],

                        'sort' => $index,
                        'activity' => true,

                        'status' => 'published',
                        'moderation_status' => 1,
                        'moderated_by' => 1,
                        'moderated_at' => now(),
                        'moderation_note' => null,

                        'published_at' => Carbon::now()->subDays($index),
                        'show_from_at' => Carbon::now()->subDays($index)->startOfDay(),
                        'show_to_at' => Carbon::now()->addYear()->endOfDay(),

                        'views' => rand(10, 180),
                    ]
                );

                foreach ($item['translations'] as $locale => $translation) {
                    $tag->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'title' => $translation['title'],
                            'subtitle' => $translation['subtitle'],
                            'short' => $translation['short'],
                            'description' => $translation['description'],
                            'meta_title' => $translation['meta_title'],
                            'meta_keywords' => $translation['meta_keywords'],
                            'meta_desc' => $translation['meta_desc'],
                        ]
                    );
                }
            }
        });
    }

    /**
     * Данные тегов.
     */
    protected function tags(): array
    {
        return [
            $this->tagBlueprint('Новинка', 'new', '#22c55e', 'New', 'Жаңа'),
            $this->tagBlueprint('Хит продаж', 'bestseller', '#f97316', 'Bestseller', 'Үздік сатылым'),
            $this->tagBlueprint('Скидка', 'discount', '#ef4444', 'Discount', 'Жеңілдік'),
            $this->tagBlueprint('Рекомендуем', 'recommended', '#3b82f6', 'Recommended', 'Ұсынамыз'),
            $this->tagBlueprint('Премиум', 'premium', '#a855f7', 'Premium', 'Премиум'),
            $this->tagBlueprint('Эконом', 'economy', '#14b8a6', 'Economy', 'Үнемді'),
            $this->tagBlueprint('Профессиональный', 'professional', '#0f172a', 'Professional', 'Кәсіби'),
            $this->tagBlueprint('Для дома', 'for-home', '#84cc16', 'For home', 'Үйге арналған'),
            $this->tagBlueprint('Для бизнеса', 'for-business', '#06b6d4', 'For business', 'Бизнеске арналған'),
            $this->tagBlueprint('Сертифицировано', 'certified', '#10b981', 'Certified', 'Сертификатталған'),
            $this->tagBlueprint('В наличии', 'in-stock', '#16a34a', 'In stock', 'Қоймада бар'),
            $this->tagBlueprint('Под заказ', 'on-order', '#eab308', 'On order', 'Тапсырыспен'),
            $this->tagBlueprint('Быстрая доставка', 'fast-delivery', '#0284c7', 'Fast delivery', 'Жылдам жеткізу'),
            $this->tagBlueprint('Гарантия', 'warranty', '#6366f1', 'Warranty', 'Кепілдік'),
            $this->tagBlueprint('Комплект', 'kit', '#64748b', 'Kit', 'Жинақ'),
            $this->tagBlueprint('Популярное', 'popular', '#ec4899', 'Popular', 'Танымал'),
        ];
    }

    /**
     * Шаблон тега.
     */
    protected function tagBlueprint(
        string $titleRu,
        ?string $url = null,
        ?string $color = null,
        ?string $titleEn = null,
        ?string $titleKk = null
    ): array {
        $slug = $url ?: Str::slug($titleRu);

        $titleEn = $titleEn ?: $titleRu;
        $titleKk = $titleKk ?: $titleRu;

        return [
            'url' => $slug,
            'color' => $color,

            'translations' => [
                'ru' => [
                    'title' => $titleRu,
                    'subtitle' => 'Тег товаров маркетплейса',
                    'short' => 'Тег "' . $titleRu . '" помогает быстро находить подходящие товары.',
                    'description' => 'Тег "' . $titleRu . '" используется в маркетплейсе для группировки, фильтрации и удобного поиска товаров по важным признакам.',
                    'meta_title' => $titleRu . ' — тег товаров',
                    'meta_keywords' => $titleRu . ', тег, товары, маркетплейс, фильтр',
                    'meta_desc' => 'Товары маркетплейса с тегом "' . $titleRu . '".',
                ],

                'en' => [
                    'title' => $titleEn,
                    'subtitle' => 'Marketplace product tag',
                    'short' => 'The "' . $titleEn . '" tag helps customers quickly find relevant products.',
                    'description' => 'The "' . $titleEn . '" tag is used in the marketplace for grouping, filtering and convenient product search by important attributes.',
                    'meta_title' => $titleEn . ' — product tag',
                    'meta_keywords' => $titleEn . ', tag, products, marketplace, filter',
                    'meta_desc' => 'Marketplace products with the "' . $titleEn . '" tag.',
                ],

                'kk' => [
                    'title' => $titleKk,
                    'subtitle' => 'Маркетплейстегі тауар тегі',
                    'short' => '"' . $titleKk . '" тегі қажетті тауарларды жылдам табуға көмектеседі.',
                    'description' => '"' . $titleKk . '" тегі маркетплейсте тауарларды топтастыру, сүзу және маңызды белгілер бойынша іздеу үшін қолданылады.',
                    'meta_title' => $titleKk . ' — тауар тегі',
                    'meta_keywords' => $titleKk . ', тег, тауарлар, маркетплейс, сүзгі',
                    'meta_desc' => '"' . $titleKk . '" тегі бар маркетплейс тауарлары.',
                ],
            ],
        ];
    }
}
