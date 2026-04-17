<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Component\ComponentFeature;
use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'box_class' => 'is-primary',
                'title'     => 'Reusable Components',
                'text'      => "Vulk ships with a ton of ready to use and reusable components. You can use them as is or duplicate to create your own variations and styles.",
                'svg'       => <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
        d="M461.81 53.81a4.4 4.4 0 0 0-3.3-3.39c-54.38-13.3-180 34.09-248.13 102.17a295 295 0 0 0-33.09 39.08c-21-1.9-42-.3-59.88 7.5c-50.49 22.2-65.18 80.18-69.28 105.07a9 9 0 0 0 9.8 10.4l81.07-8.9a180 180 0 0 0 1.1 18.3a18.15 18.15 0 0 0 5.3 11.09l31.39 31.39a18.15 18.15 0 0 0 11.1 5.3a180 180 0 0 0 18.19 1.1l-8.89 81a9 9 0 0 0 10.39 9.79c24.9-4 83-18.69 105.07-69.17c7.8-17.9 9.4-38.79 7.6-59.69a294 294 0 0 0 39.19-33.09c68.38-68 115.47-190.86 102.37-247.95M298.66 213.67a42.7 42.7 0 1 1 60.38 0a42.65 42.65 0 0 1-60.38 0"/>
  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
        d="M109.64 352a45.06 45.06 0 0 0-26.35 12.84C65.67 382.52 64 448 64 448s65.52-1.67 83.15-19.31A44.73 44.73 0 0 0 160 402.32"/>
</svg>
SVG
            ],
            [
                'box_class' => 'is-green',
                'title'     => 'Modular Code',
                'text'      => "Vulk's code is very modular and very versatile. The template only loads what you need as every component is bundled with its own styles.",
                'svg'       => <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <rect width="80" height="80" x="64" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="216" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="368" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="64" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="216" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="368" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="64" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="216" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
  <rect width="80" height="80" x="368" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
</svg>
SVG
            ],
            [
                'box_class' => 'is-info',
                'title'     => 'Easy Theming',
                'text'      => "Although it is built with Sass, Vulk leverages the power of native CSS variables, giving you the power to create any theme in a few minutes.",
                'svg'       => <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <path fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"
        d="M430.11 347.9c-6.6-6.1-16.3-7.6-24.6-9c-11.5-1.9-15.9-4-22.6-10c-14.3-12.7-14.3-31.1 0-43.8l30.3-26.9c46.4-41 46.4-108.2 0-149.2c-34.2-30.1-80.1-45-127.8-45c-55.7 0-113.9 20.3-158.8 60.1c-83.5 73.8-83.5 194.7 0 268.5c41.5 36.7 97.5 55 152.9 55.4h1.7c55.4 0 110-17.9 148.8-52.4c14.4-12.7 11.99-36.6.1-47.7Z"/>
  <circle cx="144" cy="208" r="32" fill="currentColor"/>
  <circle cx="152" cy="311" r="32" fill="currentColor"/>
  <circle cx="224" cy="144" r="32" fill="currentColor"/>
  <circle cx="256" cy="367" r="48" fill="currentColor"/>
  <circle cx="328" cy="144" r="32" fill="currentColor"/>
</svg>
SVG
            ],
        ];

        DB::transaction(function () use ($features) {
            $sections = ComponentSection::query()->get();
            foreach ($sections as $section) {
                // Чистим и пересоздаём, чтобы не плодить дубликаты
                $section->features()->delete();

                foreach ($features as $i => $f) {
                    ComponentFeature::create([
                        'section_id'     => $section->id,
                        'box_class'      => $f['box_class'],
                        'title'          => $f['title'],
                        'text'           => $f['text'],
                        'icon_svg_light' => $f['svg'],
                        'icon_svg_dark'  => $f['svg'],
                        'icon_alt'       => $f['title'],
                        'sort'           => $i,
                        'activity'       => true,
                    ]);
                }
            }
        });
    }
}
