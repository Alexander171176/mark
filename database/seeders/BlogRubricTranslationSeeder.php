<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogRubric\BlogRubricTranslation;
use Illuminate\Database\Seeder;

class BlogRubricTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'frontend-development' => [
                'ru' => [
                    'title' => 'Frontend разработка',
                    'subtitle' => 'Интерфейсы и клиентская логика',
                    'short' => 'Материалы по HTML, CSS, JavaScript и современным фронтенд-фреймворкам.',
                    'description' => 'Раздел посвящён frontend разработке: вёрстке, адаптивным интерфейсам, взаимодействию с API и построению современных SPA приложений.',
                    'meta_title' => 'Frontend разработка',
                    'meta_keywords' => 'frontend, html, css, javascript, vue',
                    'meta_desc' => 'Статьи и материалы по frontend разработке, интерфейсам и клиентской логике.',
                ],
                'en' => [
                    'title' => 'Frontend Development',
                    'subtitle' => 'Interfaces and client-side logic',
                    'short' => 'Content about HTML, CSS, JavaScript and modern frontend frameworks.',
                    'description' => 'This section is dedicated to frontend development: markup, responsive UI, API integration and modern SPA applications.',
                    'meta_title' => 'Frontend Development',
                    'meta_keywords' => 'frontend, html, css, javascript, vue',
                    'meta_desc' => 'Articles and materials about frontend development, interfaces and client-side logic.',
                ],
                'kk' => [
                    'title' => 'Frontend әзірлеу',
                    'subtitle' => 'Интерфейстер және клиенттік логика',
                    'short' => 'HTML, CSS, JavaScript және заманауи frontend фреймворктары туралы материалдар.',
                    'description' => 'Бұл бөлім frontend әзірлеуге арналған: беттеу, бейімделгіш интерфейстер, API-мен жұмыс және заманауи SPA қосымшалары.',
                    'meta_title' => 'Frontend әзірлеу',
                    'meta_keywords' => 'frontend, html, css, javascript, vue',
                    'meta_desc' => 'Frontend әзірлеу, интерфейстер және клиенттік логика туралы мақалалар мен материалдар.',
                ],
            ],

            'backend-development' => [
                'ru' => [
                    'title' => 'Backend разработка',
                    'subtitle' => 'Серверная логика и архитектура',
                    'short' => 'Материалы по PHP, API, базам данных и серверной архитектуре.',
                    'description' => 'Раздел о backend разработке: проектировании API, бизнес-логике, безопасности, очередях, кешировании и интеграциях.',
                    'meta_title' => 'Backend разработка',
                    'meta_keywords' => 'backend, php, api, laravel, database',
                    'meta_desc' => 'Материалы по backend разработке, API и серверной архитектуре.',
                ],
                'en' => [
                    'title' => 'Backend Development',
                    'subtitle' => 'Server-side logic and architecture',
                    'short' => 'Content about PHP, APIs, databases and server architecture.',
                    'description' => 'A section about backend development: API design, business logic, security, queues, caching and integrations.',
                    'meta_title' => 'Backend Development',
                    'meta_keywords' => 'backend, php, api, laravel, database',
                    'meta_desc' => 'Content about backend development, APIs and server architecture.',
                ],
                'kk' => [
                    'title' => 'Backend әзірлеу',
                    'subtitle' => 'Серверлік логика және архитектура',
                    'short' => 'PHP, API, дерекқорлар және серверлік архитектура туралы материалдар.',
                    'description' => 'Backend әзірлеу бөлімі: API жобалау, бизнес-логика, қауіпсіздік, кезектер, кэштеу және интеграциялар.',
                    'meta_title' => 'Backend әзірлеу',
                    'meta_keywords' => 'backend, php, api, laravel, database',
                    'meta_desc' => 'Backend әзірлеу, API және серверлік архитектура туралы материалдар.',
                ],
            ],

            'devops-and-infrastructure' => [
                'ru' => [
                    'title' => 'DevOps и инфраструктура',
                    'subtitle' => 'Развёртывание и автоматизация',
                    'short' => 'Материалы по Docker, CI/CD, серверам и инфраструктуре.',
                    'description' => 'Раздел о DevOps практиках: контейнеризации, автоматической доставке, конфигурации окружений и мониторинге.',
                    'meta_title' => 'DevOps и инфраструктура',
                    'meta_keywords' => 'devops, docker, ci cd, infrastructure',
                    'meta_desc' => 'Материалы по DevOps, инфраструктуре и автоматизации развёртывания.',
                ],
                'en' => [
                    'title' => 'DevOps and Infrastructure',
                    'subtitle' => 'Deployment and automation',
                    'short' => 'Content about Docker, CI/CD, servers and infrastructure.',
                    'description' => 'A section about DevOps practices: containerization, delivery pipelines, environment configuration and monitoring.',
                    'meta_title' => 'DevOps and Infrastructure',
                    'meta_keywords' => 'devops, docker, ci cd, infrastructure',
                    'meta_desc' => 'Content about DevOps, infrastructure and deployment automation.',
                ],
                'kk' => [
                    'title' => 'DevOps және инфрақұрылым',
                    'subtitle' => 'Жариялау және автоматтандыру',
                    'short' => 'Docker, CI/CD, серверлер және инфрақұрылым туралы материалдар.',
                    'description' => 'DevOps тәжірибелері: контейнерлеу, жеткізу процестері, орта баптауы және мониторинг туралы бөлім.',
                    'meta_title' => 'DevOps және инфрақұрылым',
                    'meta_keywords' => 'devops, docker, ci cd, infrastructure',
                    'meta_desc' => 'DevOps, инфрақұрылым және жариялауды автоматтандыру туралы материалдар.',
                ],
            ],

            'html-and-css' => [
                'ru' => [
                    'title' => 'HTML и CSS',
                    'subtitle' => 'База фронтенда',
                    'short' => 'Основы вёрстки, семантики и стилизации интерфейсов.',
                    'description' => 'Рубрика о фундаменте frontend разработки: семантическая разметка, flex, grid, адаптивность и стилизация.',
                    'meta_title' => 'HTML и CSS',
                    'meta_keywords' => 'html, css, flex, grid, responsive',
                    'meta_desc' => 'Материалы по HTML и CSS: вёрстка, адаптивность и стилизация интерфейсов.',
                ],
                'en' => [
                    'title' => 'HTML and CSS',
                    'subtitle' => 'Frontend fundamentals',
                    'short' => 'Basics of markup, semantics and interface styling.',
                    'description' => 'A category about the foundation of frontend development: semantic markup, flex, grid, responsive layouts and styling.',
                    'meta_title' => 'HTML and CSS',
                    'meta_keywords' => 'html, css, flex, grid, responsive',
                    'meta_desc' => 'Content about HTML and CSS: markup, responsiveness and interface styling.',
                ],
                'kk' => [
                    'title' => 'HTML және CSS',
                    'subtitle' => 'Frontend негіздері',
                    'short' => 'Белгілеу, семантика және интерфейсті безендіру негіздері.',
                    'description' => 'Frontend әзірлеудің негізі: семантикалық белгілеу, flex, grid, адаптивтілік және стильдеу туралы айдар.',
                    'meta_title' => 'HTML және CSS',
                    'meta_keywords' => 'html, css, flex, grid, responsive',
                    'meta_desc' => 'HTML және CSS туралы материалдар: беттеу, бейімделгіштік және интерфейс дизайны.',
                ],
            ],

            'javascript' => [
                'ru' => [
                    'title' => 'JavaScript',
                    'subtitle' => 'Язык веб-разработки',
                    'short' => 'Статьи о современном JavaScript, DOM, асинхронности и приложениях.',
                    'description' => 'Рубрика посвящена JavaScript: синтаксис, работа с DOM, fetch, async/await, модули и архитектура фронтенда.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async await, fetch',
                    'meta_desc' => 'Материалы по JavaScript, DOM и современной веб-разработке.',
                ],
                'en' => [
                    'title' => 'JavaScript',
                    'subtitle' => 'The language of web development',
                    'short' => 'Articles about modern JavaScript, DOM, async code and applications.',
                    'description' => 'This category is dedicated to JavaScript: syntax, DOM manipulation, fetch, async/await, modules and frontend architecture.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async await, fetch',
                    'meta_desc' => 'Content about JavaScript, DOM and modern web development.',
                ],
                'kk' => [
                    'title' => 'JavaScript',
                    'subtitle' => 'Веб-әзірлеу тілі',
                    'short' => 'Заманауи JavaScript, DOM, асинхронды код және қосымшалар туралы мақалалар.',
                    'description' => 'Бұл айдар JavaScript-ке арналған: синтаксис, DOM, fetch, async/await, модульдер және frontend архитектурасы.',
                    'meta_title' => 'JavaScript',
                    'meta_keywords' => 'javascript, dom, async await, fetch',
                    'meta_desc' => 'JavaScript, DOM және заманауи веб-әзірлеу туралы материалдар.',
                ],
            ],

            'php' => [
                'ru' => [
                    'title' => 'PHP',
                    'subtitle' => 'Серверная разработка на PHP',
                    'short' => 'Материалы по PHP, ООП, архитектуре приложений и backend-разработке.',
                    'description' => 'Рубрика о PHP: базовый синтаксис, объектно-ориентированное программирование, паттерны и серверные приложения.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, oop, backend, patterns',
                    'meta_desc' => 'Материалы по PHP, backend-разработке и архитектуре приложений.',
                ],
                'en' => [
                    'title' => 'PHP',
                    'subtitle' => 'Server-side development with PHP',
                    'short' => 'Content about PHP, OOP, application architecture and backend development.',
                    'description' => 'A category about PHP: basic syntax, object-oriented programming, patterns and server-side applications.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, oop, backend, patterns',
                    'meta_desc' => 'Content about PHP, backend development and application architecture.',
                ],
                'kk' => [
                    'title' => 'PHP',
                    'subtitle' => 'PHP тіліндегі серверлік әзірлеу',
                    'short' => 'PHP, ООП, қолданба архитектурасы және backend әзірлеу туралы материалдар.',
                    'description' => 'PHP туралы айдар: негізгі синтаксис, объектіге бағытталған бағдарламалау, паттерндер және серверлік қосымшалар.',
                    'meta_title' => 'PHP',
                    'meta_keywords' => 'php, oop, backend, patterns',
                    'meta_desc' => 'PHP, backend әзірлеу және қолданба архитектурасы туралы материалдар.',
                ],
            ],

            'databases' => [
                'ru' => [
                    'title' => 'Базы данных',
                    'subtitle' => 'Хранение и обработка данных',
                    'short' => 'Материалы по SQL, проектированию таблиц, индексам и оптимизации запросов.',
                    'description' => 'Рубрика о базах данных: реляционные СУБД, проектирование схем, индексы, миграции и оптимизация.',
                    'meta_title' => 'Базы данных',
                    'meta_keywords' => 'database, sql, mysql, postgresql, indexes',
                    'meta_desc' => 'Материалы по базам данных, SQL и проектированию схем.',
                ],
                'en' => [
                    'title' => 'Databases',
                    'subtitle' => 'Data storage and processing',
                    'short' => 'Content about SQL, schema design, indexes and query optimization.',
                    'description' => 'A category about databases: relational systems, schema design, indexes, migrations and optimization.',
                    'meta_title' => 'Databases',
                    'meta_keywords' => 'database, sql, mysql, postgresql, indexes',
                    'meta_desc' => 'Content about databases, SQL and schema design.',
                ],
                'kk' => [
                    'title' => 'Дерекқорлар',
                    'subtitle' => 'Деректерді сақтау және өңдеу',
                    'short' => 'SQL, кесте жобалау, индекстер және сұраныстарды оңтайландыру туралы материалдар.',
                    'description' => 'Дерекқорлар туралы айдар: реляциялық жүйелер, схема жобалау, индекстер, миграциялар және оңтайландыру.',
                    'meta_title' => 'Дерекқорлар',
                    'meta_keywords' => 'database, sql, mysql, postgresql, indexes',
                    'meta_desc' => 'Дерекқорлар, SQL және схема жобалау туралы материалдар.',
                ],
            ],

            'docker' => [
                'ru' => [
                    'title' => 'Docker',
                    'subtitle' => 'Контейнеризация приложений',
                    'short' => 'Материалы по Docker, образам, контейнерам и окружениям разработки.',
                    'description' => 'Раздел по Docker: контейнеризация приложений, образы, volumes, networks и локальная разработка.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops, images',
                    'meta_desc' => 'Материалы по Docker, контейнерам и окружениям разработки.',
                ],
                'en' => [
                    'title' => 'Docker',
                    'subtitle' => 'Application containerization',
                    'short' => 'Content about Docker, images, containers and development environments.',
                    'description' => 'A section about Docker: application containerization, images, volumes, networks and local development.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops, images',
                    'meta_desc' => 'Content about Docker, containers and development environments.',
                ],
                'kk' => [
                    'title' => 'Docker',
                    'subtitle' => 'Қосымшаларды контейнерлеу',
                    'short' => 'Docker, образдар, контейнерлер және әзірлеу орталары туралы материалдар.',
                    'description' => 'Docker бөлімі: қосымшаларды контейнерлеу, образдар, volumes, networks және локалды әзірлеу.',
                    'meta_title' => 'Docker',
                    'meta_keywords' => 'docker, containers, devops, images',
                    'meta_desc' => 'Docker, контейнерлер және әзірлеу орталары туралы материалдар.',
                ],
            ],

            'ci-cd' => [
                'ru' => [
                    'title' => 'CI/CD',
                    'subtitle' => 'Автоматизация доставки',
                    'short' => 'Материалы по автоматической сборке, тестированию и доставке приложений.',
                    'description' => 'Рубрика о CI/CD: пайплайны, тестирование, автоматическая сборка и выкладка приложений.',
                    'meta_title' => 'CI/CD',
                    'meta_keywords' => 'ci cd, pipeline, github actions, deployment',
                    'meta_desc' => 'Материалы по CI/CD, пайплайнам и автоматизации доставки.',
                ],
                'en' => [
                    'title' => 'CI/CD',
                    'subtitle' => 'Delivery automation',
                    'short' => 'Content about automated build, testing and delivery pipelines.',
                    'description' => 'A category about CI/CD: pipelines, testing, automated builds and application delivery.',
                    'meta_title' => 'CI/CD',
                    'meta_keywords' => 'ci cd, pipeline, github actions, deployment',
                    'meta_desc' => 'Content about CI/CD, pipelines and delivery automation.',
                ],
                'kk' => [
                    'title' => 'CI/CD',
                    'subtitle' => 'Жеткізуді автоматтандыру',
                    'short' => 'Қосымшаларды автоматты жинау, тестілеу және жеткізу туралы материалдар.',
                    'description' => 'CI/CD айдары: pipeline, тестілеу, автоматты жинау және қосымшаларды жариялау.',
                    'meta_title' => 'CI/CD',
                    'meta_keywords' => 'ci cd, pipeline, github actions, deployment',
                    'meta_desc' => 'CI/CD, pipeline және жеткізуді автоматтандыру туралы материалдар.',
                ],
            ],

            'vue-js' => [
                'ru' => [
                    'title' => 'Vue.js',
                    'subtitle' => 'Фреймворк для интерфейсов',
                    'short' => 'Материалы по Vue.js, компонентам, реактивности и SPA.',
                    'description' => 'Рубрика о Vue.js: компоненты, props, emits, computed, composables, маршрутизация и состояние.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, components, spa',
                    'meta_desc' => 'Материалы по Vue.js, компонентам и SPA приложениями.',
                ],
                'en' => [
                    'title' => 'Vue.js',
                    'subtitle' => 'Framework for interfaces',
                    'short' => 'Content about Vue.js, components, reactivity and SPAs.',
                    'description' => 'A category about Vue.js: components, props, emits, computed, composables, routing and state.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, components, spa',
                    'meta_desc' => 'Content about Vue.js, components and SPA applications.',
                ],
                'kk' => [
                    'title' => 'Vue.js',
                    'subtitle' => 'Интерфейстерге арналған фреймворк',
                    'short' => 'Vue.js, компоненттер, реактивтілік және SPA туралы материалдар.',
                    'description' => 'Vue.js туралы айдар: компоненттер, props, emits, computed, composables, маршрутизация және күй.',
                    'meta_title' => 'Vue.js',
                    'meta_keywords' => 'vue, vuejs, components, spa',
                    'meta_desc' => 'Vue.js, компоненттер және SPA қосымшалары туралы материалдар.',
                ],
            ],

            'tailwind-css' => [
                'ru' => [
                    'title' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first стилизация',
                    'short' => 'Материалы по Tailwind CSS и построению интерфейсов без лишнего CSS.',
                    'description' => 'Раздел о Tailwind CSS: utility-first подход, адаптивность, тёмная тема и переиспользуемые UI-компоненты.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, utility first, ui',
                    'meta_desc' => 'Материалы по Tailwind CSS и современным интерфейсам.',
                ],
                'en' => [
                    'title' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first styling',
                    'short' => 'Content about Tailwind CSS and building interfaces with minimal custom CSS.',
                    'description' => 'A section about Tailwind CSS: utility-first workflow, responsiveness, dark mode and reusable UI components.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, utility first, ui',
                    'meta_desc' => 'Content about Tailwind CSS and modern interfaces.',
                ],
                'kk' => [
                    'title' => 'Tailwind CSS',
                    'subtitle' => 'Utility-first стильдеу',
                    'short' => 'Tailwind CSS және артық CSS-сіз интерфейс құру туралы материалдар.',
                    'description' => 'Tailwind CSS бөлімі: utility-first тәсілі, бейімделгіштік, қараңғы режим және қайта қолданылатын UI компоненттері.',
                    'meta_title' => 'Tailwind CSS',
                    'meta_keywords' => 'tailwind css, utility first, ui',
                    'meta_desc' => 'Tailwind CSS және заманауи интерфейстер туралы материалдар.',
                ],
            ],

            'laravel' => [
                'ru' => [
                    'title' => 'Laravel',
                    'subtitle' => 'PHP-фреймворк для веб-приложений',
                    'short' => 'Материалы по Laravel, MVC, Eloquent, Inertia и архитектуре приложений.',
                    'description' => 'Рубрика о Laravel: маршруты, контроллеры, модели, реквесты, ресурсы, очереди, события и сервисный слой.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php framework, eloquent, inertia',
                    'meta_desc' => 'Материалы по Laravel, MVC и архитектуре веб-приложений.',
                ],
                'en' => [
                    'title' => 'Laravel',
                    'subtitle' => 'PHP framework for web applications',
                    'short' => 'Content about Laravel, MVC, Eloquent, Inertia and application architecture.',
                    'description' => 'A category about Laravel: routes, controllers, models, requests, resources, queues, events and service layer.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php framework, eloquent, inertia',
                    'meta_desc' => 'Content about Laravel, MVC and web application architecture.',
                ],
                'kk' => [
                    'title' => 'Laravel',
                    'subtitle' => 'Веб-қосымшаларға арналған PHP фреймворкі',
                    'short' => 'Laravel, MVC, Eloquent, Inertia және қолданба архитектурасы туралы материалдар.',
                    'description' => 'Laravel туралы айдар: маршруттар, контроллерлер, модельдер, реквесттер, ресурстар, кезектер, оқиғалар және сервистік қабат.',
                    'meta_title' => 'Laravel',
                    'meta_keywords' => 'laravel, php framework, eloquent, inertia',
                    'meta_desc' => 'Laravel, MVC және веб-қосымша архитектурасы туралы материалдар.',
                ],
            ],

            'mysql' => [
                'ru' => [
                    'title' => 'MySQL',
                    'subtitle' => 'Популярная реляционная СУБД',
                    'short' => 'Материалы по MySQL, SQL-запросам, индексам и оптимизации.',
                    'description' => 'Рубрика о MySQL: проектирование таблиц, индексы, связи, оптимизация запросов и работа с большими данными.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, indexes, optimization',
                    'meta_desc' => 'Материалы по MySQL, SQL-запросам и оптимизации.',
                ],
                'en' => [
                    'title' => 'MySQL',
                    'subtitle' => 'Popular relational DBMS',
                    'short' => 'Content about MySQL, SQL queries, indexes and optimization.',
                    'description' => 'A category about MySQL: schema design, indexes, relations, query optimization and handling large datasets.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, indexes, optimization',
                    'meta_desc' => 'Content about MySQL, SQL queries and optimization.',
                ],
                'kk' => [
                    'title' => 'MySQL',
                    'subtitle' => 'Танымал реляциялық ДҚБЖ',
                    'short' => 'MySQL, SQL сұраныстары, индекстер және оңтайландыру туралы материалдар.',
                    'description' => 'MySQL туралы айдар: кесте жобалау, индекстер, байланыстар, сұраныстарды оңтайландыру және үлкен деректермен жұмыс.',
                    'meta_title' => 'MySQL',
                    'meta_keywords' => 'mysql, sql, indexes, optimization',
                    'meta_desc' => 'MySQL, SQL сұраныстары және оңтайландыру туралы материалдар.',
                ],
            ],

            'postgresql' => [
                'ru' => [
                    'title' => 'PostgreSQL',
                    'subtitle' => 'Мощная реляционная база данных',
                    'short' => 'Материалы по PostgreSQL, индексам, JSONB и производительности.',
                    'description' => 'Рубрика о PostgreSQL: типы данных, индексы, JSONB, сложные запросы и надёжная серверная разработка.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, database',
                    'meta_desc' => 'Материалы по PostgreSQL, индексам и производительности.',
                ],
                'en' => [
                    'title' => 'PostgreSQL',
                    'subtitle' => 'Powerful relational database',
                    'short' => 'Content about PostgreSQL, indexes, JSONB and performance.',
                    'description' => 'A category about PostgreSQL: data types, indexes, JSONB, advanced queries and reliable backend development.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, database',
                    'meta_desc' => 'Content about PostgreSQL, indexes and performance.',
                ],
                'kk' => [
                    'title' => 'PostgreSQL',
                    'subtitle' => 'Қуатты реляциялық дерекқор',
                    'short' => 'PostgreSQL, индекстер, JSONB және өнімділік туралы материалдар.',
                    'description' => 'PostgreSQL туралы айдар: деректер типтері, индекстер, JSONB, күрделі сұраныстар және сенімді backend әзірлеу.',
                    'meta_title' => 'PostgreSQL',
                    'meta_keywords' => 'postgresql, sql, jsonb, database',
                    'meta_desc' => 'PostgreSQL, индекстер және өнімділік туралы материалдар.',
                ],
            ],

            'docker-compose' => [
                'ru' => [
                    'title' => 'Docker Compose',
                    'subtitle' => 'Оркестрация контейнеров для разработки',
                    'short' => 'Материалы по docker-compose, сервисам, сетям и локальной разработке.',
                    'description' => 'Рубрика о Docker Compose: настройка многоконтейнерных проектов, зависимости сервисов и удобная локальная разработка.',
                    'meta_title' => 'Docker Compose',
                    'meta_keywords' => 'docker compose, containers, services, dev environment',
                    'meta_desc' => 'Материалы по Docker Compose и локальной разработке в контейнерах.',
                ],
                'en' => [
                    'title' => 'Docker Compose',
                    'subtitle' => 'Container orchestration for development',
                    'short' => 'Content about docker-compose, services, networks and local development.',
                    'description' => 'A category about Docker Compose: multi-container project setup, service dependencies and convenient local development.',
                    'meta_title' => 'Docker Compose',
                    'meta_keywords' => 'docker compose, containers, services, dev environment',
                    'meta_desc' => 'Content about Docker Compose and local containerized development.',
                ],
                'kk' => [
                    'title' => 'Docker Compose',
                    'subtitle' => 'Әзірлеуге арналған контейнер оркестрациясы',
                    'short' => 'docker-compose, сервистер, желілер және локалды әзірлеу туралы материалдар.',
                    'description' => 'Docker Compose туралы айдар: көп контейнерлі жобаларды баптау, сервис тәуелділіктері және ыңғайлы локалды әзірлеу.',
                    'meta_title' => 'Docker Compose',
                    'meta_keywords' => 'docker compose, containers, services, dev environment',
                    'meta_desc' => 'Docker Compose және контейнерлердегі локалды әзірлеу туралы материалдар.',
                ],
            ],

            'github-actions' => [
                'ru' => [
                    'title' => 'GitHub Actions',
                    'subtitle' => 'Автоматизация workflow',
                    'short' => 'Материалы по GitHub Actions, workflow, сборке и деплою.',
                    'description' => 'Рубрика о GitHub Actions: автоматизация тестов, сборки, деплоя и повседневных процессов разработки.',
                    'meta_title' => 'GitHub Actions',
                    'meta_keywords' => 'github actions, ci, cd, workflow',
                    'meta_desc' => 'Материалы по GitHub Actions и автоматизации рабочих процессов.',
                ],
                'en' => [
                    'title' => 'GitHub Actions',
                    'subtitle' => 'Workflow automation',
                    'short' => 'Content about GitHub Actions, workflows, builds and deployment.',
                    'description' => 'A category about GitHub Actions: test automation, builds, deployment and daily development workflows.',
                    'meta_title' => 'GitHub Actions',
                    'meta_keywords' => 'github actions, ci, cd, workflow',
                    'meta_desc' => 'Content about GitHub Actions and workflow automation.',
                ],
                'kk' => [
                    'title' => 'GitHub Actions',
                    'subtitle' => 'Workflow автоматтандыру',
                    'short' => 'GitHub Actions, workflow, жинау және деплой туралы материалдар.',
                    'description' => 'GitHub Actions туралы айдар: тесттерді, жинауды, деплойды және күнделікті әзірлеу процесін автоматтандыру.',
                    'meta_title' => 'GitHub Actions',
                    'meta_keywords' => 'github actions, ci, cd, workflow',
                    'meta_desc' => 'GitHub Actions және жұмыс процестерін автоматтандыру туралы материалдар.',
                ],
            ],
        ];

        $rubrics = BlogRubric::query()->get()->keyBy('url');

        foreach ($translations as $url => $items) {
            $rubric = $rubrics->get($url);

            if (!$rubric) {
                continue;
            }

            foreach ($items as $locale => $data) {
                BlogRubricTranslation::updateOrCreate(
                    [
                        'rubric_id' => $rubric->id,
                        'locale' => $locale,
                    ],
                    $data
                );
            }
        }
    }
}
