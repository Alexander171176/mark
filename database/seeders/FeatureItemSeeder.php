<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // гарантируем наличие секций
        $ru = DB::table('feature_sections')->where('locale', 'ru')->first();
        $en = DB::table('feature_sections')->where('locale', 'en')->first();
        $kk = DB::table('feature_sections')->where('locale', 'kk')->first();

        if (!$ru || !$en || !$kk) {
            $this->call(FeatureSectionSeeder::class);
            $ru = DB::table('feature_sections')->where('locale', 'ru')->first();
            $en = DB::table('feature_sections')->where('locale', 'en')->first();
            $kk = DB::table('feature_sections')->where('locale', 'kk')->first();
        }

        // --- SVG иконки (с оригинальными классами/атрибутами) ---
        $svgVue = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24"
data-icon="uit:vuejs-alt" class="iconify iconify--uit">
<path fill="currentColor" d="M22.934 3.256a.5.5 0 0 0-.434-.251l-4.99-.003L17.503 3l-4-.026H13.5a.5.5 0 0 0-.43.245l-1.072 1.805l-1.07-1.78A.5.5 0 0 0 10.505 3l-4-.027H6.5A.5.5 0 0 0 6.399 3H1.5a.5.5 0 0 0-.432.752l10.5 18a.5.5 0 0 0 .864 0l10.5-17.995a.5.5 0 0 0 .002-.501m-12.718.742l1.355 2.259A.5.5 0 0 0 12 6.5h.001a.5.5 0 0 0 .429-.245l1.353-2.28l2.83.02l-3.006 4.917L12 11.54L7.394 3.979zM12 20.508L2.37 4h3.85l5.353 8.76a.5.5 0 0 0 .147.142c.014.01.021.026.035.034a.5.5 0 0 0 .672-.175l5.353-8.759l3.85.002z"></path>
</svg>
SVG;

        $svgLayoutRight = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24"
data-icon="iconoir:layout-right" class="iconify iconify--iconoir">
<path fill="none" stroke="currentColor" stroke-width="1.5" d="M20.4 3H3.6a.6.6 0 0 0-.6.6v16.8a.6.6 0 0 0 .6.6h16.8a.6.6 0 0 0 .6-.6V3.6a.6.6 0 0 0-.6-.6Zm-6.15 6.75V21M21 9.75H3"></path>
</svg>
SVG;

        $svgMoon = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 256 256"
data-icon="ph:moon" class="iconify iconify--ph">
<path fill="currentColor" d="M233.54 142.23a8 8 0 0 0-8-2a88.08 88.08 0 0 1-109.8-109.8a8 8 0 0 0-10-10a104.84 104.84 0 0 0-52.91 37A104 104 0 0 0 136 224a103.1 103.1 0 0 0 62.52-20.88a104.84 104.84 0 0 0 37-52.91a8 8 0 0 0-1.98-7.98m-44.64 48.11A88 88 0 0 1 65.66 67.11a89 89 0 0 1 31.4-26A106 106 0 0 0 96 56a104.11 104.11 0 0 0 104 104a106 106 0 0 0 14.92-1.06a89 89 0 0 1-26.02 31.4"></path>
</svg>
SVG;

        $svgLifebuoy = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 256 256"
data-icon="ph:lifebuoy-light" class="iconify iconify--ph">
<path fill="currentColor" d="M128 26a102 102 0 1 0 102 102A102.12 102.12 0 0 0 128 26m36.47 130a45.87 45.87 0 0 0 0-56l31.24-31.23a89.81 89.81 0 0 1 0 118.44ZM94 128a34 34 0 1 1 34 34a34 34 0 0 1-34-34m93.22-67.71L156 91.53a45.87 45.87 0 0 0-56 0L68.78 60.29a89.81 89.81 0 0 1 118.44 0M60.29 68.78L91.53 100a45.87 45.87 0 0 0 0 56l-31.24 31.22a89.81 89.81 0 0 1 0-118.44m8.49 126.93L100 164.47a45.87 45.87 0 0 0 56 0l31.23 31.24a89.81 89.81 0 0 1-118.44 0Z"></path>
</svg>
SVG;

        $svgTerminal = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24"
data-icon="iconoir:terminal-outline" class="iconify iconify--iconoir">
<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
<path d="M13 16h5M6 8l4 4l-4 4"></path>
<path d="M2 18V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2"></path>
</g>
</svg>
SVG;

        $svgPlug = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 15 15"
