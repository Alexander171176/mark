<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\Page\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('pages')) {
            $this->command?->warn('Нет таблицы pages — пропускаю PageSeeder.');
            return;
        }

        $authorId = null;
        if (Schema::hasTable('users')) {
            $authorId = User::query()->orderBy('id')->value('id');
        }

        $now = now();

        /**
         * Структура дерева.
         * Единственный глобально уникальный ключ — slug.
         * Для разных локалей slug включаем префикс локали (ru/..., en/...).
         */
        $trees = [
            // Русская версия
            [
                'locale'   => 'ru',
                'pages'    => [
                    [
                        'title'       => 'Главная',
                        'slug'        => 'ru',
                        'template'    => 'pages.landing',
                        'layout'      => 'app',
                        'status'      => 'published',
                        'activity'   => true,
                        'published_at'=> $now->copy()->subDays(15),
                        'excerpt'     => 'Добро пожаловать на наш сайт.',
                        'content'     => '<h1>Главная</h1><p>Это главная страница.</p>',
                        'meta'        => [
                            'seo' => [
                                'title'       => 'Главная — Наш сайт',
                                'description' => 'Описание главной страницы на русском.',
                                'keywords'    => 'главная, курсы, обучение',
                            ],
                        ],
                        'children'    => [
                            [
                                'title'    => 'О нас',
                                'slug'     => 'ru/about',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Кто мы такие',
                                'content'  => '<h1>О нас</h1><p>Немного о компании.</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'О нас',
                                        'description' => 'Информация о компании.',
                                        'keywords'    => 'о нас, компания',
                                    ],
                                ],
                                'children' => [
                                    [
                                        'title'    => 'Команда',
                                        'slug'     => 'ru/about/team',
                                        'template' => 'pages.default',
                                        'layout'   => 'app',
                                        'status'   => 'published',
                                        'activity'=> true,
                                        'excerpt'  => 'Наша команда',
                                        'content'  => '<h1>Команда</h1><p>Знакомьтесь с нашей командой.</p>',
                                        'meta'     => [
                                            'seo' => [
                                                'title'       => 'Команда',
                                                'description' => 'Команда проекта.',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Контакты',
                                'slug'     => 'ru/contacts',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Как с нами связаться',
                                'content'  => '<h1>Контакты</h1><p>Почта: info@example.com</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Контакты',
                                        'description' => 'Свяжитесь с нами.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Блог',
                                'slug'     => 'ru/blog',
                                'template' => 'pages.blog-index',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Новости и статьи',
                                'content'  => '<h1>Блог</h1><p>Список статей выводится компонентом.</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Блог',
                                        'description' => 'Новости компании и статьи.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Политика конфиденциальности',
                                'slug'     => 'ru/privacy',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Как мы обрабатываем данные',
                                'content'  => '<h1>Политика конфиденциальности</h1><p>Текст политики...</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Политика конфиденциальности',
                                        'description' => 'Правила обработки персональных данных.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Пользовательское соглашение',
                                'slug'     => 'ru/terms',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Условия использования',
                                'content'  => '<h1>Пользовательское соглашение</h1><p>Текст соглашения...</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Пользовательское соглашение',
                                        'description' => 'Условия использования сервиса.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // Английская версия
            [
                'locale'   => 'en',
                'pages'    => [
                    [
                        'title'       => 'Home',
                        'slug'        => 'en',
                        'template'    => 'pages.landing',
                        'layout'      => 'app',
                        'status'      => 'published',
                        'activity'   => true,
                        'published_at'=> $now->copy()->subDays(15),
                        'excerpt'     => 'Welcome to our website.',
                        'content'     => '<h1>Home</h1><p>This is the landing page.</p>',
                        'meta'        => [
                            'seo' => [
                                'title'       => 'Home — Our Site',
                                'description' => 'English home page description.',
                                'keywords'    => 'home, courses, learning',
                            ],
                        ],
                        'children'    => [
                            [
                                'title'    => 'About Us',
                                'slug'     => 'en/about',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Who we are',
                                'content'  => '<h1>About</h1><p>About the company.</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'About',
                                        'description' => 'Company information.',
                                        'keywords'    => 'about, company',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Contacts',
                                'slug'     => 'en/contacts',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'How to reach us',
                                'content'  => '<h1>Contacts</h1><p>Email: info@example.com</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Contacts',
                                        'description' => 'Get in touch.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Blog',
                                'slug'     => 'en/blog',
                                'template' => 'pages.blog-index',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'News & Articles',
                                'content'  => '<h1>Blog</h1><p>Articles list is rendered by a component.</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Blog',
                                        'description' => 'Company news and articles.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Privacy Policy',
                                'slug'     => 'en/privacy',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'How we process data',
                                'content'  => '<h1>Privacy Policy</h1><p>Policy text...</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Privacy Policy',
                                        'description' => 'Personal data processing rules.',
                                    ],
                                ],
                            ],
                            [
                                'title'    => 'Terms of Service',
                                'slug'     => 'en/terms',
                                'template' => 'pages.default',
                                'layout'   => 'app',
                                'status'   => 'published',
                                'activity'=> true,
                                'excerpt'  => 'Usage terms',
                                'content'  => '<h1>Terms of Service</h1><p>Terms text...</p>',
                                'meta'     => [
                                    'seo' => [
                                        'title'       => 'Terms of Service',
                                        'description' => 'Service usage terms.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($trees as $tree) {
            $locale = $tree['locale'];
            foreach ($tree['pages'] as $rootIndex => $root) {
                $rootId = $this->upsertPage($root, null, $authorId, $locale, $rootIndex, $created, $updated);

                // Дети корня
                if (!empty($root['children'])) {
                    foreach ($root['children'] as $childIndex => $child) {
                        $childId = $this->upsertPage($child, $rootId, $authorId, $locale, $childIndex, $created, $updated);

                        // Вложенные дети
                        if (!empty($child['children'])) {
                            foreach ($child['children'] as $grandIndex => $grand) {
                                $this->upsertPage($grand, $childId, $authorId, $locale, $grandIndex, $created, $updated);
                            }
                        }
                    }
                }
            }
        }

        $this->command?->info("Pages upserted: created {$created}, updated {$updated}.");
    }

    /**
     * Создаёт/обновляет страницу по уникальному slug.
     * Возвращает id страницы.
     */
    private function upsertPage(array $data, ?int $parentId, ?int $authorId, ?string $locale, int $sort, int &$created, int &$updated): int
    {
        $now  = now();
        $slug = $data['slug'];

        $where = ['slug' => $slug];

        $payload = [
            'parent_id'    => $parentId,
            'author_id'    => $authorId,
            'title'        => $data['title'] ?? Str::title(str_replace(['-', '/'], ' ', $slug)),
            'slug'         => $slug,
            'excerpt'      => $data['excerpt']   ?? null,
            'content'      => $data['content']   ?? null,
            'status'       => $data['status']    ?? 'draft',
            'activity'     => $data['activity'] ?? true,
            'published_at' => $data['published_at'] ?? ($data['status'] ?? 'draft') === 'published' ? $now : null,
            'template'     => $data['template']  ?? null,
            'layout'       => $data['layout']    ?? null,
            'locale'       => $locale,
            'sort'         => $sort,
            'meta'         => $data['meta']      ?? null,
        ];

        /** @var Page|null $model */
        $model = Page::query()->where($where)->first();

        if ($model) {
            $model->fill($payload);
            // не трогаем created_at, но обновим updated_at
            $model->updated_at = $now;
            $model->save();
            $updated++;
        } else {
            $model = new Page(array_merge($where, $payload));
            $model->created_at = $now;
            $model->updated_at = $now;
            $model->save();
            $created++;
        }

        return (int) $model->id;
    }
}
