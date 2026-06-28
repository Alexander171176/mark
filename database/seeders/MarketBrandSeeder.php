<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketBrand\MarketBrand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketBrandSeeder extends Seeder
{
    /**
     * Запуск сидера.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->brands() as $index => $item) {
                $brand = MarketBrand::updateOrCreate(
                    [
                        'url' => $item['url'],
                    ],
                    [
                        'user_id' => 1,

                        'website' => $item['website'],
                        'logo' => null,
                        'icon' => null,
                        'social_links' => $item['social_links'],

                        'sort' => $index,
                        'activity' => true,

                        'left' => $index % 3 === 0,
                        'main' => $index % 2 === 0,
                        'right' => $index % 3 === 1,

                        'status' => 'published',
                        'moderation_status' => 1,
                        'moderated_by' => 1,
                        'moderated_at' => now(),
                        'moderation_note' => null,

                        'published_at' => Carbon::now()->subDays($index),
                        'show_from_at' => Carbon::now()->subDays($index)->startOfDay(),
                        'show_to_at' => Carbon::now()->addYear()->endOfDay(),

                        'views' => rand(20, 250),
                    ]
                );

                foreach ($item['translations'] as $locale => $translation) {
                    $brand->translations()->updateOrCreate(
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
     * Данные брендов.
     */
    protected function brands(): array
    {
        return [
            $this->brandBlueprint('Bosch', 'bosch', 'https://www.bosch.com'),
            $this->brandBlueprint('Makita', 'makita', 'https://www.makita.com'),
            $this->brandBlueprint('Siemens', 'siemens', 'https://www.siemens.com'),
            $this->brandBlueprint('Schneider Electric', 'schneider-electric', 'https://www.se.com'),
            $this->brandBlueprint('ABB', 'abb', 'https://global.abb'),
            $this->brandBlueprint('DeWalt', 'dewalt', 'https://www.dewalt.com'),
            $this->brandBlueprint('Hyundai', 'hyundai', 'https://www.hyundai.com'),
            $this->brandBlueprint('Hikvision', 'hikvision', 'https://www.hikvision.com'),
        ];
    }

    /**
     * Шаблон бренда.
     */
    protected function brandBlueprint(
        string $name,
        ?string $url = null,
        ?string $website = null
    ): array {
        $slug = $url ?: Str::slug($name);

        return [
            'url' => $slug,
            'website' => $website,
            'social_links' => [],

            'translations' => [
                'ru' => [
                    'title' => $name,
                    'subtitle' => 'Официальный бренд на маркетплейсе',
                    'short' => 'Товары и решения бренда ' . $name . ' для клиентов маркетплейса.',
                    'description' => 'Бренд ' . $name . ' представлен на маркетплейсе товарами, оборудованием, комплектующими и решениями для профессионального и бытового применения.',
                    'meta_title' => $name . ' — бренд товаров',
                    'meta_keywords' => $name . ', бренд, товары, оборудование, маркетплейс',
                    'meta_desc' => 'Каталог товаров и решений бренда ' . $name . ' на маркетплейсе.',
                ],

                'en' => [
                    'title' => $name,
                    'subtitle' => 'Official brand on the marketplace',
                    'short' => 'Products and solutions from ' . $name . ' for marketplace customers.',
                    'description' => 'The ' . $name . ' brand is represented on the marketplace with products, equipment, components and solutions for professional and everyday use.',
                    'meta_title' => $name . ' — product brand',
                    'meta_keywords' => $name . ', brand, products, equipment, marketplace',
                    'meta_desc' => 'Catalog of products and solutions from the ' . $name . ' brand on the marketplace.',
                ],

                'kk' => [
                    'title' => $name,
                    'subtitle' => 'Маркетплейстегі ресми бренд',
                    'short' => $name . ' брендінің маркетплейс клиенттеріне арналған тауарлары мен шешімдері.',
                    'description' => $name . ' бренді маркетплейсте кәсіби және тұрмыстық қолдануға арналған тауарлармен, жабдықтармен, бөлшектермен және шешімдермен ұсынылған.',
                    'meta_title' => $name . ' — тауар бренді',
                    'meta_keywords' => $name . ', бренд, тауарлар, жабдық, маркетплейс',
                    'meta_desc' => $name . ' брендінің маркетплейстегі тауарлары мен шешімдер каталогы.',
                ],
            ],
        ];
    }
}
