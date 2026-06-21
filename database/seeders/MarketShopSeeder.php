<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketShop\MarketShop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketShopSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $companies = MarketCompany::query()
                ->with('translations')
                ->orderBy('id')
                ->get();

            foreach ($companies as $index => $company) {
                $item = $this->shopBlueprint($company, $index);

                $shop = MarketShop::updateOrCreate(
                    [
                        'market_company_id' => $company->id,
                    ],
                    [
                        'user_id' => $company->user_id,

                        'url' => $item['url'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'logo' => null,

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

                        'views' => rand(15, 180),
                    ]
                );

                foreach ($item['translations'] as $locale => $translation) {
                    $shop->translations()->updateOrCreate(
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

    protected function shopBlueprint(MarketCompany $company, int $index): array
    {
        $companyTitleRu = $company->translations->firstWhere('locale', 'ru')?->title
            ?? $company->legal_name
            ?? 'Магазин компании';

        $companyTitleEn = $company->translations->firstWhere('locale', 'en')?->title
            ?? $companyTitleRu;

        $companyTitleKk = $company->translations->firstWhere('locale', 'kk')?->title
            ?? $companyTitleRu;

        return [
            'url' => $company->url . '-shop',
            'email' => $company->email,
            'phone' => $company->phone,
            'social_links' => $company->social_links ?? [],

            'translations' => [
                'ru' => [
                    'title' => $companyTitleRu . ' — магазин',
                    'subtitle' => 'Официальная витрина компании на маркетплейсе',
                    'short' => 'Каталог товаров и решений компании ' . $companyTitleRu . '.',
                    'description' => 'Магазин компании ' . $companyTitleRu . ' на маркетплейсе предлагает товары, оборудование, комплектующие и инженерные решения для клиентов платформы.',
                    'meta_title' => $companyTitleRu . ' — магазин',
                    'meta_keywords' => 'магазин, маркетплейс, товары, оборудование, ' . $companyTitleRu,
                    'meta_desc' => 'Официальный магазин компании ' . $companyTitleRu . ' на маркетплейсе.',
                ],

                'en' => [
                    'title' => $companyTitleEn . ' — shop',
                    'subtitle' => 'Official company storefront on the marketplace',
                    'short' => 'Product catalog and solutions from ' . $companyTitleEn . '.',
                    'description' => 'The shop of ' . $companyTitleEn . ' on the marketplace offers products, equipment, components and engineering solutions for platform customers.',
                    'meta_title' => $companyTitleEn . ' — shop',
                    'meta_keywords' => 'shop, marketplace, products, equipment, ' . $companyTitleEn,
                    'meta_desc' => 'Official marketplace shop of ' . $companyTitleEn . '.',
                ],

                'kk' => [
                    'title' => $companyTitleKk . ' — дүкен',
                    'subtitle' => 'Маркетплейстегі компанияның ресми витринасы',
                    'short' => $companyTitleKk . ' компаниясының тауарлары мен шешімдер каталогы.',
                    'description' => $companyTitleKk . ' компаниясының маркетплейстегі дүкені платформа клиенттеріне тауарлар, жабдықтар, бөлшектер және инженерлік шешімдер ұсынады.',
                    'meta_title' => $companyTitleKk . ' — дүкен',
                    'meta_keywords' => 'дүкен, маркетплейс, тауарлар, жабдық, ' . $companyTitleKk,
                    'meta_desc' => $companyTitleKk . ' компаниясының маркетплейстегі ресми дүкені.',
                ],
            ],
        ];
    }
}
