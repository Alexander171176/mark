<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolHashtag\SchoolHashtagTranslation;
use Illuminate\Database\Seeder;

class SchoolHashtagTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            1 => [
                'ru' => ['PHP', 'Серверный язык программирования.', 'PHP используется для создания динамических сайтов, API и backend-приложений.'],
                'en' => ['PHP', 'A server-side programming language.', 'PHP is used to build dynamic websites, APIs, and backend applications.'],
                'kk' => ['PHP', 'Серверлік бағдарламалау тілі.', 'PHP динамикалық сайттар, API және backend қосымшаларын жасау үшін қолданылады.'],
            ],
            2 => [
                'ru' => ['Laravel', 'PHP-фреймворк для современной веб-разработки.', 'Laravel помогает быстро создавать надёжные веб-приложения, API, админ-панели и CRM-системы.'],
                'en' => ['Laravel', 'A PHP framework for modern web development.', 'Laravel helps build reliable web applications, APIs, admin panels, and CRM systems quickly.'],
                'kk' => ['Laravel', 'Қазіргі веб-әзірлеуге арналған PHP фреймворкі.', 'Laravel сенімді веб-қосымшалар, API, админ-панельдер және CRM жүйелерін жылдам жасауға көмектеседі.'],
            ],
            3 => [
                'ru' => ['Vue.js', 'Прогрессивный JavaScript-фреймворк.', 'Vue.js используется для создания интерактивных интерфейсов, SPA и компонентной frontend-архитектуры.'],
                'en' => ['Vue.js', 'A progressive JavaScript framework.', 'Vue.js is used to build interactive interfaces, SPAs, and component-based frontend architecture.'],
                'kk' => ['Vue.js', 'Прогрессивті JavaScript фреймворкі.', 'Vue.js интерактивті интерфейстер, SPA және компоненттік frontend архитектурасын жасауға қолданылады.'],
            ],
            4 => [
                'ru' => ['JavaScript', 'Основной язык frontend-разработки.', 'JavaScript позволяет создавать интерактивные сайты, веб-приложения и клиентскую бизнес-логику.'],
                'en' => ['JavaScript', 'The core language of frontend development.', 'JavaScript enables interactive websites, web applications, and client-side business logic.'],
                'kk' => ['JavaScript', 'Frontend әзірлеудің негізгі тілі.', 'JavaScript интерактивті сайттар, веб-қосымшалар және клиенттік бизнес-логика жасауға мүмкіндік береді.'],
            ],
            5 => [
                'ru' => ['TypeScript', 'JavaScript с типизацией.', 'TypeScript повышает надёжность кода и удобство разработки крупных frontend и backend-проектов.'],
                'en' => ['TypeScript', 'Typed JavaScript.', 'TypeScript improves code reliability and developer experience in large frontend and backend projects.'],
                'kk' => ['TypeScript', 'Типтелген JavaScript.', 'TypeScript үлкен frontend және backend жобаларында код сенімділігін арттырады.'],
            ],
            6 => [
                'ru' => ['HTML', 'Язык разметки веб-страниц.', 'HTML задаёт структуру страниц, контента, форм и основных элементов интерфейса.'],
                'en' => ['HTML', 'The markup language of web pages.', 'HTML defines the structure of pages, content, forms, and basic interface elements.'],
                'kk' => ['HTML', 'Веб-беттердің белгілеу тілі.', 'HTML беттердің, контенттің, формалардың және интерфейс элементтерінің құрылымын анықтайды.'],
            ],
            7 => [
                'ru' => ['CSS', 'Язык оформления веб-интерфейсов.', 'CSS отвечает за внешний вид сайта: цвета, сетки, адаптивность, анимации и стили компонентов.'],
                'en' => ['CSS', 'A styling language for web interfaces.', 'CSS controls website appearance: colors, layouts, responsiveness, animations, and component styles.'],
                'kk' => ['CSS', 'Веб-интерфейстерді сәндеу тілі.', 'CSS сайт көрінісіне жауап береді: түстер, торлар, бейімделу, анимациялар және компонент стильдері.'],
            ],
            8 => [
                'ru' => ['Tailwind CSS', 'Utility-first CSS-фреймворк.', 'Tailwind CSS ускоряет создание адаптивных и современных интерфейсов без написания лишнего CSS.'],
                'en' => ['Tailwind CSS', 'A utility-first CSS framework.', 'Tailwind CSS speeds up building responsive and modern interfaces without writing excessive custom CSS.'],
                'kk' => ['Tailwind CSS', 'Utility-first CSS фреймворкі.', 'Tailwind CSS артық CSS жазбай, заманауи және бейімделгіш интерфейстерді тез жасауға көмектеседі.'],
            ],
            9 => [
                'ru' => ['MySQL', 'Реляционная база данных.', 'MySQL используется для хранения данных приложений, интернет-магазинов, CRM и образовательных платформ.'],
                'en' => ['MySQL', 'A relational database.', 'MySQL is used to store data for applications, online stores, CRMs, and learning platforms.'],
                'kk' => ['MySQL', 'Реляциялық деректер базасы.', 'MySQL қосымшалар, интернет-дүкендер, CRM және оқу платформалары деректерін сақтау үшін қолданылады.'],
            ],
            10 => [
                'ru' => ['Docker', 'Платформа контейнеризации приложений.', 'Docker помогает запускать проекты в изолированной и повторяемой среде разработки.'],
                'en' => ['Docker', 'An application containerization platform.', 'Docker helps run projects in isolated and reproducible development environments.'],
                'kk' => ['Docker', 'Қосымшаларды контейнерлеу платформасы.', 'Docker жобаларды оқшауланған және қайталанатын әзірлеу ортасында іске қосуға көмектеседі.'],
            ],
            11 => [
                'ru' => ['API', 'Интерфейс взаимодействия между приложениями.', 'API позволяет frontend, мобильным приложениям и внешним сервисам обмениваться данными с backend.'],
                'en' => ['API', 'An interface for communication between applications.', 'APIs allow frontends, mobile apps, and external services to exchange data with a backend.'],
                'kk' => ['API', 'Қосымшалар арасындағы байланыс интерфейсі.', 'API frontend, мобильді қосымшалар және сыртқы сервистерге backend-пен дерек алмасуға мүмкіндік береді.'],
            ],
            12 => [
                'ru' => ['Git', 'Система контроля версий.', 'Git помогает отслеживать изменения кода, работать в команде и управлять версиями проекта.'],
                'en' => ['Git', 'A version control system.', 'Git helps track code changes, collaborate in teams, and manage project versions.'],
                'kk' => ['Git', 'Нұсқаларды басқару жүйесі.', 'Git код өзгерістерін бақылауға, командада жұмыс істеуге және жоба нұсқаларын басқаруға көмектеседі.'],
            ],
        ];

        foreach ($translations as $hashtagId => $locales) {
            foreach ($locales as $locale => [$name, $short, $description]) {
                SchoolHashtagTranslation::updateOrCreate(
                    [
                        'school_hashtag_id' => $hashtagId,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $name,
                        'short' => $short,
                        'description' => $description,
                        'meta_title' => $name . ' — онлайн-обучение программированию',
                        'meta_keywords' => $name . ', программирование, IT, онлайн школа, веб-разработка',
                        'meta_desc' => $short,
                    ]
                );
            }
        }
    }
}
