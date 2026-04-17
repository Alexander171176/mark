<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Developer\DeveloperItem;
use App\Models\Admin\Constructor\HomePage\Developer\DeveloperSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeveloperItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key' => 'vue',
                'title' => 'Vue 3',
                'description' => 'Vue 3 and its powerful composition API makes your experience pleasant along with killer features.',
                'file' => 'vuejs-light.svg',
            ],
            [
                'key' => 'ts',
                'title' => 'Typescript',
                'description' => 'Typescript makes things much more simpler but remains optional. A good occasion to learn about it!',
                'file' => 'typescript-light.svg',
            ],
            [
                'key' => 'vite',
                'title' => 'Vite JS',
                'description' => 'Vite is blazing fast, does HOT reloading and parses all your components. 100x faster than Webpack.',
                'file' => 'vite-light.svg',
            ],
            [
                'key' => 'vscode',
                'title' => 'VS Code Integration',
                'description' => 'Vulk is fully integrated with VS Code to help you produce quality checked code. Supports Typescript.',
                'file' => 'vscode-light.svg',
            ],
            [
                'key' => 'eslint',
                'title' => 'ES Lint',
                'description' => 'ES Lint watches your javascript code and makes sure it matches the best standards. Code with standards.',
                'file' => 'eslint-light.svg',
            ],
            [
                'key' => 'prettier',
                'title' => 'Prettier',
                'description' => 'Prettier tidies your code and makes it look much more cleaner and easier to read. Read code easily.',
                'file' => 'prettier-light.svg',
            ],
            [
                'key' => 'stylelint',
                'title' => 'Stylelint',
                'description' => 'Stylelint looks for poorly formatted styles and fixes everything for you. Learn to write valid CSS.',
                'file' => 'stylelint-light.svg',
            ],
            [
                'key' => 'docker',
                'title' => 'Docker',
                'description' => 'Vulk ships with a Docker file to make your test deployments faster and easier. Get started fast!',
                'file' => 'docker-light.svg',
            ],
        ];

        foreach (['ru', 'en', 'kk'] as $locale) {
            $section = DeveloperSection::firstOrCreate(
                ['locale' => $locale],
                [
                    'title'     => 'Build a professional website',
                    'subtitle'  => 'Developer Experience',
                    'sort'      => 3,
                    'is_dark'   => false,
                    'activity'  => true,
                ]
            );

            foreach ($items as $idx => $item) {
                $inline = $this->readSvgOrFallback("images/vulk/{$item['file']}", $item['key']);

                DeveloperItem::updateOrCreate(
                    [
                        'developer_section_id' => $section->id,
                        'title'                => $item['title'],
                    ],
                    [
                        'subtitle'     => null,
                        'description'  => $item['description'],
                        'image_light'  => $inline, // inline SVG (TEXT)
                        'image_dark'   => $inline, // используем те же SVG
                        'alt'          => $item['title'],
                        'sort'         => $idx,
                        'activity'     => true,
                    ]
                );
            }
        }
    }

    /**
     * Пытается прочитать SVG из public_path($relative).
     * Если нет — возвращает уникальный fallback для конкретного ключа.
     */
    protected function readSvgOrFallback(string $relative, string $key): string
    {
        $path = public_path($relative);

        if (is_file($path)) {
            $svg = @file_get_contents($path);
            if (is_string($svg) && Str::contains(Str::lower($svg), '<svg')) {
                return $svg;
            }
        }

        return $this->fallbackSvg($key);
    }

    /**
     * 8 разных fallback-SVG под каждый пункт.
     * Минимальные, аккуратные, с уникальными формами/буквами.
     */
    protected function fallbackSvg(string $key): string
    {
        switch ($key) {
            case 'vue':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <defs>
    <linearGradient id="gvue" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#41b883"/>
      <stop offset="1" stop-color="#35495e"/>
    </linearGradient>
  </defs>
  <rect rx="12" ry="12" x="4" y="4" width="88" height="88" fill="#e5f7f0"/>
  <path d="M20 28l28 48 28-48h-14l-14 24L34 28z" fill="url(#gvue)"/>
  <path d="M34 28l14 24 14-24h-8l-6 10-6-10z" fill="#fff" opacity=".9"/>
</svg>
SVG;

            case 'ts':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" ry="12" fill="#e6f0ff"/>
  <rect x="12" y="12" width="72" height="72" rx="10" ry="10" fill="#3178c6"/>
  <text x="26" y="60" font-family="Inter, Arial, sans-serif" font-size="36" font-weight="700" fill="#fff">TS</text>
</svg>
SVG;

            case 'vite':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <defs>
    <linearGradient id="gvite1" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#646cff"/>
      <stop offset="1" stop-color="#b692ff"/>
    </linearGradient>
    <linearGradient id="gvite2" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#ffdd55"/>
      <stop offset="1" stop-color="#ffa800"/>
    </linearGradient>
  </defs>
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#f5f3ff"/>
  <path d="M48 16l26 12-26 52L22 28z" fill="url(#gvite1)" opacity=".9"/>
  <path d="M53 18l-14 26h10l-6 26 14-26H47z" fill="url(#gvite2)"/>
</svg>
SVG;

            case 'vscode':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#eaf5ff"/>
  <g transform="translate(10,10)">
    <rect x="16" y="8" width="44" height="60" rx="8" fill="#007acc"/>
    <path d="M18 38l12-12 20 20-20 20-12-12 8-8z" fill="#fff" opacity=".9"/>
  </g>
</svg>
SVG;

            case 'eslint':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#f6f1ff"/>
  <circle cx="48" cy="48" r="26" fill="#4b32c3"/>
  <polygon points="48,30 62,38 62,58 48,66 34,58 34,38" fill="#fff" opacity=".95"/>
</svg>
SVG;

            case 'prettier':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#fff7ed"/>
  <g stroke="#111827" stroke-width="4" stroke-linecap="round">
    <path d="M24 28h36"/>
    <path d="M24 38h24"/>
    <path d="M24 48h42"/>
    <path d="M24 58h30"/>
    <path d="M24 68h36"/>
  </g>
</svg>
SVG;

            case 'stylelint':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#ecfeff"/>
  <path d="M26 62l18-18 10 10 16-16" fill="none" stroke="#0ea5b7" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M22 68h52" stroke="#0ea5b7" stroke-width="4" stroke-linecap="round"/>
</svg>
SVG;

            case 'docker':
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#eff6ff"/>
  <g fill="#1d4ed8">
    <rect x="22" y="44" width="12" height="10" rx="2"/>
    <rect x="36" y="44" width="12" height="10" rx="2"/>
    <rect x="50" y="44" width="12" height="10" rx="2"/>
    <rect x="36" y="32" width="12" height="10" rx="2"/>
    <rect x="50" y="32" width="12" height="10" rx="2"/>
  </g>
  <path d="M18 64c2 8 10 12 20 12h20c10 0 18-4 20-12" fill="none" stroke="#1d4ed8" stroke-width="4" stroke-linecap="round"/>
</svg>
SVG;

            default:
                // общий запасной
                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" class="is-image is-image-block" width="96" height="96">
  <rect x="4" y="4" width="88" height="88" rx="12" fill="#e5e7eb"/>
  <path d="M24 48h48" stroke="#111827" stroke-width="6" stroke-linecap="round"/>
</svg>
SVG;
        }
    }
}
