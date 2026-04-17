<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Quality\QualityItem;
use App\Models\Admin\Constructor\HomePage\Quality\QualitySection;
use Illuminate\Database\Seeder;

class QualityItemSeeder extends Seeder
{
    public function run(): void
    {
        // Оригинальные 11 фич — один и тот же набор для EN и RU секций
        $items = [
            [
                'top_title'   => 'Modern Framework',
                'title'       => 'Vue 3',
                'delay'       => 0,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <path fill="currentColor" d="M22.934 3.256a.5.5 0 0 0-.434-.251l-4.99-.003L17.503 3l-4-.026H13.5a.5.5 0 0 0-.43.245l-1.072 1.805l-1.07-1.78A.5.5 0 0 0 10.505 3l-4-.027H6.5A.5.5 0 0 0 6.399 3H1.5a.5.5 0 0 0-.432.752l10.5 18a.5.5 0 0 0 .864 0l10.5-17.995a.5.5 0 0 0 .002-.501m-12.718.742l1.355 2.259A.5.5 0 0 0 12 6.5h.001a.5.5 0 0 0 .429-.245l1.353-2.28l2.83.02l-3.006 4.917L12 11.54L7.394 3.979zM12 20.508L2.37 4h3.85l5.353 8.76a.5.5 0 0 0 .147.142c.014.01.021.026.035.034a.5.5 0 0 0 .672-.175l5.353-8.759l3.85.002z"/>
</svg>
SVG,
                'description' => "Vulk is built with Vue 3 and its brand new Composition API. It also features loads of high quality component that have been prebuilt for you. Vulk also relies on the best coding practices from the Vue core team.",
            ],
            [
                'top_title'   => 'Easy Partials',
                'title'       => 'Vite JS',
                'delay'       => 80,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 510 512">
  <path fill="currentColor" d="M493.89 58.275L355.178 83.558L379.282 0L186.79 37.718l-2.999 50.64L15.145 58.214C3.53 57.538-3.238 65.879 1.558 77.46l244.056 427.983c5.253 8.575 17.347 8.91 22.65 0L507.575 77.419c5.4-9.676-2.874-21.018-13.685-19.144m-237 435.435L17.87 74.556l164.993 29.491l-7.778 131.365l67.632-15.608l-18.858 92.344l51.374-15.608l-25.495 123.397c-1.27 8.069 9.241 12.362 14.44.812l150.22-299.792l-74.135 14.308l10.086-34.962l140.91-25.684z"/>
</svg>
SVG,
                'description' => "Vulk is powered by Vite JS. Vite is a no bundler DEV environment for Vue.js, created by Evan You. Vite serves your code via native ES Module imports during development, allowing you to develop Vue.js single file components without a bundle step.",
            ],
            [
                'top_title'   => 'Bulma Framework',
                'title'       => 'Modular CSS',
                'delay'       => 160,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
    <path d="m4 3l1.778 17.09L12 22l6.222-1.91L20 3z"/>
    <path d="M7 7h9.5l-1 10l-3.5 1l-3.5-1l-.25-2.5m7.75-3H7.5"/>
  </g>
</svg>
SVG,
                'description' => "Vulk uses Dart-Sass, which is the latest Sass compilation standard. Vulk's styles are written in Sass and lazy-loaded at the component level. However, all variable are written using native CSS variables.",
            ],
            [
                'top_title'   => 'Custom SSR',
                'title'       => 'SSG/SSR Ready',
                'delay'       => 240,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M216 44H72a12 12 0 0 0-12 12v20H40a12 12 0 0 0-12 12v112a12 12 0 0 0 12 12h144a12 12 0 0 0 12-12v-20h20a12 12 0 0 0 12-12V56a12 12 0 0 0-12-12M40 84h144a4 4 0 0 1 4 4v20H36V88a4 4 0 0 1 4-4m148 116a4 4 0 0 1-4 4H40a4 4 0 0 1-4-4v-84h152Zm32-32a4 4 0 0 1-4 4h-20V88a12 12 0 0 0-12-12H68V56a4 4 0 0 1 4-4h144a4 4 0 0 1 4 4Z"/>
</svg>
SVG,
                'description' => "Since the new Vue 3 release is very recent, the entire ecosystem migration will likely take some time. For example, Nuxt 3 is still quite far from bein prime-ready. This is why we implemented our own cusom SSR setup.",
            ],
            [
                'top_title'   => 'Solid structure',
                'title'       => '600+ Components',
                'delay'       => 320,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M92 68.49L25 128l67 59.52a6 6 0 1 1-8 9l-72-64a6 6 0 0 1 0-9l72-64a6 6 0 0 1 8 9Zm152 55l-72-64a6 6 0 0 0-8 9L231 128l-67 59.52a6 6 0 1 0 8 9l72-64a6 6 0 0 0 0-9Z"/>
</svg>
SVG,
                'description' => "Vulk ships with more than 600 fully implemented vue 3 components using the Composition API. Most components are configured to waith for static or dynamic data sources.",
            ],
            [
                'top_title'   => 'Quickstart',
                'title'       => '100+ Prebuilt pages',
                'delay'       => 400,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M216 42H40a14 14 0 0 0-14 14v144a14 14 0 0 0 14 14h176a14 14 0 0 0 14-14V56a14 14 0 0 0-14-14M40 54h176a2 2 0 0 1 2 2v42H38V56a2 2 0 0 1 2-2m-2 146v-90h60v92H40a2 2 0 0 1-2-2m178 2H110v-92h108v90a2 2 0 0 1-2 2"/>
</svg>
SVG,
                'description' => "Vulk ships with a lot of prebuilt pages to help you build your project as fast as possible, without losing quality. You can also easily copy and paste components from different pages to create original layouts.",
            ],
            [
                'top_title'   => 'For Everything',
                'title'       => 'Multipurpose',
                'delay'       => 480,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M246 152a6 6 0 0 1-6 6h-18v18a6 6 0 0 1-12 0v-18h-18a6 6 0 0 1 0-12h18v-18a6 6 0 0 1 12 0v18h18a6 6 0 0 1 6 6M56 70h18v18a6 6 0 0 0 12 0V70h18a6 6 0 0 0 0-12H86V40a6 6 0 0 0-12 0v18H56a6 6 0 0 0 0 12m128 124h-10v-10a6 6 0 0 0-12 0v10h-10a6 6 0 0 0 0 12h10v10a6 6 0 0 0 12 0v-10h10a6 6 0 0 0 0-12m33.9-115.41L78.58 217.9a14 14 0 0 1-19.8 0l-20.69-20.69a14 14 0 0 1 0-19.8L177.41 38.1a14 14 0 0 1 19.8 0l20.69 20.69a14 14 0 0 1 0 19.8M167.51 112L144 88.49L46.58 185.9a2 2 0 0 0 0 2.83l20.69 20.68a2 2 0 0 0 2.82 0Zm41.9-44.73l-20.68-20.68a2 2 0 0 0-2.83 0L152.48 80L176 103.52l33.41-33.42a2 2 0 0 0 0-2.83"/>
</svg>
SVG,
                'description' => "Vulk is a multipurpose landing UI kit. It is also mademfor any type of modern website or web application. Featured content will of course continue to grow with future updates.",
            ],
            [
                'top_title'   => 'Mobile First',
                'title'       => 'Ultra Responsive',
                'delay'       => 560,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M176 18H80a22 22 0 0 0-22 22v176a22 22 0 0 0 22 22h96a22 22 0 0 0 22-22V40a22 22 0 0 0-22-22M70 62h116v132H70Zm10-32h96a10 10 0 0 1 10 10v10H70V40a10 10 0 0 1 10-10m96 196H80a10 10 0 0 1-10-10v-10h116v10a10 10 0 0 1-10 10"/>
</svg>
SVG,
                'description' => "Vulk is built on top of the very popular Bulma CSS Framework. Bulma is a free, open source framework that provides ready-to-use frontend components that you can easily combine to build responsive web interfaces.",
            ],
            [
                'top_title'   => 'All Inclusive',
                'title'       => 'Cool Vectors',
                'delay'       => 640,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <path fill="currentColor" d="M368 184h47.458c-4.664 69.192-39.8 119.633-95.458 140.869V288H192v36.869C136.344 303.633 101.206 253.192 96.542 184H144V56H16v128h48.471c2.993 50.374 20.242 93.872 50.341 126.537A170.7 170.7 0 0 0 166.7 348.9l-48.9 16.3a57 57 0 0 0-8.608-9.083a56.63 56.63 0 0 0-79.807 6.693A56.635 56.635 0 0 0 72.535 456q2.4 0 4.827-.2a56.61 56.61 0 0 0 51.731-60.634L192 374.2V416h128v-41.8l62.9 20.963a57.4 57.4 0 0 0 .043 8.939A56.64 56.64 0 0 0 439.274 456q2.4 0 4.828-.2a56.63 56.63 0 1 0-47.982-92.988c-.662.782-1.3 1.582-1.91 2.392L345.3 348.9a170.7 170.7 0 0 0 51.891-38.363c30.1-32.665 47.348-76.163 50.341-126.537H496V56H368ZM48 152V88h64v64Zm43.44 263.27a24.629 24.629 0 0 1-37.62-31.8a24.47 24.47 0 0 1 16.752-8.644q1.053-.087 2.1-.088A24.631 24.631 0 0 1 91.44 415.27M288 384h-64v-64h64Zm132.56-.529a24.47 24.47 0 0 1 16.752-8.644q1.053-.087 2.1-.088a24.635 24.635 0 1 1-18.851 8.733ZM400 88h64v64h-64Z"/>
</svg>
SVG,
                'description' => "At Css Ninja, we've always made our best to build high quality products. We believe that illustrations and graphic assets are part of a high quality visual and this is why we include all the graphics in our templates.",
            ],
            [
                'top_title'   => 'Customization',
                'title'       => 'Easy Theming',
                'delay'       => 720,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M216 26H72a38 38 0 0 0-38 38v72a22 22 0 0 0 22 22h48a2 2 0 0 1 2 2.23L98.08 207a7 7 0 0 0-.08 1a30 30 0 0 0 60 0a7 7 0 0 0-.08-1L150 160.23a2 2 0 0 1 2-2.23h48a22 22 0 0 0 22-22V32a6 6 0 0 0-6-6M72 38h106v42a6 6 0 0 0 12 0V38h20v68H46V64a26 26 0 0 1 26-26m128 108h-48a14 14 0 0 0-13.86 16v.15l7.86 46.32a18 18 0 0 1-36 0l7.82-46.34v-.15A14 14 0 0 0 104 146H56a10 10 0 0 1-10-10v-18h164v18a10 10 0 0 1-10 10"/>
</svg>
SVG,
                'description' => "With the power of Sass and CSS vars, Vulk customization is very easy. You can implement your branding litteraly in a matter of minutes simply by changing the color variables implemented by Vulk. Change the var(--primary) color variable, and that's it.",
            ],
            [
                'top_title'   => 'Updates & Support',
                'title'       => 'Active Support',
                'delay'       => 800,
                'svg'         => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
  <path fill="currentColor" d="M128 26a102 102 0 1 0 102 102A102.12 102.12 0 0 0 128 26m36.47 130a45.87 45.87 0 0 0 0-56l31.24-31.23a89.81 89.81 0 0 1 0 118.44ZM94 128a34 34 0 1 1 34 34a34 34 0 0 1-34-34m93.22-67.71L156 91.53a45.87 45.87 0 0 0-56 0L68.78 60.29a89.81 89.81 0 0 1 118.44 0M60.29 68.78L91.53 100a45.87 45.87 0 0 0 0 56l-31.24 31.22a89.81 89.81 0 0 1 0-118.44m8.49 126.93L100 164.47a45.87 45.87 0 0 0 56 0l31.23 31.24a89.81 89.81 0 0 1-118.44 0Z"/>
</svg>
SVG,
                'description' => "Vulk includes an extensive documentation that covers all the basic things you need to know before running the template and starting to work with it. You can also contact us anytime and open a ticket on our Support Portal.",
            ],
        ];

        // Записываем во все локали, если они существуют
        foreach (['ru', 'en', 'kk'] as $locale) {
            $section = QualitySection::query()->where('locale', $locale)->first();
            if (!$section) {
                // Если секции ещё нет — пропустим эту локаль
                continue;
            }

            foreach ($items as $idx => $raw) {
                $sort = $idx + 1;

                QualityItem::query()->updateOrCreate(
                    [
                        'section_id' => $section->id,
                        'title'      => $raw['title'], // уникальность в рамках секции
                    ],
                    [
                        'top_title'       => $raw['top_title'],
                        'description'     => $raw['description'],
                        'icon_svg_light'  => $raw['svg'],
                        'icon_svg_dark'   => $raw['svg'],
                        'reveal_from'     => 'bottom',
                        'delay'           => (int) $raw['delay'],
                        'sort'            => $sort,
                        'activity'        => true,
                    ]
                );
            }
        }
    }
}
