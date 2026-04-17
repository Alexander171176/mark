<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Wave\WaveTech;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaveTechSeeder extends Seeder
{
    public function run(): void
    {
        // Убедимся, что все локали есть
        $sectionRu = DB::table('wave_sections')->where('locale', 'ru')->first();
        $sectionEn = DB::table('wave_sections')->where('locale', 'en')->first();
        $sectionKk = DB::table('wave_sections')->where('locale', 'kk')->first();
        if (!$sectionEn || !$sectionRu) {
            $this->call(WaveSectionSeeder::class);
            $sectionRu = DB::table('wave_sections')->where('locale', 'ru')->first();
            $sectionEn = DB::table('wave_sections')->where('locale', 'en')->first();
            $sectionKk = DB::table('wave_sections')->where('locale', 'kk')->first();
        }

        $now = now();

        $items = [
            [
                'img' => 'images/vulk/vuejs-light.svg',
                'alt' => 'Vue.js logo',
                'title' => 'Vue 3',
                'subtitle' => [
                    'ru' => 'Фронтенд-фреймворк',
                    'en' => 'Frontend framework',
                    'kk' => 'Фронтенд фреймворкі',
                ],
                'description' => [
                    'ru' => 'Vue 3 — мощная переработка Vue 2.',
                    'en' => 'Vue 3 is a robust and overpowered rewrite of Vue 2.',
                    'kk' => 'Vue 3 - Vue 2-нің үлкен қайта нұсқасы.',
                ],
            ],
            [
                'img' => 'images/vulk/vite-light.svg',
                'alt' => 'Vite logo',
                'title' => 'Vite JS',
                'subtitle' => [
                    'ru' => 'Компилятор / Бандлер',
                    'en' => 'Compiler / Bundler',
                    'kk' => 'Компилятор / Бундлер',
                ],
                'description' => [
                    'ru' => 'Vite — новый компилятор, до 100× быстрее Webpack.',
                    'en' => 'Vite is a brand new compiler that is 100x faster than Webpack.',
                    'kk' => 'Vite - Webpack-тен 100 есе жылдам жаңа компилятор.',
                ],
            ],
            [
                'img' => 'images/vulk/typescript-light.svg',
                'alt' => 'TypeScript logo',
                'title' => 'Typescript',
                'subtitle' => [
                    'ru' => 'Язык поверх JS',
                    'en' => 'Javascript framework',
                    'kk' => 'JS-тің үстіндегі тіл',
                ],
                'description' => [
                    'ru' => 'TypeScript — строго типизированный язык поверх JS.',
                    'en' => 'TypeScript is a strongly typed language that builds on JS.',
                    'kk' => 'TypeScript - JS-тен басқа, қатаң типтелген тіл.',
                ],
            ],
            [
                'img' => 'images/vulk/sass-light.svg',
                'alt' => 'Sass logo',
                'title' => 'Sass',
                'subtitle' => [
                    'ru' => 'CSS-препроцессор',
                    'en' => 'CSS Preprocessor',
                    'kk' => 'CSS препроцессоры',
                ],
                'description' => [
                    'ru' => 'Sass — компилятор SCSS/SASS в CSS.',
                    'en' => 'Sass is a compiler that builds SCSS and SASS to CSS.',
                    'kk' => 'Sass - SCSS/SASS-тен CSS-ке дейінгі компилятор.',
                ],
            ],
            [
                'img' => 'images/vulk/bulma-light.svg',
                'alt' => 'Bulma logo',
                'title' => 'Bulma',
                'subtitle' => [
                    'ru' => 'CSS-фреймворк',
                    'en' => 'CSS Framework',
                    'kk' => 'CSS құрылымы',
                ],
                'description' => [
                    'ru' => 'Bulma — mobile-first CSS-фреймворк на Flexbox.',
                    'en' => 'Bulma is a mobile-first CSS framework based on Flexbox.',
                    'kk' => 'Bulma - Flexbox негізіндегі мобильді құрылғыларға арналған CSS платформасы.',
                ],
            ],
        ];

        // RU
        $this->seedForLocale($sectionRu->id, $items, $now, 'ru');

        // EN
        $this->seedForLocale($sectionEn->id, $items, $now, 'en');

        // KK
        $this->seedForLocale($sectionKk->id, $items, $now, 'kk');
    }

    private function seedForLocale(int $sectionId, array $items, $now, string $loc): void
    {
        foreach ($items as $i => $it) {
            // upsert строковых полей (оставляем как бэкап)
            DB::table('wave_teches')->updateOrInsert(
                ['wave_section_id' => $sectionId, 'title' => $it['title']],
                [
                    'alt'         => $it['alt'],
                    'subtitle'    => $it['subtitle'][$loc],
                    'description' => $it['description'][$loc],
                    'sort'        => $i + 1,
                    'activity'    => true,
                    'image_light' => $it['img'], // fallback
                    'image_dark'  => str_replace('-light.', '-dark.', $it['img']), // fallback
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );

            /** @var WaveTech $tech */
            $tech = WaveTech::where('wave_section_id', $sectionId)
                ->where('title', $it['title'])
                ->first();

            if (!$tech) continue;

            // Подцепим light/dark из public/images/vulk/*.svg (если есть)
            $lightPath = public_path($it['img']);
            $darkPath  = public_path(str_replace('-light.', '-dark.', $it['img']));

            if (is_file($lightPath)) {
                $tech->addMedia($lightPath)
                    ->preservingOriginal()
                    ->toMediaCollection(WaveTech::MEDIA_COLLECTION_LIGHT);
            }

            if (is_file($darkPath)) {
                $tech->addMedia($darkPath)
                    ->preservingOriginal()
                    ->toMediaCollection(WaveTech::MEDIA_COLLECTION_DARK);
            }
        }
    }
}
