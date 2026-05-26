<?php

namespace Database\Seeders;

use App\Models\Admin\School\Bundle\SchoolBundleTranslation;
use Illuminate\Database\Seeder;

class SchoolBundleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => [
                'ru' => ['Frontend-разработчик', 'Подборка курсов по HTML, CSS, JavaScript и Vue.js.'],
                'en' => ['Frontend Developer', 'A bundle of courses on HTML, CSS, JavaScript, and Vue.js.'],
                'kk' => ['Frontend әзірлеуші', 'HTML, CSS, JavaScript және Vue.js курстарының жинағы.'],
            ],
            2 => [
                'ru' => ['Backend-разработчик', 'Курсы по PHP, Laravel, API и серверной архитектуре.'],
                'en' => ['Backend Developer', 'Courses on PHP, Laravel, APIs, and backend architecture.'],
                'kk' => ['Backend әзірлеуші', 'PHP, Laravel, API және серверлік архитектура курстары.'],
            ],
            3 => [
                'ru' => ['Fullstack Laravel + Vue', 'Полный путь от интерфейса до серверной логики.'],
                'en' => ['Fullstack Laravel + Vue', 'A complete path from interface to server-side logic.'],
                'kk' => ['Fullstack Laravel + Vue', 'Интерфейстен серверлік логикаға дейінгі толық жол.'],
            ],
            4 => [
                'ru' => ['DevOps: Docker и CI/CD', 'Инфраструктура, контейнеры и автоматизация деплоя.'],
                'en' => ['DevOps: Docker and CI/CD', 'Infrastructure, containers, and deployment automation.'],
                'kk' => ['DevOps: Docker және CI/CD', 'Инфрақұрылым, контейнерлер және деплой автоматизациясы.'],
            ],
            5 => [
                'ru' => ['Инженер баз данных', 'Проектирование MySQL, индексы и оптимизация запросов.'],
                'en' => ['Database Engineer', 'MySQL design, indexes, and query optimization.'],
                'kk' => ['Деректер базасы инженері', 'MySQL жобалау, индекстер және сұраныстарды оңтайландыру.'],
            ],
            6 => [
                'ru' => ['Старт Junior-программиста', 'Базовый набор курсов для начинающих разработчиков.'],
                'en' => ['Junior Programmer Start', 'A basic course bundle for beginner developers.'],
                'kk' => ['Junior бағдарламашы старты', 'Жаңадан бастаушы әзірлеушілерге арналған базалық курс жинағы.'],
            ],
        ];

        foreach ($data as $bundleId => $translations) {
            foreach ($translations as $locale => [$title, $short]) {
                SchoolBundleTranslation::updateOrCreate(
                    [
                        'school_bundle_id' => $bundleId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'subtitle' => $short,
                        'short' => $short,
                        'description' => $short . ' Набор помогает пройти обучение последовательно и собрать практические навыки для IT-разработки.',
                        'meta_title' => $title,
                        'meta_keywords' => 'IT, программирование, онлайн школа, набор курсов',
                        'meta_desc' => $short,
                    ]
                );
            }
        }
    }
}
