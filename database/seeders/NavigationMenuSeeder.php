<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\NavigationMenu\NavigationMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('navigation_menus')) {
            $this->command?->warn('Нет таблицы navigation_menus — пропускаю NavigationMenuSeeder.');
            return;
        }

        $now = now();

        // Набор типовых меню
        $menus = [
            [
                'slug'     => 'header',
                'name'     => 'Главное меню',
                'location' => 'header',
                'sort' => 10,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'layout-dashboard',
                    'description' => 'Основная навигация в шапке сайта',
                ],
            ],
            [
                'slug'     => 'mobile',
                'name'     => 'Мобильное меню',
                'location' => 'header',
                'sort' => 15,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'smartphone',
                    'description' => 'Меню для мобильной версии (гамбургер)',
                ],
            ],
            [
                'slug'     => 'utility',
                'name'     => 'Служебное меню',
                'location' => 'header',
                'sort' => 20,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'wrench',
                    'description' => 'Линки авторизации, профиля, языка и т.п.',
                ],
            ],
            [
                'slug'     => 'sidebar',
                'name'     => 'Боковое меню',
                'location' => 'sidebar',
                'sort' => 30,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'panel-left',
                    'description' => 'Навигация боковой панели/категорий',
                ],
            ],
            [
                'slug'     => 'footer',
                'name'     => 'Футер: Основные ссылки',
                'location' => 'footer',
                'sort' => 40,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'columns-3',
                    'description' => 'Колонки ссылок в подвале',
                    'columns' => 3,
                ],
            ],
            [
                'slug'     => 'footer-legal',
                'name'     => 'Футер: Правовая информация',
                'location' => 'footer',
                'sort' => 50,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'scale',
                    'description' => 'Политики, оферта, реквизиты',
                ],
            ],
            [
                'slug'     => 'social',
                'name'     => 'Соцсети',
                'location' => 'custom',
                'sort' => 60,
                'activity'=> true,
                'meta'     => [
                    'icon' => 'share-2',
                    'description' => 'Ссылки на социальные сети',
                    'style' => 'icons-only',
                ],
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($menus as $m) {
            $keys = ['slug' => $m['slug']];
            $payload = [
                'name'       => $m['name'],
                'location'   => $m['location'] ?? 'header',
                'activity'   => (bool)($m['activity'] ?? true),
                'sort'   => (int)($m['sort'] ?? 0),
                'meta'       => $m['meta'] ?? null, // cast -> array
                'updated_at' => $now,
            ];

            /** @var NavigationMenu|null $existing */
            $existing = NavigationMenu::query()->where($keys)->first();

            if ($existing) {
                $existing->fill($payload)->save();
                $updated++;
            } else {
                NavigationMenu::query()->create($keys + $payload + ['created_at' => $now]);
                $created++;
            }
        }

        $this->command?->info("Navigation menus upserted: created {$created}, updated {$updated}.");
    }
}
