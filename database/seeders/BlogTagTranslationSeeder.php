<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogTag\BlogTagTranslation;
use Illuminate\Database\Seeder;

class BlogTagTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'php' => [
                'ru' => [
                    'name' => 'PHP',
                    'subtitle' => 'Серверный язык программирования',
                    'short' => 'Тег для материалов по PHP и backend-разработке.',
                    'description' => 'Материалы по PHP, архитектуре приложений, ООП, backend-разработке и серверной логике.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, backend, oop, web development',
                    'meta_desc' => 'Материалы по PHP и серверной разработке.',
                ],
                'en' => [
                    'name' => 'PHP',
                    'subtitle' => 'Server-side programming language',
                    'short' => 'Tag for content about PHP and backend development.',
                    'description' => 'Content about PHP, application architecture, OOP, backend development and server-side logic.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, backend, oop, web development',
                    'meta_desc' => 'Content about PHP and server-side development.',
                ],
                'kk' => [
                    'name' => 'PHP',
                    'subtitle' => 'Серверлік бағдарламалау тілі',
                    'short' => 'PHP және backend әзірлеу материалдарына арналған тег.',
                    'description' => 'PHP, қолданба архитектурасы, ООП, backend әзірлеу және серверлік логика туралы материалдар.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, backend, oop, web development',
                    'meta_desc' => 'PHP және серверлік әзірлеу туралы материалдар.',
                ],
            ],

            'laravel' => [
                'ru' => [
                    'name' => 'Laravel',
                    'subtitle' => 'PHP-фреймворк',
                    'short' => 'Тег для материалов по Laravel, MVC и Eloquent.',
                    'description' => 'Материалы по Laravel, маршрутам, контроллерам, реквестам, ресурсам, Eloquent и архитектуре приложений.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php, mvc, eloquent',
                    'meta_desc' => 'Материалы по Laravel и архитектуре веб-приложений.',
                ],
                'en' => [
                    'name' => 'Laravel',
                    'subtitle' => 'PHP framework',
                    'short' => 'Tag for content about Laravel, MVC and Eloquent.',
                    'description' => 'Content about Laravel, routing, controllers, requests, resources, Eloquent and application architecture.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php, mvc, eloquent',
                    'meta_desc' => 'Content about Laravel and web application architecture.',
                ],
                'kk' => [
                    'name' => 'Laravel',
                    'subtitle' => 'PHP фреймворкі',
                    'short' => 'Laravel, MVC және Eloquent туралы материалдарға арналған тег.',
                    'description' => 'Laravel, маршруттар, контроллерлер, реквесттер, ресурстар, Eloquent және қолданба архитектурасы туралы материалдар.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php, mvc, eloquent',
                    'meta_desc' => 'Laravel және веб-қосымша архитектурасы туралы материалдар.',
                ],
            ],

            'vue-js' => [
                'ru' => [
                    'name' => 'Vue.js',
                    'subtitle' => 'Фреймворк для интерфейсов',
                    'short' => 'Тег для материалов по Vue.js и SPA.',
                    'description' => 'Материалы по Vue.js, компонентам, реактивности, form-логике, composables и SPA-приложениям.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, spa, frontend',
                    'meta_desc' => 'Материалы по Vue.js и frontend-разработке.',
                ],
                'en' => [
                    'name' => 'Vue.js',
                    'subtitle' => 'Framework for interfaces',
                    'short' => 'Tag for content about Vue.js and SPAs.',
                    'description' => 'Content about Vue.js, components, reactivity, form logic, composables and SPA applications.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, spa, frontend',
                    'meta_desc' => 'Content about Vue.js and frontend development.',
                ],
                'kk' => [
                    'name' => 'Vue.js',
                    'subtitle' => 'Интерфейстерге арналған фреймворк',
                    'short' => 'Vue.js және SPA туралы материалдарға арналған тег.',
                    'description' => 'Vue.js, компоненттер, реактивтілік, форма логикасы, composables және SPA қосымшалары туралы материалдар.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, spa, frontend',
                    'meta_desc' => 'Vue.js және frontend әзірлеу туралы материалдар.',
                ],
            ],

            'javascript' => [
                'ru' => [
                    'name' => 'JavaScript',
                    'subtitle' => 'Язык веб-разработки',
                    'short' => 'Тег для материалов по JavaScript и клиентской логике.',
                    'description' => 'Материалы по JavaScript, DOM, async/await, fetch, модулям и логике frontend-приложений.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async, frontend',
                    'meta_desc' => 'Материалы по JavaScript и веб-разработке.',
                ],
                'en' => [
                    'name' => 'JavaScript',
                    'subtitle' => 'Language of web development',
                    'short' => 'Tag for content about JavaScript and client-side logic.',
                    'description' => 'Content about JavaScript, DOM, async/await, fetch, modules and frontend application logic.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async, frontend',
                    'meta_desc' => 'Content about JavaScript and web development.',
                ],
                'kk' => [
                    'name' => 'JavaScript',
                    'subtitle' => 'Веб-әзірлеу тілі',
                    'short' => 'JavaScript және клиенттік логика туралы материалдарға арналған тег.',
                    'description' => 'JavaScript, DOM, async/await, fetch, модульдер және frontend логикасы туралы материалдар.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async, frontend',
                    'meta_desc' => 'JavaScript және веб-әзірлеу туралы материалдар.',
                ],
            ],

            'tailwind-css' => [
                'ru' => [
                    'name' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first CSS',
                    'short' => 'Тег для материалов по Tailwind CSS и UI.',
                    'description' => 'Материалы по Tailwind CSS, адаптивным интерфейсам, utility-first подходу и современному UI.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, ui, utility first',
                    'meta_desc' => 'Материалы по Tailwind CSS и интерфейсам.',
                ],
                'en' => [
                    'name' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first CSS',
                    'short' => 'Tag for content about Tailwind CSS and UI.',
                    'description' => 'Content about Tailwind CSS, responsive interfaces, utility-first workflow and modern UI.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, ui, utility first',
                    'meta_desc' => 'Content about Tailwind CSS and interfaces.',
                ],
                'kk' => [
                    'name' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first CSS',
                    'short' => 'Tailwind CSS және UI туралы материалдарға арналған тег.',
                    'description' => 'Tailwind CSS, бейімделгіш интерфейстер, utility-first тәсілі және заманауи UI туралы материалдар.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, ui, utility first',
                    'meta_desc' => 'Tailwind CSS және интерфейстер туралы материалдар.',
                ],
            ],

            'docker' => [
                'ru' => [
                    'name' => 'Docker',
                    'subtitle' => 'Контейнеризация',
                    'short' => 'Тег для материалов по Docker и контейнерам.',
                    'description' => 'Материалы по Docker, контейнеризации, образам, volumes, networks и локальной разработке.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops',
                    'meta_desc' => 'Материалы по Docker и контейнеризации.',
                ],
                'en' => [
                    'name' => 'Docker',
                    'subtitle' => 'Containerization',
                    'short' => 'Tag for content about Docker and containers.',
                    'description' => 'Content about Docker, containerization, images, volumes, networks and local development.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops',
                    'meta_desc' => 'Content about Docker and containerization.',
                ],
                'kk' => [
                    'name' => 'Docker',
                    'subtitle' => 'Контейнерлеу',
                    'short' => 'Docker және контейнерлер туралы материалдарға арналған тег.',
                    'description' => 'Docker, контейнерлеу, образдар, volumes, networks және локалды әзірлеу туралы материалдар.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops',
                    'meta_desc' => 'Docker және контейнерлеу туралы материалдар.',
                ],
            ],

            'mysql' => [
                'ru' => [
                    'name' => 'MySQL',
                    'subtitle' => 'Реляционная база данных',
                    'short' => 'Тег для материалов по MySQL и SQL.',
                    'description' => 'Материалы по MySQL, SQL-запросам, индексам, миграциям и проектированию таблиц.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, database',
                    'meta_desc' => 'Материалы по MySQL и SQL.',
                ],
                'en' => [
                    'name' => 'MySQL',
                    'subtitle' => 'Relational database',
                    'short' => 'Tag for content about MySQL and SQL.',
                    'description' => 'Content about MySQL, SQL queries, indexes, migrations and schema design.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, database',
                    'meta_desc' => 'Content about MySQL and SQL.',
                ],
                'kk' => [
                    'name' => 'MySQL',
                    'subtitle' => 'Реляциялық дерекқор',
                    'short' => 'MySQL және SQL туралы материалдарға арналған тег.',
                    'description' => 'MySQL, SQL сұраныстары, индекстер, миграциялар және кесте жобалау туралы материалдар.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, database',
                    'meta_desc' => 'MySQL және SQL туралы материалдар.',
                ],
            ],

            'postgresql' => [
                'ru' => [
                    'name' => 'PostgreSQL',
                    'subtitle' => 'Надёжная SQL база данных',
                    'short' => 'Тег для материалов по PostgreSQL и производительности.',
                    'description' => 'Материалы по PostgreSQL, индексам, JSONB, сложным запросам и производительности.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, performance',
                    'meta_desc' => 'Материалы по PostgreSQL и производительности.',
                ],
                'en' => [
                    'name' => 'PostgreSQL',
                    'subtitle' => 'Reliable SQL database',
                    'short' => 'Tag for content about PostgreSQL and performance.',
                    'description' => 'Content about PostgreSQL, indexes, JSONB, advanced queries and performance.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, performance',
                    'meta_desc' => 'Content about PostgreSQL and performance.',
                ],
                'kk' => [
                    'name' => 'PostgreSQL',
                    'subtitle' => 'Сенімді SQL дерекқоры',
                    'short' => 'PostgreSQL және өнімділік туралы материалдарға арналған тег.',
                    'description' => 'PostgreSQL, индекстер, JSONB, күрделі сұраныстар және өнімділік туралы материалдар.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, performance',
                    'meta_desc' => 'PostgreSQL және өнімділік туралы материалдар.',
                ],
            ],

            'api' => [
                'ru' => [
                    'name' => 'API',
                    'subtitle' => 'Интеграции и интерфейсы обмена',
                    'short' => 'Тег для материалов по REST API и интеграциям.',
                    'description' => 'Материалы по API, REST, JSON, внешним сервисам, аутентификации и интеграциям между системами.',
                    'meta_title' => 'API',
                    'meta_keywords' => 'api, rest, json, integration',
                    'meta_desc' => 'Материалы по API и интеграциям.',
                ],
                'en' => [
                    'name' => 'API',
                    'subtitle' => 'Integrations and interfaces',
                    'short' => 'Tag for content about REST APIs and integrations.',
                    'description' => 'Content about APIs, REST, JSON, external services, authentication and system integrations.',
                    'meta_title' => 'API',
                    'meta_keywords' => 'api, rest, json, integration',
                    'meta_desc' => 'Content about APIs and integrations.',
                ],
                'kk' => [
                    'name' => 'API',
                    'subtitle' => 'Интеграциялар және интерфейстер',
                    'short' => 'REST API және интеграциялар туралы материалдарға арналған тег.',
                    'description' => 'API, REST, JSON, сыртқы сервистер, аутентификация және жүйелер арасындағы интеграциялар туралы материалдар.',
                    'meta_title' => 'API',
                    'meta_keywords' => 'api, rest, json, integration',
                    'meta_desc' => 'API және интеграциялар туралы материалдар.',
                ],
            ],

            'devops' => [
                'ru' => [
                    'name' => 'DevOps',
                    'subtitle' => 'Автоматизация и инфраструктура',
                    'short' => 'Тег для материалов по DevOps, деплою и инфраструктуре.',
                    'description' => 'Материалы по DevOps, окружениям, автоматизации развёртывания, мониторингу и стабильности проектов.',
                    'meta_title' => 'DevOps',
                    'meta_keywords' => 'devops, deployment, infrastructure, automation',
                    'meta_desc' => 'Материалы по DevOps и инфраструктуре.',
                ],
                'en' => [
                    'name' => 'DevOps',
                    'subtitle' => 'Automation and infrastructure',
                    'short' => 'Tag for content about DevOps, deployment and infrastructure.',
                    'description' => 'Content about DevOps, environments, deployment automation, monitoring and project stability.',
                    'meta_title' => 'DevOps',
                    'meta_keywords' => 'devops, deployment, infrastructure, automation',
                    'meta_desc' => 'Content about DevOps and infrastructure.',
                ],
                'kk' => [
                    'name' => 'DevOps',
                    'subtitle' => 'Автоматтандыру және инфрақұрылым',
                    'short' => 'DevOps, деплой және инфрақұрылым туралы материалдарға арналған тег.',
                    'description' => 'DevOps, орталар, жариялауды автоматтандыру, мониторинг және жоба тұрақтылығы туралы материалдар.',
                    'meta_title' => 'DevOps',
                    'meta_keywords' => 'devops, deployment, infrastructure, automation',
                    'meta_desc' => 'DevOps және инфрақұрылым туралы материалдар.',
                ],
            ],
        ];

        $tags = BlogTag::query()->get()->keyBy('slug');

        foreach ($translations as $slug => $items) {
            $tag = $tags->get($slug);

            if (!$tag) {
                continue;
            }

            foreach ($items as $locale => $data) {
                BlogTagTranslation::updateOrCreate(
                    [
                        'tag_id' => $tag->id,
                        'locale' => $locale,
                    ],
                    $data
                );
            }
        }
    }
}
