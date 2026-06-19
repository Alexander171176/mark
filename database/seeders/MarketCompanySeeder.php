<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketCompany\MarketCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketCompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $companies = $this->companyBlueprints();

            foreach ($companies as $index => $item) {
                $userId = $index + 2;

                $company = MarketCompany::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'url' => $item['url'],
                    ],
                    [
                        'company_type' => $item['company_type'],
                        'bin_iin' => $item['bin_iin'],
                        'legal_name' => $item['legal_name'],
                        'director_name' => $item['director_name'],

                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'website' => $item['website'],

                        'logo' => null,
                        'signature' => null,
                        'stamp' => null,

                        'country' => 'Казахстан',
                        'region' => $item['region'],
                        'city' => $item['city'],
                        'legal_address' => $item['legal_address'],
                        'actual_address' => $item['actual_address'],
                        'latitude' => $item['latitude'],
                        'longitude' => $item['longitude'],

                        'bank_name' => $item['bank_name'],
                        'bank_account' => $item['bank_account'],
                        'bank_account_secondary' => $item['bank_account_secondary'],
                        'bank_bik' => $item['bank_bik'],
                        'bank_iban' => $item['bank_iban'],

                        'vat_enabled' => $item['vat_enabled'],
                        'vat_rate' => $item['vat_enabled'] ? 12 : null,

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

                        'published_at' => Carbon::now()->subDays($index)->toDateString(),
                        'show_from_at' => Carbon::now()->subDays($index)->startOfDay(),
                        'show_to_at' => Carbon::now()->addYear()->endOfDay(),

                        'views' => rand(25, 250),
                    ]
                );

                foreach ($item['translations'] as $locale => $translation) {
                    $company->translations()->updateOrCreate(
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

    protected function companyBlueprints(): array
    {
        return [
            [
                'url' => 'vent-climate-kazakhstan',
                'company_type' => 'company',
                'bin_iin' => '240101000001',
                'legal_name' => 'ТОО «Вент Климат Казахстан»',
                'director_name' => 'Иванов Сергей Петрович',
                'email' => 'info@vent-climate.kz',
                'phone' => '+7 701 100 10 01',
                'website' => 'https://vent-climate.kz',
                'region' => 'Алматинская область',
                'city' => 'Алматы',
                'legal_address' => 'г. Алматы, пр. Абая, 150',
                'actual_address' => 'г. Алматы, ул. Толе би, 85',
                'latitude' => 43.238949,
                'longitude' => 76.889709,
                'bank_name' => 'Halyk Bank',
                'bank_account' => 'KZ111111111111111001',
                'bank_account_secondary' => 'KZ111111111111111002',
                'bank_bik' => 'HSBKKZKX',
                'bank_iban' => 'KZ111111111111111001',
                'vat_enabled' => true,
                'social_links' => [
                    'instagram' => 'https://instagram.com/vent-climate',
                    'whatsapp' => 'https://wa.me/77011001001',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Вент Климат Казахстан',
                        'subtitle' => 'Инженерные решения для вентиляции и климата',
                        'short' => 'Поставка вентиляционного и климатического оборудования.',
                        'description' => 'Компания занимается поставкой вентиляционных установок, кондиционеров и инженерного оборудования для коммерческих объектов.',
                        'meta_title' => 'Вент Климат Казахстан',
                        'meta_keywords' => 'вентиляция, климат, оборудование, Казахстан',
                        'meta_desc' => 'Поставка вентиляционного и климатического оборудования в Казахстане.',
                    ],
                    'en' => [
                        'title' => 'Vent Climate Kazakhstan',
                        'subtitle' => 'Engineering solutions for ventilation and climate',
                        'short' => 'Supplier of ventilation and climate equipment.',
                        'description' => 'The company supplies ventilation units, air conditioners and engineering equipment for commercial buildings.',
                        'meta_title' => 'Vent Climate Kazakhstan',
                        'meta_keywords' => 'ventilation, climate, equipment, Kazakhstan',
                        'meta_desc' => 'Ventilation and climate equipment supplier in Kazakhstan.',
                    ],
                    'kk' => [
                        'title' => 'Вент Климат Қазақстан',
                        'subtitle' => 'Желдету және климатқа арналған инженерлік шешімдер',
                        'short' => 'Желдету және климаттық жабдықтарды жеткізу.',
                        'description' => 'Компания коммерциялық нысандарға арналған желдету қондырғыларын, кондиционерлерді және инженерлік жабдықтарды жеткізеді.',
                        'meta_title' => 'Вент Климат Қазақстан',
                        'meta_keywords' => 'желдету, климат, жабдық, Қазақстан',
                        'meta_desc' => 'Қазақстандағы желдету және климаттық жабдық жеткізушісі.',
                    ],
                ],
            ],
            [
                'url' => 'air-system-group',
                'company_type' => 'company',
                'bin_iin' => '240101000002',
                'legal_name' => 'ТОО «Air System Group»',
                'director_name' => 'Садыков Нурлан Ермекович',
                'email' => 'sales@airsystem.kz',
                'phone' => '+7 701 100 10 02',
                'website' => 'https://airsystem.kz',
                'region' => 'Астана',
                'city' => 'Астана',
                'legal_address' => 'г. Астана, пр. Кабанбай Батыра, 25',
                'actual_address' => 'г. Астана, ул. Сыганак, 10',
                'latitude' => 51.128220,
                'longitude' => 71.430668,
                'bank_name' => 'Kaspi Bank',
                'bank_account' => 'KZ222222222222222001',
                'bank_account_secondary' => 'KZ222222222222222002',
                'bank_bik' => 'CASPKZKA',
                'bank_iban' => 'KZ222222222222222001',
                'vat_enabled' => true,
                'social_links' => [
                    'instagram' => 'https://instagram.com/airsystem',
                    'whatsapp' => 'https://wa.me/77011001002',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Air System Group',
                        'subtitle' => 'Комплексные системы вентиляции',
                        'short' => 'Оборудование для вентиляции и кондиционирования.',
                        'description' => 'Поставщик оборудования для вентиляции, кондиционирования и автоматизации инженерных систем.',
                        'meta_title' => 'Air System Group',
                        'meta_keywords' => 'вентиляция, кондиционирование, автоматика',
                        'meta_desc' => 'Комплексные решения для вентиляции и кондиционирования.',
                    ],
                    'en' => [
                        'title' => 'Air System Group',
                        'subtitle' => 'Integrated ventilation systems',
                        'short' => 'Ventilation and air conditioning equipment.',
                        'description' => 'Supplier of ventilation, air conditioning and automation equipment for engineering systems.',
                        'meta_title' => 'Air System Group',
                        'meta_keywords' => 'ventilation, air conditioning, automation',
                        'meta_desc' => 'Integrated solutions for ventilation and air conditioning.',
                    ],
                    'kk' => [
                        'title' => 'Air System Group',
                        'subtitle' => 'Кешенді желдету жүйелері',
                        'short' => 'Желдету және кондиционерлеу жабдықтары.',
                        'description' => 'Желдету, кондиционерлеу және инженерлік жүйелерді автоматтандыру жабдықтарын жеткізуші.',
                        'meta_title' => 'Air System Group',
                        'meta_keywords' => 'желдету, кондиционерлеу, автоматика',
                        'meta_desc' => 'Желдету және кондиционерлеу үшін кешенді шешімдер.',
                    ],
                ],
            ],
            [
                'url' => 'climate-tech-almaty',
                'company_type' => 'company',
                'bin_iin' => '240101000003',
                'legal_name' => 'ТОО «Climate Tech Almaty»',
                'director_name' => 'Ахметов Тимур Болатович',
                'email' => 'info@climatetech.kz',
                'phone' => '+7 701 100 10 03',
                'website' => 'https://climatetech.kz',
                'region' => 'Алматинская область',
                'city' => 'Алматы',
                'legal_address' => 'г. Алматы, ул. Жандосова, 58',
                'actual_address' => 'г. Алматы, пр. Сейфуллина, 404',
                'latitude' => 43.256670,
                'longitude' => 76.928610,
                'bank_name' => 'ForteBank',
                'bank_account' => 'KZ333333333333333001',
                'bank_account_secondary' => 'KZ333333333333333002',
                'bank_bik' => 'IRTYKZKA',
                'bank_iban' => 'KZ333333333333333001',
                'vat_enabled' => false,
                'social_links' => [
                    'instagram' => 'https://instagram.com/climatetech',
                    'whatsapp' => 'https://wa.me/77011001003',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Climate Tech Almaty',
                        'subtitle' => 'Климатическое оборудование для бизнеса',
                        'short' => 'Поставка климатического оборудования и комплектующих.',
                        'description' => 'Компания поставляет климатическое оборудование, комплектующие, автоматику и расходные материалы для инженерных систем.',
                        'meta_title' => 'Climate Tech Almaty',
                        'meta_keywords' => 'климатическое оборудование, автоматика, комплектующие',
                        'meta_desc' => 'Поставка климатического оборудования и комплектующих в Алматы.',
                    ],
                    'en' => [
                        'title' => 'Climate Tech Almaty',
                        'subtitle' => 'Climate equipment for business',
                        'short' => 'Supplier of climate equipment and components.',
                        'description' => 'The company supplies climate equipment, components, automation and consumables for engineering systems.',
                        'meta_title' => 'Climate Tech Almaty',
                        'meta_keywords' => 'climate equipment, automation, components',
                        'meta_desc' => 'Climate equipment and components supplier in Almaty.',
                    ],
                    'kk' => [
                        'title' => 'Climate Tech Almaty',
                        'subtitle' => 'Бизнеске арналған климаттық жабдық',
                        'short' => 'Климаттық жабдықтар мен қосалқы бөлшектерді жеткізу.',
                        'description' => 'Компания инженерлік жүйелерге арналған климаттық жабдықтарды, қосалқы бөлшектерді, автоматиканы және шығын материалдарын жеткізеді.',
                        'meta_title' => 'Climate Tech Almaty',
                        'meta_keywords' => 'климаттық жабдық, автоматика, қосалқы бөлшектер',
                        'meta_desc' => 'Алматыдағы климаттық жабдықтар мен қосалқы бөлшектер жеткізушісі.',
                    ],
                ],
            ],
            [
                'url' => 'electro-market-kz',
                'company_type' => 'company',
                'bin_iin' => '240101000004',
                'legal_name' => 'ТОО «Electro Market KZ»',
                'director_name' => 'Кузнецов Андрей Викторович',
                'email' => 'sales@electromarket.kz',
                'phone' => '+7 701 100 10 04',
                'website' => 'https://electromarket.kz',
                'region' => 'Карагандинская область',
                'city' => 'Караганда',
                'legal_address' => 'г. Караганда, пр. Бухар Жырау, 45',
                'actual_address' => 'г. Караганда, ул. Ермекова, 61',
                'latitude' => 49.804683,
                'longitude' => 73.109383,
                'bank_name' => 'Bank CenterCredit',
                'bank_account' => 'KZ444444444444444001',
                'bank_account_secondary' => 'KZ444444444444444002',
                'bank_bik' => 'KCJBKZKX',
                'bank_iban' => 'KZ444444444444444001',
                'vat_enabled' => true,
                'social_links' => [
                    'instagram' => 'https://instagram.com/electromarketkz',
                    'whatsapp' => 'https://wa.me/77011001004',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Electro Market KZ',
                        'subtitle' => 'Электрика и инженерные комплектующие',
                        'short' => 'Поставщик электротехнической продукции.',
                        'description' => 'Компания занимается поставкой кабельной продукции, электрощитов, автоматики и комплектующих для промышленных и коммерческих объектов.',
                        'meta_title' => 'Electro Market KZ',
                        'meta_keywords' => 'электрика, кабель, автоматика, электрощиты',
                        'meta_desc' => 'Поставщик электротехнической продукции в Казахстане.',
                    ],
                    'en' => [
                        'title' => 'Electro Market KZ',
                        'subtitle' => 'Electrical and engineering components',
                        'short' => 'Supplier of electrical products.',
                        'description' => 'The company supplies cables, electrical panels, automation and components for industrial and commercial facilities.',
                        'meta_title' => 'Electro Market KZ',
                        'meta_keywords' => 'electrical products, cable, automation, panels',
                        'meta_desc' => 'Electrical products supplier in Kazakhstan.',
                    ],
                    'kk' => [
                        'title' => 'Electro Market KZ',
                        'subtitle' => 'Электрика және инженерлік бөлшектер',
                        'short' => 'Электротехникалық өнімдерді жеткізуші.',
                        'description' => 'Компания өнеркәсіптік және коммерциялық нысандарға арналған кабель өнімдерін, электр қалқандарын, автоматиканы және бөлшектерді жеткізеді.',
                        'meta_title' => 'Electro Market KZ',
                        'meta_keywords' => 'электрика, кабель, автоматика, электр қалқандары',
                        'meta_desc' => 'Қазақстандағы электротехникалық өнім жеткізушісі.',
                    ],
                ],
            ],
            [
                'url' => 'smart-building-solutions',
                'company_type' => 'company',
                'bin_iin' => '240101000005',
                'legal_name' => 'ТОО «Smart Building Solutions»',
                'director_name' => 'Омарова Алия Нурлановна',
                'email' => 'hello@smartbuilding.kz',
                'phone' => '+7 701 100 10 05',
                'website' => 'https://smartbuilding.kz',
                'region' => 'Астана',
                'city' => 'Астана',
                'legal_address' => 'г. Астана, ул. Достык, 12',
                'actual_address' => 'г. Астана, пр. Мәңгілік Ел, 55',
                'latitude' => 51.090700,
                'longitude' => 71.418900,
                'bank_name' => 'Jusan Bank',
                'bank_account' => 'KZ555555555555555001',
                'bank_account_secondary' => 'KZ555555555555555002',
                'bank_bik' => 'TSESKZKA',
                'bank_iban' => 'KZ555555555555555001',
                'vat_enabled' => false,
                'social_links' => [
                    'instagram' => 'https://instagram.com/smartbuildingkz',
                    'whatsapp' => 'https://wa.me/77011001005',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Smart Building Solutions',
                        'subtitle' => 'Автоматизация зданий и инженерных систем',
                        'short' => 'Решения для умных зданий и автоматизации.',
                        'description' => 'Компания поставляет оборудование для автоматизации зданий, диспетчеризации, мониторинга и управления инженерными системами.',
                        'meta_title' => 'Smart Building Solutions',
                        'meta_keywords' => 'умное здание, автоматизация, диспетчеризация',
                        'meta_desc' => 'Оборудование для автоматизации зданий и инженерных систем.',
                    ],
                    'en' => [
                        'title' => 'Smart Building Solutions',
                        'subtitle' => 'Building and engineering systems automation',
                        'short' => 'Solutions for smart buildings and automation.',
                        'description' => 'The company supplies equipment for building automation, dispatching, monitoring and engineering systems control.',
                        'meta_title' => 'Smart Building Solutions',
                        'meta_keywords' => 'smart building, automation, monitoring',
                        'meta_desc' => 'Equipment for building automation and engineering systems.',
                    ],
                    'kk' => [
                        'title' => 'Smart Building Solutions',
                        'subtitle' => 'Ғимараттар мен инженерлік жүйелерді автоматтандыру',
                        'short' => 'Ақылды ғимараттар мен автоматтандыру шешімдері.',
                        'description' => 'Компания ғимараттарды автоматтандыруға, диспетчерлеуге, мониторингке және инженерлік жүйелерді басқаруға арналған жабдықтарды жеткізеді.',
                        'meta_title' => 'Smart Building Solutions',
                        'meta_keywords' => 'ақылды ғимарат, автоматтандыру, мониторинг',
                        'meta_desc' => 'Ғимараттарды және инженерлік жүйелерді автоматтандыруға арналған жабдық.',
                    ],
                ],
            ],
            [
                'url' => 'industrial-supply-kz',
                'company_type' => 'company',
                'bin_iin' => '240101000006',
                'legal_name' => 'ТОО «Industrial Supply KZ»',
                'director_name' => 'Мухамедьяров Руслан Асхатович',
                'email' => 'order@industrialsupply.kz',
                'phone' => '+7 701 100 10 06',
                'website' => 'https://industrialsupply.kz',
                'region' => 'Павлодарская область',
                'city' => 'Павлодар',
                'legal_address' => 'г. Павлодар, ул. Ломова, 140',
                'actual_address' => 'г. Павлодар, Северная промзона, склад 7',
                'latitude' => 52.287054,
                'longitude' => 76.967402,
                'bank_name' => 'Eurasian Bank',
                'bank_account' => 'KZ666666666666666001',
                'bank_account_secondary' => 'KZ666666666666666002',
                'bank_bik' => 'EURIKZKA',
                'bank_iban' => 'KZ666666666666666001',
                'vat_enabled' => true,
                'social_links' => [
                    'instagram' => 'https://instagram.com/industrialsupplykz',
                    'whatsapp' => 'https://wa.me/77011001006',
                ],
                'translations' => [
                    'ru' => [
                        'title' => 'Industrial Supply KZ',
                        'subtitle' => 'Промышленное оборудование и снабжение',
                        'short' => 'Поставщик промышленного оборудования и комплектующих.',
                        'description' => 'Компания поставляет промышленное оборудование, инженерные комплектующие, расходные материалы и решения для производственных предприятий.',
                        'meta_title' => 'Industrial Supply KZ',
                        'meta_keywords' => 'промышленное оборудование, снабжение, комплектующие',
                        'meta_desc' => 'Поставка промышленного оборудования и комплектующих в Казахстане.',
                    ],
                    'en' => [
                        'title' => 'Industrial Supply KZ',
                        'subtitle' => 'Industrial equipment and supply',
                        'short' => 'Supplier of industrial equipment and components.',
                        'description' => 'The company supplies industrial equipment, engineering components, consumables and solutions for manufacturing enterprises.',
                        'meta_title' => 'Industrial Supply KZ',
                        'meta_keywords' => 'industrial equipment, supply, components',
                        'meta_desc' => 'Industrial equipment and components supplier in Kazakhstan.',
                    ],
                    'kk' => [
                        'title' => 'Industrial Supply KZ',
                        'subtitle' => 'Өнеркәсіптік жабдық және жабдықтау',
                        'short' => 'Өнеркәсіптік жабдықтар мен бөлшектерді жеткізуші.',
                        'description' => 'Компания өндірістік кәсіпорындарға арналған өнеркәсіптік жабдықтарды, инженерлік бөлшектерді, шығын материалдарын және шешімдерді жеткізеді.',
                        'meta_title' => 'Industrial Supply KZ',
                        'meta_keywords' => 'өнеркәсіптік жабдық, жабдықтау, бөлшектер',
                        'meta_desc' => 'Қазақстандағы өнеркәсіптік жабдықтар мен бөлшектер жеткізушісі.',
                    ],
                ],
            ],
        ];
    }
}
