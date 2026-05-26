<?php

namespace Database\Seeders;

use App\Models\Admin\School\Track\SchoolTrackTranslation;
use Illuminate\Database\Seeder;

class SchoolTrackTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => [
                'ru' => ['Веб-разработка', 'Создание сайтов и веб-приложений.', 'Направление охватывает frontend, backend, интерфейсы, API и современные веб-технологии.'],
                'en' => ['Web Development', 'Building websites and web applications.', 'This track covers frontend, backend, interfaces, APIs, and modern web technologies.'],
                'kk' => ['Веб-әзірлеу', 'Сайттар мен веб-қосымшалар жасау.', 'Бұл бағыт frontend, backend, интерфейстер, API және заманауи веб-технологияларды қамтиды.'],
            ],
            2 => [
                'ru' => ['Инженерия ПО', 'Проектирование и поддержка программных систем.', 'Направление посвящено архитектуре, инфраструктуре, базам данных и качеству программных решений.'],
                'en' => ['Software Engineering', 'Designing and maintaining software systems.', 'This track focuses on architecture, infrastructure, databases, and software quality.'],
                'kk' => ['Бағдарламалық инженерия', 'Бағдарламалық жүйелерді жобалау және қолдау.', 'Бұл бағыт архитектура, инфрақұрылым, деректер базасы және бағдарламалық сапаға арналған.'],
            ],

            3 => [
                'ru' => ['Frontend-разработка', 'Пользовательские интерфейсы и SPA.', 'Изучение HTML, CSS, JavaScript, Vue.js и современных подходов к созданию интерфейсов.'],
                'en' => ['Frontend Development', 'User interfaces and SPAs.', 'Learning HTML, CSS, JavaScript, Vue.js, and modern interface development approaches.'],
                'kk' => ['Frontend әзірлеу', 'Пайдаланушы интерфейстері және SPA.', 'HTML, CSS, JavaScript, Vue.js және заманауи интерфейс жасау тәсілдерін үйрену.'],
            ],
            4 => [
                'ru' => ['Backend-разработка', 'Серверная логика и API.', 'Изучение PHP, Laravel, баз данных, REST API и серверной архитектуры приложений.'],
                'en' => ['Backend Development', 'Server-side logic and APIs.', 'Learning PHP, Laravel, databases, REST APIs, and backend application architecture.'],
                'kk' => ['Backend әзірлеу', 'Серверлік логика және API.', 'PHP, Laravel, деректер базасы, REST API және backend архитектурасын үйрену.'],
            ],
            5 => [
                'ru' => ['DevOps-инженерия', 'Инфраструктура и автоматизация.', 'Направление про Docker, CI/CD, окружения разработки и автоматизацию развёртывания.'],
                'en' => ['DevOps Engineering', 'Infrastructure and automation.', 'A track about Docker, CI/CD, development environments, and deployment automation.'],
                'kk' => ['DevOps инженериясы', 'Инфрақұрылым және автоматизация.', 'Docker, CI/CD, әзірлеу орталары және деплой автоматизациясы туралы бағыт.'],
            ],
            6 => [
                'ru' => ['Инженерия баз данных', 'Проектирование и оптимизация данных.', 'Изучение MySQL, проектирования схем, индексов, связей и оптимизации запросов.'],
                'en' => ['Database Engineering', 'Data design and optimization.', 'Learning MySQL, schema design, indexes, relationships, and query optimization.'],
                'kk' => ['Деректер базасы инженериясы', 'Деректерді жобалау және оңтайландыру.', 'MySQL, схема жобалау, индекстер, байланыстар және сұраныстарды оңтайландыру.'],
            ],

            7 => [
                'ru' => ['HTML и CSS', 'База frontend-разработки.', 'Разметка страниц, стилизация интерфейсов, адаптивность, сетки и базовая визуальная структура сайта.'],
                'en' => ['HTML and CSS', 'The foundation of frontend development.', 'Page markup, interface styling, responsiveness, grids, and basic visual website structure.'],
                'kk' => ['HTML және CSS', 'Frontend әзірлеудің негізі.', 'Бет белгілеу, интерфейс стильдері, бейімделу, торлар және сайттың визуалды құрылымы.'],
            ],
            8 => [
                'ru' => ['JavaScript и Vue.js', 'Интерактивные интерфейсы.', 'Создание динамических SPA-приложений, компонентов, реактивности и клиентской логики.'],
                'en' => ['JavaScript and Vue.js', 'Interactive interfaces.', 'Building dynamic SPA applications, components, reactivity, and client-side logic.'],
                'kk' => ['JavaScript және Vue.js', 'Интерактивті интерфейстер.', 'Динамикалық SPA қосымшалар, компоненттер, реактивтілік және клиенттік логика жасау.'],
            ],
            9 => [
                'ru' => ['PHP и Laravel', 'Backend на Laravel.', 'Разработка серверной логики, моделей, контроллеров, миграций, API и админ-панелей.'],
                'en' => ['PHP and Laravel', 'Backend with Laravel.', 'Developing server logic, models, controllers, migrations, APIs, and admin panels.'],
                'kk' => ['PHP және Laravel', 'Laravel арқылы backend.', 'Серверлік логика, модельдер, контроллерлер, миграциялар, API және админ-панельдер әзірлеу.'],
            ],
            10 => [
                'ru' => ['API-разработка', 'REST API и интеграции.', 'Проектирование API, ресурсов, авторизации, валидации и обмена данными между сервисами.'],
                'en' => ['API Development', 'REST APIs and integrations.', 'Designing APIs, resources, authorization, validation, and data exchange between services.'],
                'kk' => ['API әзірлеу', 'REST API және интеграциялар.', 'API, ресурстар, авторизация, валидация және сервистер арасында дерек алмасуды жобалау.'],
            ],
            11 => [
                'ru' => ['Docker и контейнеры', 'Изолированные окружения разработки.', 'Создание контейнеров, docker-compose окружений и стабильной инфраструктуры для проектов.'],
                'en' => ['Docker and Containers', 'Isolated development environments.', 'Creating containers, docker-compose environments, and stable project infrastructure.'],
                'kk' => ['Docker және контейнерлер', 'Оқшауланған әзірлеу орталары.', 'Контейнерлер, docker-compose орталары және тұрақты жоба инфрақұрылымын жасау.'],
            ],
            12 => [
                'ru' => ['CI/CD', 'Автоматизация сборки и деплоя.', 'Настройка автоматических проверок, тестирования, сборки и публикации приложений.'],
                'en' => ['CI/CD', 'Build and deployment automation.', 'Setting up automated checks, testing, builds, and application deployment.'],
                'kk' => ['CI/CD', 'Жинау және деплой автоматизациясы.', 'Автоматты тексерулер, тестілеу, жинау және қосымшаны жариялауды баптау.'],
            ],
            13 => [
                'ru' => ['Проектирование MySQL', 'Структура таблиц и связей.', 'Проектирование таблиц, внешних ключей, индексов и нормализованных структур данных.'],
                'en' => ['MySQL Design', 'Tables and relationships structure.', 'Designing tables, foreign keys, indexes, and normalized data structures.'],
                'kk' => ['MySQL жобалау', 'Кестелер мен байланыстар құрылымы.', 'Кестелер, сыртқы кілттер, индекстер және нормаланған деректер құрылымдарын жобалау.'],
            ],
            14 => [
                'ru' => ['Оптимизация баз данных', 'Производительность запросов.', 'Анализ запросов, индексов, связей и подходов к ускорению работы базы данных.'],
                'en' => ['Database Optimization', 'Query performance.', 'Analyzing queries, indexes, relationships, and approaches to improving database performance.'],
                'kk' => ['Деректер базасын оңтайландыру', 'Сұраныстар өнімділігі.', 'Сұраныстарды, индекстерді, байланыстарды және деректер базасын жылдамдату тәсілдерін талдау.'],
            ],
        ];

        foreach ($data as $trackId => $translations) {
            foreach ($translations as $locale => [$name, $short, $description]) {
                SchoolTrackTranslation::updateOrCreate(
                    [
                        'school_track_id' => $trackId,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $name,
                        'short' => $short,
                        'description' => $description,
                        'meta_title' => $name . ' — онлайн-обучение IT и программированию',
                        'meta_keywords' => $name . ', IT, программирование, онлайн школа, веб-разработка',
                        'meta_desc' => $short,
                    ]
                );
            }
        }
    }
}
