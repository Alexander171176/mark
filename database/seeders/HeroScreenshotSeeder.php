<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Hero\HeroScreenshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeroScreenshotSeeder extends Seeder
{
    public function run(): void
    {
        $locales = ['ru', 'en', 'kk'];

        $sections = DB::table('hero_sections')
            ->whereIn('locale', $locales)
            ->get()
            ->keyBy('locale');

        if ($sections->count() !== count($locales)) {
            $this->call(HeroSectionSeeder::class);
            $sections = DB::table('hero_sections')
                ->whereIn('locale', $locales)
                ->get()
                ->keyBy('locale');
        }

        $items = [
            ['alt' => 'landing-1',       'file' => 'landing-1'],
            ['alt' => 'landing-1 (alt)', 'file' => 'landing-1'],
            ['alt' => 'landing-4',       'file' => 'landing-4'],
            ['alt' => 'landing-7',       'file' => 'landing-7'],
            ['alt' => 'landing-19',      'file' => 'landing-19'],
            ['alt' => 'landing-5',       'file' => 'landing-5'],
            ['alt' => 'landing-4 (alt)', 'file' => 'landing-4'],
            ['alt' => 'landing-6',       'file' => 'landing-6'],
            ['alt' => 'blog-1',          'file' => 'blog-1'],
            ['alt' => 'post-1',          'file' => 'post-1'],
            ['alt' => 'about-1',         'file' => 'about-1'],
        ];

        $baseDir = storage_path('app/public/home/hero/screenshots');

        foreach ($locales as $locale) {
            $section = $sections[$locale] ?? null;
            if (!$section) continue;

            foreach ($items as $i => $it) {
                $shot = HeroScreenshot::updateOrCreate(
                    ['hero_section_id' => $section->id, 'alt' => $it['alt']],
                    ['sort' => $i + 1, 'activity' => true]
                );

                $light = $baseDir . DIRECTORY_SEPARATOR . "{$it['file']}.png";
                $dark  = $baseDir . DIRECTORY_SEPARATOR . "{$it['file']}-dark.png";

                if (is_file($light)) {
                    $shot->addMedia($light)
                        ->preservingOriginal()
                        ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_LIGHT);
                }

                if (is_file($dark)) {
                    $shot->addMedia($dark)
                        ->preservingOriginal()
                        ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_DARK);
                }
            }
        }
    }
}