data-icon="teenyicons:plug-outline" class="iconify iconify--teenyicons">
<path fill="none" stroke="currentColor" d="M6.5 11.5V15m2-3.5V15m-4-15v4.5m6-4.5v4.5m-8 0h10v3h-1v2a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-2h-1z"></path>
</svg>
SVG;

        $svgSmartphone = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 1024 1024"
data-icon="simple-line-icons:screen-smartphone" class="iconify iconify--simple-line-icons">
<path fill="currentColor" d="M704.144 0H319.856c-53.024 0-96 42.976-96 96v832c0 53.024 42.976 96 96 96h384.288c53.024 0 96-42.976 96-96V96c0-53.024-42.976-96-96-96m32 928c0 17.664-14.336 32-32 32H319.856c-17.664 0-32-14.336-32-32V96c0-17.664 14.336-32 32-32h384.288c17.664 0 32 14.336 32 32zM512.048 800.176c-35.28 0-63.84 28.592-63.84 63.824s28.576 63.841 63.84 63.841c35.28 0 63.84-28.608 63.84-63.84s-28.56-63.825-63.84-63.825m64-704.176h-128c-17.664 0-32 14.336-32 32s14.336 32 32 32h128c17.664 0 32-14.336 32-32s-14.336-32-32-32"></path>
</svg>
SVG;

        $svgDoc = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 15 15"
