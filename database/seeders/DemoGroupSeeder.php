<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Demo\DemoGroup;
use App\Models\Admin\Constructor\HomePage\Demo\DemoSection;
use Illuminate\Database\Seeder;

class DemoGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Гарантируем наличие секций
        if (DemoSection::count() === 0) {
            $this->call(DemoSectionSeeder::class);
        }

        // Тексты групп для всех языков
        $texts = [
            'ru' => [
                ['slug' => 'landing',  'title' => 'Лендинги',                 'description' => 'Готовые демо-страницы'],
                ['slug' => 'about',    'title' => 'Страницы «О компании»',    'description' => 'Готовые демо-страницы'],
                ['slug' => 'pricing',  'title' => 'Страницы с ценами',        'description' => 'Готовые демо-страницы'],
                ['slug' => 'blog',     'title' => 'Страницы блога',           'description' => 'Готовые демо-страницы'],
                ['slug' => 'subpages', 'title' => 'Подстраницы',              'description' => 'Готовые демо-страницы'],
                ['slug' => 'auth',     'title' => 'Страницы авторизации',     'description' => 'Готовые демо-страницы'],
                ['slug' => 'layouts',  'title' => 'Макеты',                   'description' => 'Готовые стартовые макеты'],
            ],
            'en' => [
                ['slug' => 'landing',  'title' => 'Landing pages',  'description' => 'Prebuilt page demos'],
                ['slug' => 'about',    'title' => 'About pages',    'description' => 'Prebuilt page demos'],
                ['slug' => 'pricing',  'title' => 'Pricing pages',  'description' => 'Prebuilt page demos'],
                ['slug' => 'blog',     'title' => 'Blog pages',     'description' => 'Prebuilt page demos'],
                ['slug' => 'subpages', 'title' => 'Sub pages',      'description' => 'Prebuilt page demos'],
                ['slug' => 'auth',     'title' => 'Auth pages',     'description' => 'Prebuilt page demos'],
                ['slug' => 'layouts',  'title' => 'Master Layouts', 'description' => 'Prebuilt starter layouts'],
            ],
            'kk' => [
                ['slug' => 'landing',  'title' => 'Қону беттері',         'description' => 'Дайын демо беттер'],
                ['slug' => 'about',    'title' => 'Біз туралы беттер',    'description' => 'Дайын демо беттер'],
                ['slug' => 'pricing',  'title' => 'Баға беттері',         'description' => 'Дайын демо беттер'],
                ['slug' => 'blog',     'title' => 'Блог беттері',         'description' => 'Дайын демо беттер'],
                ['slug' => 'subpages', 'title' => 'Ішкі беттер',          'description' => 'Дайын демо беттер'],
                ['slug' => 'auth',     'title' => 'Авторизация беттері',  'description' => 'Дайын демо беттер'],
                ['slug' => 'layouts',  'title' => 'Орналасулар',          'description' => 'Дайын бастапқы макеттер'],
            ],
        ];

        /**
         * Маппинг файлов иконок (light/dark) по слагу.
         * 1) Сначала пытаемся взять «уникальные» файлы группы (если они есть в /public/images/vulk).
         * 2) Иначе — дефолт: layout-1.svg / layout-1-dark.svg (как в оригинальных Vue).
         */
        $iconMap = [
            'landing'  => ['light' => 'images/vulk/layout-landing.svg',  'dark' => 'images/vulk/layout-landing-dark.svg'],
            'about'    => ['light' => 'images/vulk/layout-about.svg',    'dark' => 'images/vulk/layout-about-dark.svg'],
            'pricing'  => ['light' => 'images/vulk/layout-pricing.svg',  'dark' => 'images/vulk/layout-pricing-dark.svg'],
            'blog'     => ['light' => 'images/vulk/layout-blog.svg',     'dark' => 'images/vulk/layout-blog-dark.svg'],
            'subpages' => ['light' => 'images/vulk/layout-subpages.svg', 'dark' => 'images/vulk/layout-subpages-dark.svg'],
            'auth'     => ['light' => 'images/vulk/layout-auth.svg',     'dark' => 'images/vulk/layout-auth-dark.svg'],
            'layouts'  => ['light' => 'images/vulk/layout-master.svg',   'dark' => 'images/vulk/layout-master-dark.svg'],
        ];

        // Дефолтные (как в компонентах)
        $defaultLight = public_path('images/vulk/layout-1.svg');
        $defaultDark  = public_path('images/vulk/layout-1-dark.svg');

        // Проходим по секциям и создаём группы с иконками
        DemoSection::query()->orderBy('id')->get()->each(function (DemoSection $section) use ($texts, $iconMap, $defaultLight, $defaultDark) {
            $locale = $section->locale ?? 'en';
            $rows   = $texts[$locale] ?? $texts['en'];

            foreach ($rows as $idx => $g) {
                // Подбираем пути к SVG для конкретного слага
                $lightPath = public_path($iconMap[$g['slug']]['light'] ?? '');
                $darkPath  = public_path($iconMap[$g['slug']]['dark']  ?? '');

                // Если уникальных файлов нет — берём дефолт
                if (!is_file($lightPath)) $lightPath = $defaultLight;
                if (!is_file($darkPath))  $darkPath  = $defaultDark;

                // Грузим как inline SVG (или плейсхолдер)
                $svgLight = $this->readSvgOrPlaceholder($lightPath, 'light');
                $svgDark  = $this->readSvgOrPlaceholder($darkPath,  'dark');

                DemoGroup::updateOrCreate(
                    ['section_id' => $section->id, 'slug' => $g['slug']],
                    [
                        'title'           => $g['title'],
                        'description'     => $g['description'],
                        'icon_alt'        => $locale === 'ru' ? 'Иконка группы' : 'Group icon',
                        'icon_svg_light'  => $svgLight,
                        'icon_svg_dark'   => $svgDark,
                        'sort'            => $idx + 1,
                        'activity'        => true,
                    ]
                );
            }
        });
    }

    private function readSvgOrPlaceholder(?string $path, string $mode = 'light'): string
    {
        if ($path && is_file($path)) {
            $svg = @file_get_contents($path);
            if (is_string($svg) && $svg !== '') {
                return $svg;
            }
        }
        // Простой плейсхолдер (если файл не найден)
        $stroke = $mode === 'dark' ? '#94a3b8' : '#475569';
        $fill   = $mode === 'dark' ? '#1f2937' : '#e2e8f0';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" width="120" height="80">
  <rect x="1" y="1" width="118" height="78" rx="8" fill="$fill" stroke="$stroke" stroke-width="2"/>
  <path d="M15 55 L35 35 L55 50 L85 25 L105 45" fill="none" stroke="$stroke" stroke-width="3"/>
</svg>
SVG;
    }
}
