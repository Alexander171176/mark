<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\NavigationItem\NavigationItem;
use App\Models\Admin\Constructor\NavigationMenu\NavigationMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NavigationItemSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('navigation_items') || !Schema::hasTable('navigation_menus')) {
            $this->command?->warn('Нет таблиц navigation_items/navigation_menus — пропускаю NavigationItemSeeder.');
            return;
        }

        // Ищем меню по слагам, созданным NavigationMenuSeeder
        $slugs = ['header','mobile','utility','sidebar','footer','footer-legal','social'];
        $menus = NavigationMenu::query()
            ->whereIn('slug', $slugs)
            ->get(['id','slug'])
            ->keyBy('slug');

        // Если не нашли хотя бы одно — подскажем
        foreach ($slugs as $slug) {
            if (!isset($menus[$slug])) {
                $this->command?->warn("Меню со слагом '{$slug}' не найдено. Запусти NavigationMenuSeeder и повтори.");
            }
        }
        if ($menus->isEmpty()) return;

        $now = now();
        $created = 0;
        $updated = 0;

        // Деревья пунктов по меню
        $trees = [
            'header' => [
                // sort задаётся порядком в массиве
                [
                    'title' => 'Главная',
                    'type'  => 'internal',
                    'url'   => '/',
                    'icon'  => 'home',
                    'target'=> '_self',
                    'children' => [],
                ],
                [
                    'title' => 'Курсы',
                    'type'  => 'internal',
                    'url'   => '/courses',
                    'icon'  => 'graduation-cap',
                    'children' => [
                        ['title' => 'Все курсы', 'type'=>'internal', 'url'=>'/courses', 'icon'=>null],
                        ['title' => 'Категории', 'type'=>'internal', 'url'=>'/categories', 'icon'=>null],
                        ['title' => 'Преподаватели', 'type'=>'internal', 'url'=>'/instructors', 'icon'=>null],
                    ],
                ],
                [
                    'title' => 'Блог',
                    'type'  => 'internal',
                    'url'   => '/blog',
                    'icon'  => 'newspaper',
                    'children' => [
                        ['title'=>'Новости','type'=>'internal','url'=>'/blog/news'],
                        ['title'=>'Статьи','type'=>'internal','url'=>'/blog/articles'],
                    ],
                ],
                [
                    'title' => 'О платформе',
                    'type'  => 'internal',
                    'url'   => '/about',
                    'icon'  => 'info',
                ],
                [
                    'title' => 'Цены',
                    'type'  => 'internal',
                    'url'   => '/pricing',
                    'icon'  => 'coins',
                ],
                [
                    'title' => 'Контакты',
                    'type'  => 'internal',
                    'url'   => '/contacts',
                    'icon'  => 'phone',
                ],
            ],

            'mobile' => [
                ['title'=>'Главная', 'type'=>'internal', 'url'=>'/'],
                ['title'=>'Курсы',  'type'=>'internal', 'url'=>'/courses'],
                ['title'=>'Блог',   'type'=>'internal', 'url'=>'/blog'],
                ['title'=>'Цены',   'type'=>'internal', 'url'=>'/pricing'],
                ['title'=>'Контакты','type'=>'internal','url'=>'/contacts'],
            ],

            'utility' => [
                ['title'=>'Вход',     'type'=>'internal', 'url'=>'/login',    'icon'=>'log-in'],
                ['title'=>'Регистрация','type'=>'internal','url'=>'/register', 'icon'=>'user-plus'],
                ['title'=>'RU', 'type'=>'custom', 'url'=>'/?lang=ru', 'meta'=>['locale'=>'ru']],
                ['title'=>'EN', 'type'=>'custom', 'url'=>'/?lang=en', 'meta'=>['locale'=>'en']],
            ],

            'sidebar' => [
                ['title'=>'Личный кабинет','type'=>'internal','url'=>'/dashboard','icon'=>'gauge'],
                ['title'=>'Мои курсы',    'type'=>'internal','url'=>'/my/courses','icon'=>'bookmark'],
                ['title'=>'Закладки',     'type'=>'internal','url'=>'/bookmarks','icon'=>'star'],
                ['title'=>'Настройки',    'type'=>'internal','url'=>'/settings','icon'=>'settings'],
            ],

            'footer' => [
                [
                    'title'=>'О нас', 'type'=>'custom', 'url'=>null, 'icon'=>null,
                    'children'=>[
                        ['title'=>'О платформе', 'type'=>'internal','url'=>'/about'],
                        ['title'=>'Команда','type'=>'internal','url'=>'/team'],
                        ['title'=>'Контакты','type'=>'internal','url'=>'/contacts'],
                    ],
                ],
                [
                    'title'=>'Студентам', 'type'=>'custom', 'url'=>null,
                    'children'=>[
                        ['title'=>'Каталог курсов','type'=>'internal','url'=>'/courses'],
                        ['title'=>'Вопросы и ответы','type'=>'internal','url'=>'/help'],
                        ['title'=>'Оплата и возвраты','type'=>'internal','url'=>'/payments'],
                    ],
                ],
                [
                    'title'=>'Партнёрам', 'type'=>'custom', 'url'=>null,
                    'children'=>[
                        ['title'=>'Для компаний','type'=>'internal','url'=>'/b2b'],
                        ['title'=>'Стать преподавателем','type'=>'internal','url'=>'/become-instructor'],
                    ],
                ],
            ],

            'footer-legal' => [
                ['title'=>'Пользовательское соглашение','type'=>'internal','url'=>'/terms'],
                ['title'=>'Политика конфиденциальности','type'=>'internal','url'=>'/privacy'],
                ['title'=>'Публичная оферта','type'=>'internal','url'=>'/offer'],
                ['title'=>'Реквизиты','type'=>'internal','url'=>'/legal'],
            ],

            'social' => [
                ['title'=>'YouTube','type'=>'custom','url'=>'https://youtube.com/','target'=>'_blank','icon'=>'youtube','meta'=>['rel'=>'me']],
                ['title'=>'Telegram','type'=>'custom','url'=>'https://t.me/','target'=>'_blank','icon'=>'telegram','meta'=>['rel'=>'me']],
                ['title'=>'Instagram','type'=>'custom','url'=>'https://instagram.com/','target'=>'_blank','icon'=>'instagram','meta'=>['rel'=>'me']],
                ['title'=>'LinkedIn','type'=>'custom','url'=>'https://linkedin.com/','target'=>'_blank','icon'=>'linkedin','meta'=>['rel'=>'me']],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($trees as $slug => $items) {
                if (!isset($menus[$slug])) continue;
                $menuId = (int)$menus[$slug]->id;

                // очищаем текущие пункты этого меню (мягко, только для выбранного меню)
                NavigationItem::query()->where('menu_id', $menuId)->delete();

                // создаём заново дерево
                $this->createTree($menuId, $items, null, $created, $updated, $now);
            }

            DB::commit();
            $this->command?->info("Navigation items upserted: created {$created}, updated {$updated}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('NavigationItemSeeder failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Рекурсивное создание дерева пунктов.
     *
     * @param int $menuId
     * @param array $items
     * @param int|null $parentId
     * @param int &$created
     * @param int &$updated
     * @param \Illuminate\Support\Carbon $now
     * @return void
     */
    private function createTree(int $menuId, array $items, ?int $parentId, int &$created, int &$updated, $now): void
    {
        $sort = 0;

        foreach ($items as $node) {
            $sort++;

            $payload = [
                'menu_id'      => $menuId,
                'parent_id'    => $parentId,
                'title'        => (string)($node['title'] ?? 'Пункт'),
                'type'         => (string)($node['type']  ?? 'custom'),
                'url'          => $node['url']          ?? null,
                'route_name'   => $node['route_name']   ?? null,
                'route_params' => $node['route_params'] ?? null, // cast -> array
                'target'       => (string)($node['target'] ?? '_self'),
                'icon'         => $node['icon'] ?? null,
                'activity'    => (bool)($node['activity'] ?? true),
                'sort'     => (int)($node['sort'] ?? $sort),
                'meta'         => $node['meta'] ?? null, // cast -> array
                'updated_at'   => $now,
            ];

            // Идемпотентность внутри одного посева:
            // ключ считаем по (menu_id, parent_id, title)
            $where = [
                'menu_id'   => $menuId,
                'parent_id' => $parentId,
                'title'     => $payload['title'],
            ];

            /** @var NavigationItem|null $existing */
            $existing = NavigationItem::query()->where($where)->first();

            if ($existing) {
                $existing->fill($payload)->save();
                $id = (int)$existing->id;
                $updated++;
            } else {
                $createdModel = NavigationItem::query()->create($where + $payload + ['created_at' => $now]);
                $id = (int)$createdModel->id;
                $created++;
            }

            // дети
            if (!empty($node['children']) && is_array($node['children'])) {
                $this->createTree($menuId, $node['children'], $id, $created, $updated, $now);
            }
        }
    }
}