data-icon="teenyicons:text-document-outline" class="iconify iconify--teenyicons">
<path fill="currentColor" d="M4.5 6.995H4v1h.5zm6 1h.5v-1h-.5zm-6 1.998H4v1h.5zm6 1.007h.5v-1h-.5zm-6-7.003H4v1h.5zM8.5 5H9V4h-.5zm2-4.5l.354-.354L10.707 0H10.5zm3 3h.5v-.207l-.146-.147zm-9 4.495h6v-1h-6zm0 2.998l6 .007v-1l-6-.007zm0-5.996L8.5 5V4l-4-.003zm8 9.003h-10v1h10zM2 13.5v-12H1v12zM2.5 1h8V0h-8zM13 3.5v10h1v-10zM10.146.854l3 3l.708-.708l-3-3zM2.5 14a.5.5 0 0 1-.5-.5H1A1.5 1.5 0 0 0 2.5 15zm10 1a1.5 1.5 0 0 0 1.5-1.5h-1a.5.5 0 0 1-.5.5zM2 1.5a.5.5 0 0 1 .5-.5V0A1.5 1.5 0 0 0 1 1.5z"></path>
</svg>
SVG;

        // --- RU (переводы + те же SVG) ---
        $itemsRu = [
            [
                'title' => '600+ компонентов',
                'description' => "Готовые блоки с поддержкой тёмной темы.",
                'svg' => $svgVue],
            [
                'title' => '100+ демо',
                'description' => "Vulk поставляется с множеством красивых демо для старта.",
                'svg' => $svgLayoutRight],
            [
                'title' => 'Нативный тёмный режим',
                'description' => "Каждый элемент интерфейса готов к тёмной теме.",
                'svg' => $svgMoon],
            [
                'title' => 'Активная поддержка',
                'description' => "Мы помогаем оперативно решать любые вопросы.",
                'svg' => $svgLifebuoy],
            [
                'title' => 'Чистый код',
                'description' => "Кодовая база большая, но структурированная и надёжная.",
                'svg' => $svgTerminal],
            [
                'title' => '100 000 иконок',
                'description' => "Поставляется с более чем 100000 иконками, очищенными на сборке.",
                'svg' => $svgPlug],
            [
                'title' => 'Полная адаптивность',
                'description' => "Построено на Bulma 0.9 — mobile-first фреймворке.",
                'svg' => $svgSmartphone],
            [
                'title' => 'Подробная документация',
                'description' => "Полный гайд, чтобы вы быстро стартовали.",
                'svg' => $svgDoc],
        ];

        $rowsRu = [];
        foreach ($itemsRu as $i => $it) {
            $rowsRu[] = [
                'feature_section_id' => $ru->id,
                'title'       => $it['title'],
                'subtitle'    => null,
                'description' => $it['description'],
                'alt'         => $it['title'],
                'image_light' => $it['svg'],
                'image_dark'  => $it['svg'],
                'sort'        => $i + 1,
                'activity'    => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('feature_items')->upsert(
            $rowsRu,
            ['feature_section_id','title'],
            ['subtitle','description','alt','image_light','image_dark','sort','activity','updated_at']
        );

        // --- EN ---
        $itemsEn = [
            [
                'title' => '600+ Components',
                'description' => "Ready to go building blocks with dark mode support.",
                'svg' => $svgVue
            ],
            [
                'title' => '100+ Demos',
                'description' => "Vulk ships with a lot of beautiful demos to start from.",
                'svg' => $svgLayoutRight
            ],
            [
                'title' => 'Native Dark Mode',
                'description' => "Every single piece of UI is natively dark mode ready.",
                'svg' => $svgMoon
            ],
            [
                'title' => 'Active Support',
                'description' => "Our support helps you solve any issues you have.",
                'svg' => $svgLifebuoy
            ],
            [
                'title' => 'Clean Code',
                'description' => "Vulk's codebase is huge, but structured and robust.",
                'svg' => $svgTerminal
            ],
            [
                'title' => '100 000 Icons',
                'description' => "Vulk ships with more than 100000 icons purged at build.",
                'svg' => $svgPlug
            ],
            [
                'title' => 'Fully Responsive',
                'description' => "Vulk is built with Bulma 0.9, a mobile first framework.",
                'svg' => $svgSmartphone
            ],
            [
                'title' => 'Extensive Docs',
                'description' => "A complete documentation to help you get started fast.",
                'svg' => $svgDoc
            ],
        ];

        $rowsEn = [];
        foreach ($itemsEn as $i => $it) {
            $rowsEn[] = [
                'feature_section_id' => $en->id,
                'title'       => $it['title'],
                'subtitle'    => null,
                'description' => $it['description'],
                'alt'         => $it['title'],
                // inline SVG в колонки
                'image_light' => $it['svg'],
                'image_dark'  => $it['svg'],
                'sort'        => $i + 1,
                'activity'    => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('feature_items')->upsert(
            $rowsEn,
            ['feature_section_id','title'],
            ['subtitle','description','alt','image_light','image_dark','sort','activity','updated_at']
        );

        // --- KK (переводы + те же SVG) ---
        $itemsKk = [
            [
                'title' => '600+ компонент',
                'description' => "Қараңғы тақырыпты қолдайтын дайын блоктар.",
                'svg' => $svgVue
            ],
            [
                'title' => '100+ демо',
                'description' => "Vulk сізді бастау үшін көптеген жақсы демонстрациялармен бірге келеді.",
                'svg' => $svgLayoutRight
            ],
            [
                'title' => 'Жергілікті қараңғы режим',
                'description' => "Әрбір интерфейс элементі қараңғы тақырыпқа дайын.",
                'svg' => $svgMoon
            ],
            [
                'title' => 'Белсенді қолдау',
                'description' => "Біз кез келген мәселелерді тез шешуге көмектесеміз.",
                'svg' => $svgLifebuoy
            ],
            [
                'title' => 'Таза код',
                'description' => "Код базасы үлкен, бірақ құрылымдалған және сенімді.",
                'svg' => $svgTerminal
            ],
            [
                'title' => '100 000 белгіше',
                'description' => "Құрастыруға дейін тазартылған 100 000-нан астам белгішемен бірге келеді.",
                'svg' => $svgPlug
            ],
            [
                'title' => 'Толық бейімделу',
                'description' => "Мобильді құрылғыларға арналған Bulma 0.9 платформасында жасалған.",
                'svg' => $svgSmartphone
            ],
            [
                'title' => 'Егжей-тегжейлі құжаттама',
                'description' => "Тез бастауға арналған толық нұсқаулық.",
                'svg' => $svgDoc
            ],
        ];

        $rowsKk = [];
        foreach ($itemsKk as $i => $it) {
            $rowsKk[] = [
                'feature_section_id' => $kk->id,
                'title'       => $it['title'],
                'subtitle'    => null,
                'description' => $it['description'],
                'alt'         => $it['title'],
                'image_light' => $it['svg'],
                'image_dark'  => $it['svg'],
                'sort'        => $i + 1,
                'activity'    => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('feature_items')->upsert(
            $rowsKk,
            ['feature_section_id','title'],
            ['subtitle','description','alt','image_light','image_dark','sort','activity','updated_at']
        );
    }
}
