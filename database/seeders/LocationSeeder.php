<?php

namespace Database\Seeders;

use App\Models\Admin\System\Location\Location;
use App\Models\Admin\System\Location\LocationTranslation;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Казахстан.
         */
        $kazakhstan = Location::query()->updateOrCreate(
            [
                'slug' => 'kazakhstan',
            ],
            [
                'parent_id' => null,
                'type' => 'country',
                'code' => 'KZ',
                'latitude' => 48.0196,
                'longitude' => 66.9237,
                'timezone' => 'Asia/Almaty',
                'activity' => true,
                'is_default' => false,
                'sort' => 1,
            ]
        );

        $this->translations(
            $kazakhstan,
            [
                'ru' => [
                    'title' => 'Казахстан',
                    'title_in' => 'Казахстане',
                    'title_from' => 'Казахстана',
                    'short' => 'Республика Казахстан',
                    'description' => null,
                    'meta_title' => 'Казахстан',
                    'meta_keywords' => 'Казахстан',
                    'meta_desc' => 'Информация для пользователей из Казахстана.',
                ],
                'en' => [
                    'title' => 'Kazakhstan',
                    'title_in' => 'Kazakhstan',
                    'title_from' => 'Kazakhstan',
                    'short' => 'Republic of Kazakhstan',
                    'description' => null,
                    'meta_title' => 'Kazakhstan',
                    'meta_keywords' => 'Kazakhstan',
                    'meta_desc' => 'Information for users from Kazakhstan.',
                ],
                'kk' => [
                    'title' => 'Қазақстан',
                    'title_in' => 'Қазақстанда',
                    'title_from' => 'Қазақстаннан',
                    'short' => 'Қазақстан Республикасы',
                    'description' => null,
                    'meta_title' => 'Қазақстан',
                    'meta_keywords' => 'Қазақстан',
                    'meta_desc' => 'Қазақстандағы пайдаланушыларға арналған ақпарат.',
                ],
            ]
        );

        /**
         * Города Казахстана.
         */
        $cities = [
            [
                'slug' => 'astana',
                'code' => 'KZ-AST',
                'latitude' => 51.1694,
                'longitude' => 71.4491,
                'is_default' => true,
                'translations' => [
                    'ru' => ['Астана', 'Астане', 'Астаны'],
                    'en' => ['Astana', 'Astana', 'Astana'],
                    'kk' => ['Астана', 'Астанада', 'Астанадан'],
                ],
            ],
            [
                'slug' => 'almaty',
                'code' => 'KZ-ALA',
                'latitude' => 43.2389,
                'longitude' => 76.8897,
                'translations' => [
                    'ru' => ['Алматы', 'Алматы', 'Алматы'],
                    'en' => ['Almaty', 'Almaty', 'Almaty'],
                    'kk' => ['Алматы', 'Алматыда', 'Алматыдан'],
                ],
            ],
            [
                'slug' => 'shymkent',
                'code' => 'KZ-SHY',
                'latitude' => 42.3417,
                'longitude' => 69.5901,
                'translations' => [
                    'ru' => ['Шымкент', 'Шымкенте', 'Шымкента'],
                    'en' => ['Shymkent', 'Shymkent', 'Shymkent'],
                    'kk' => ['Шымкент', 'Шымкентте', 'Шымкенттен'],
                ],
            ],
            [
                'slug' => 'karaganda',
                'code' => 'KZ-KRG',
                'latitude' => 49.8064,
                'longitude' => 73.0855,
                'translations' => [
                    'ru' => ['Караганда', 'Караганде', 'Караганды'],
                    'en' => ['Karaganda', 'Karaganda', 'Karaganda'],
                    'kk' => ['Қарағанды', 'Қарағандыда', 'Қарағандыдан'],
                ],
            ],
            [
                'slug' => 'aktobe',
                'code' => 'KZ-AKT',
                'latitude' => 50.2839,
                'longitude' => 57.1669,
                'translations' => [
                    'ru' => ['Актобе', 'Актобе', 'Актобе'],
                    'en' => ['Aktobe', 'Aktobe', 'Aktobe'],
                    'kk' => ['Ақтөбе', 'Ақтөбеде', 'Ақтөбеден'],
                ],
            ],
            [
                'slug' => 'taraz',
                'code' => 'KZ-TAR',
                'latitude' => 42.9000,
                'longitude' => 71.3667,
                'translations' => [
                    'ru' => ['Тараз', 'Таразе', 'Тараза'],
                    'en' => ['Taraz', 'Taraz', 'Taraz'],
                    'kk' => ['Тараз', 'Таразда', 'Тараздан'],
                ],
            ],
            [
                'slug' => 'pavlodar',
                'code' => 'KZ-PAV',
                'latitude' => 52.2873,
                'longitude' => 76.9674,
                'translations' => [
                    'ru' => ['Павлодар', 'Павлодаре', 'Павлодара'],
                    'en' => ['Pavlodar', 'Pavlodar', 'Pavlodar'],
                    'kk' => ['Павлодар', 'Павлодарда', 'Павлодардан'],
                ],
            ],
            [
                'slug' => 'ust-kamenogorsk',
                'code' => 'KZ-UKG',
                'latitude' => 49.9483,
                'longitude' => 82.6275,
                'translations' => [
                    'ru' => ['Усть-Каменогорск', 'Усть-Каменогорске', 'Усть-Каменогорска'],
                    'en' => ['Oskemen', 'Oskemen', 'Oskemen'],
                    'kk' => ['Өскемен', 'Өскеменде', 'Өскеменнен'],
                ],
            ],
            [
                'slug' => 'semey',
                'code' => 'KZ-SEM',
                'latitude' => 50.4111,
                'longitude' => 80.2275,
                'translations' => [
                    'ru' => ['Семей', 'Семее', 'Семея'],
                    'en' => ['Semey', 'Semey', 'Semey'],
                    'kk' => ['Семей', 'Семейде', 'Семейден'],
                ],
            ],
            [
                'slug' => 'atyrau',
                'code' => 'KZ-ATY',
                'latitude' => 47.0945,
                'longitude' => 51.9238,
                'translations' => [
                    'ru' => ['Атырау', 'Атырау', 'Атырау'],
                    'en' => ['Atyrau', 'Atyrau', 'Atyrau'],
                    'kk' => ['Атырау', 'Атырауда', 'Атыраудан'],
                ],
            ],
            [
                'slug' => 'kostanay',
                'code' => 'KZ-KOS',
                'latitude' => 53.2144,
                'longitude' => 63.6246,
                'translations' => [
                    'ru' => ['Костанай', 'Костанае', 'Костаная'],
                    'en' => ['Kostanay', 'Kostanay', 'Kostanay'],
                    'kk' => ['Қостанай', 'Қостанайда', 'Қостанайдан'],
                ],
            ],
            [
                'slug' => 'kyzylorda',
                'code' => 'KZ-KYZ',
                'latitude' => 44.8488,
                'longitude' => 65.4823,
                'translations' => [
                    'ru' => ['Кызылорда', 'Кызылорде', 'Кызылорды'],
                    'en' => ['Kyzylorda', 'Kyzylorda', 'Kyzylorda'],
                    'kk' => ['Қызылорда', 'Қызылордада', 'Қызылордадан'],
                ],
            ],
            [
                'slug' => 'uralsk',
                'code' => 'KZ-URA',
                'latitude' => 51.2278,
                'longitude' => 51.3865,
                'translations' => [
                    'ru' => ['Уральск', 'Уральске', 'Уральска'],
                    'en' => ['Oral', 'Oral', 'Oral'],
                    'kk' => ['Орал', 'Оралда', 'Оралдан'],
                ],
            ],
            [
                'slug' => 'petropavl',
                'code' => 'KZ-PET',
                'latitude' => 54.8753,
                'longitude' => 69.1628,
                'translations' => [
                    'ru' => ['Петропавловск', 'Петропавловске', 'Петропавловска'],
                    'en' => ['Petropavl', 'Petropavl', 'Petropavl'],
                    'kk' => ['Петропавл', 'Петропавлда', 'Петропавлдан'],
                ],
            ],
            [
                'slug' => 'aktau',
                'code' => 'KZ-AKU',
                'latitude' => 43.6532,
                'longitude' => 51.1975,
                'translations' => [
                    'ru' => ['Актау', 'Актау', 'Актау'],
                    'en' => ['Aktau', 'Aktau', 'Aktau'],
                    'kk' => ['Ақтау', 'Ақтауда', 'Ақтаудан'],
                ],
            ],
            [
                'slug' => 'turkistan',
                'code' => 'KZ-TUR',
                'latitude' => 43.2973,
                'longitude' => 68.2518,
                'translations' => [
                    'ru' => ['Туркестан', 'Туркестане', 'Туркестана'],
                    'en' => ['Turkistan', 'Turkistan', 'Turkistan'],
                    'kk' => ['Түркістан', 'Түркістанда', 'Түркістаннан'],
                ],
            ],
            [
                'slug' => 'taldykorgan',
                'code' => 'KZ-TDK',
                'latitude' => 45.0156,
                'longitude' => 78.3739,
                'translations' => [
                    'ru' => ['Талдыкорган', 'Талдыкоргане', 'Талдыкоргана'],
                    'en' => ['Taldykorgan', 'Taldykorgan', 'Taldykorgan'],
                    'kk' => ['Талдықорған', 'Талдықорғанда', 'Талдықорғаннан'],
                ],
            ],
            [
                'slug' => 'kokshetau',
                'code' => 'KZ-KOK',
                'latitude' => 53.2833,
                'longitude' => 69.3833,
                'translations' => [
                    'ru' => ['Кокшетау', 'Кокшетау', 'Кокшетау'],
                    'en' => ['Kokshetau', 'Kokshetau', 'Kokshetau'],
                    'kk' => ['Көкшетау', 'Көкшетауда', 'Көкшетаудан'],
                ],
            ],
            [
                'slug' => 'konaev',
                'code' => 'KZ-KON',
                'latitude' => 43.8833,
                'longitude' => 77.0833,
                'translations' => [
                    'ru' => ['Конаев', 'Конаеве', 'Конаева'],
                    'en' => ['Konaev', 'Konaev', 'Konaev'],
                    'kk' => ['Қонаев', 'Қонаевта', 'Қонаевтан'],
                ],
            ],
            [
                'slug' => 'zhezkazgan',
                'code' => 'KZ-ZHE',
                'latitude' => 47.7833,
                'longitude' => 67.7667,
                'translations' => [
                    'ru' => ['Жезказган', 'Жезказгане', 'Жезказгана'],
                    'en' => ['Zhezkazgan', 'Zhezkazgan', 'Zhezkazgan'],
                    'kk' => ['Жезқазған', 'Жезқазғанда', 'Жезқазғаннан'],
                ],
            ],
            [
                'slug' => 'ekibastuz',
                'code' => 'KZ-EKB',
                'latitude' => 51.7298,
                'longitude' => 75.3266,
                'translations' => [
                    'ru' => ['Экибастуз', 'Экибастузе', 'Экибастуза'],
                    'en' => ['Ekibastuz', 'Ekibastuz', 'Ekibastuz'],
                    'kk' => ['Екібастұз', 'Екібастұзда', 'Екібастұздан'],
                ],
            ],
            [
                'slug' => 'temirtau',
                'code' => 'KZ-TEM',
                'latitude' => 50.0549,
                'longitude' => 72.9646,
                'translations' => [
                    'ru' => ['Темиртау', 'Темиртау', 'Темиртау'],
                    'en' => ['Temirtau', 'Temirtau', 'Temirtau'],
                    'kk' => ['Теміртау', 'Теміртауда', 'Теміртаудан'],
                ],
            ],
            [
                'slug' => 'rudny',
                'code' => 'KZ-RUD',
                'latitude' => 52.9729,
                'longitude' => 63.1168,
                'translations' => [
                    'ru' => ['Рудный', 'Рудном', 'Рудного'],
                    'en' => ['Rudny', 'Rudny', 'Rudny'],
                    'kk' => ['Рудный', 'Рудныйда', 'Рудныйдан'],
                ],
            ],
            [
                'slug' => 'balkhash',
                'code' => 'KZ-BAL',
                'latitude' => 46.8481,
                'longitude' => 74.9950,
                'translations' => [
                    'ru' => ['Балхаш', 'Балхаше', 'Балхаша'],
                    'en' => ['Balkhash', 'Balkhash', 'Balkhash'],
                    'kk' => ['Балқаш', 'Балқашта', 'Балқаштан'],
                ],
            ],
        ];

        foreach ($cities as $index => $data) {
            $location = Location::query()->updateOrCreate(
                [
                    'slug' => $data['slug'],
                ],
                [
                    'parent_id' => $kazakhstan->id,
                    'type' => 'city',
                    'code' => $data['code'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'timezone' => 'Asia/Almaty',
                    'activity' => true,
                    'is_default' => $data['is_default'] ?? false,
                    'sort' => $index + 10,
                ]
            );

            $translations = [];

            foreach ($data['translations'] as $locale => $translation) {
                $translations[$locale] = [
                    'title' => $translation[0],
                    'title_in' => $translation[1],
                    'title_from' => $translation[2],
                    'short' => null,
                    'description' => null,
                    'meta_title' => $translation[0],
                    'meta_keywords' => $translation[0],
                    'meta_desc' => null,
                ];
            }

            $this->translations(
                $location,
                $translations
            );
        }
    }

    /**
     * Создание или обновление переводов локации.
     */
    private function translations(
        Location $location,
        array $translations
    ): void {
        foreach ($translations as $locale => $translation) {
            LocationTranslation::query()->updateOrCreate(
                [
                    'location_id' => $location->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'],
                    'title_in' => $translation['title_in'] ?? null,
                    'title_from' => $translation['title_from'] ?? null,
                    'short' => $translation['short'] ?? null,
                    'description' => $translation['description'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_keywords' => $translation['meta_keywords'] ?? null,
                    'meta_desc' => $translation['meta_desc'] ?? null,
                ]
            );
        }
    }
}
