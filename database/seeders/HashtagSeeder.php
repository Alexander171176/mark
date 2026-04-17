<?php

namespace Database\Seeders;

use App\Models\Admin\School\Hashtag\Hashtag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HashtagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // Явно фиксируем локали
            $locales = ['ru', 'en', 'kk'];

            /**
             * 6 базовых хештегов (ключ = базовый slug)
             *
             * Для каждого: один цвет, а name/description зависят от локали.
             */
            $baseHashtags = [
                [
                    'key'   => 'html',
                    'color' => '#E44D26',
                    'translations' => [
                        'ru' => [
                            'name'        => 'HTML',
                            'description' => 'HTML — язык разметки для создания структуры веб-страниц: заголовки, абзацы, списки, таблицы и семантические теги.',
                        ],
                        'en' => [
                            'name'        => 'HTML',
                            'description' => 'HTML is a markup language used to define the structure of web pages: headings, paragraphs, lists, tables and semantic tags.',
                        ],
                        'kk' => [
                            'name'        => 'HTML',
                            'description' => 'HTML — веб-беттердің құрылымын сипаттайтын белгілеу тілі: тақырыптар, абзацтар, тізімдер, кестелер және семантикалық тегтер.',
                        ],
                    ],
                ],
                [
                    'key'   => 'css',
                    'color' => '#1572B6',
                    'translations' => [
                        'ru' => [
                            'name'        => 'CSS',
                            'description' => 'CSS — таблицы стилей для оформления веб-страниц: цвета, шрифты, отступы, сетки и адаптивная вёрстка.',
                        ],
                        'en' => [
                            'name'        => 'CSS',
                            'description' => 'CSS is a style sheet language used to control the visual appearance of web pages: colors, fonts, spacing, layouts and responsiveness.',
                        ],
                        'kk' => [
                            'name'        => 'CSS',
                            'description' => 'CSS — веб-беттердің сыртқы көрінісін басқаратын стиль кестелері: түстер, қаріптер, аралықтар, орналасу және адаптивті вёрстка.',
                        ],
                    ],
                ],
                [
                    'key'   => 'javascript',
                    'color' => '#F7DF1E',
                    'translations' => [
                        'ru' => [
                            'name'        => 'JavaScript',
                            'description' => 'JavaScript — язык программирования для добавления интерактивности на сайт: обработка событий, работа с DOM и запросы к серверу.',
                        ],
                        'en' => [
                            'name'        => 'JavaScript',
                            'description' => 'JavaScript is a programming language used to add interactivity to websites: event handling, DOM manipulation and server requests.',
                        ],
                        'kk' => [
                            'name'        => 'JavaScript',
                            'description' => 'JavaScript — сайтқа интерактивтілік қосатын бағдарламалау тілі: оқиғаларды өңдеу, DOM-пен жұмыс және серверге сұраулар.',
                        ],
                    ],
                ],
                [
                    'key'   => 'laravel',
                    'color' => '#FF2D20',
                    'translations' => [
                        'ru' => [
                            'name'        => 'Laravel',
                            'description' => 'Laravel — современный PHP-фреймворк для быстрого и удобного создания веб-приложений, API и сложных бизнес-систем.',
                        ],
                        'en' => [
                            'name'        => 'Laravel',
                            'description' => 'Laravel is a modern PHP framework for building web applications, APIs and complex business systems with expressive, elegant syntax.',
                        ],
                        'kk' => [
                            'name'        => 'Laravel',
                            'description' => 'Laravel — веб-қосымшалар, API және күрделі бизнес жүйелерін ыңғайлы құруға арналған заманауи PHP фреймворкі.',
                        ],
                    ],
                ],
                [
                    'key'   => 'vuejs',
                    'color' => '#42B883',
                    'translations' => [
                        'ru' => [
                            'name'        => 'Vue.js',
                            'description' => 'Vue.js — прогрессивный JavaScript-фреймворк для создания реактивных интерфейсов и SPA-приложений.',
                        ],
                        'en' => [
                            'name'        => 'Vue.js',
                            'description' => 'Vue.js is a progressive JavaScript framework for building reactive user interfaces and single-page applications.',
                        ],
                        'kk' => [
                            'name'        => 'Vue.js',
                            'description' => 'Vue.js — реактивті интерфейстер мен бір беттен тұратын қосымшаларды жасауға арналған прогрессивті JavaScript фреймворкі.',
                        ],
                    ],
                ],
                [
                    'key'   => 'tailwindcss',
                    'color' => '#38BDF8',
                    'translations' => [
                        'ru' => [
                            'name'        => 'Tailwind CSS',
                            'description' => 'Tailwind CSS — утилитарный CSS-фреймворк, позволяющий быстро собирать адаптивный дизайн прямо в класcах разметки.',
                        ],
                        'en' => [
                            'name'        => 'Tailwind CSS',
                            'description' => 'Tailwind CSS is a utility-first CSS framework that enables rapid creation of responsive designs directly in markup classes.',
                        ],
                        'kk' => [
                            'name'        => 'Tailwind CSS',
                            'description' => 'Tailwind CSS — адаптивті дизайнды тікелей белгілеу класстарында тез құруға мүмкіндік беретін утилитарлық CSS фреймворкі.',
                        ],
                    ],
                ],
            ];

            $sort = 0;

            foreach ($baseHashtags as $tag) {
                foreach ($locales as $locale) {
                    $t = $tag['translations'][$locale];

                    $name        = $t['name'];
                    $description = $t['description'];
                    $slug        = $this->makeSlug($tag['key'], $name, $locale);
                    $color       = $tag['color'];

                    // Краткое описание — первые 200 символов description
                    $short = mb_substr($description, 0, 200);

                    // SEO
                    $metaTitleSuffix = match ($locale) {
                        'ru' => 'Хештег',
                        'kk' => 'Хэштег',
                        default => 'Hashtag',
                    };

                    $metaTitle    = $name . ' | ' . $metaTitleSuffix;
                    $metaKeywords = Str::replace('-', ', ', $slug);
                    $metaDesc     = $description;

                    /** @var Hashtag $model */
                    $model = Hashtag::withTrashed()->firstOrNew([
                        'slug'   => $slug,
                        'locale' => $locale,
                    ]);

                    $model->fill([
                        'sort'          => $sort++,
                        'activity'      => true,
                        'name'          => $name,
                        'slug'          => $slug,
                        'locale'        => $locale,
                        'color'         => $color,
                        'short'         => $short,
                        'description'   => $description,

                        // Если запись уже есть — не обнуляем накопленную статистику
                        'views'         => $model->exists ? $model->views : 0,
                        'likes'         => $model->exists ? $model->likes : 0,

                        'meta_title'    => $metaTitle,
                        'meta_keywords' => $metaKeywords,
                        'meta_desc'     => $metaDesc,
                    ]);

                    if ($model->exists && $model->trashed()) {
                        $model->restore();
                    }

                    $model->save();
                }
            }
        });
    }

    /**
     * Сборка уникального slug в рамках конкретной locale.
     */
    protected function makeSlug(?string $slug, string $name, ?string $locale): string
    {
        $base = Str::slug($slug ?? $name);

        if (!Hashtag::where('slug', $base)->where('locale', $locale)->exists()) {
            return $base;
        }

        $i = 2;
        while (
        Hashtag::where('slug', "{$base}-{$i}")
            ->where('locale', $locale)
            ->exists()
        ) {
            $i++;
        }

        return "{$base}-{$i}";
    }
}
