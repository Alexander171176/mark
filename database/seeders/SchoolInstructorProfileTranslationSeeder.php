<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileTranslation;
use Illuminate\Database\Seeder;

class SchoolInstructorProfileTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            2 => [
                'ru' => ['Backend разработчик (Laravel)', 'Опытный PHP разработчик', 'Разрабатываю API, CRM и маркетплейсы на Laravel.'],
                'en' => ['Backend Developer (Laravel)', 'Experienced PHP developer', 'I build APIs, CRMs, and marketplaces using Laravel.'],
                'kk' => ['Backend әзірлеуші (Laravel)', 'Тәжірибелі PHP әзірлеуші', 'Laravel арқылы API, CRM және маркетплейстер жасаймын.'],
            ],
            3 => [
                'ru' => ['Fullstack разработчик', 'JavaScript и PHP эксперт', 'Работаю с Vue.js, Node.js и Laravel более 10 лет.'],
                'en' => ['Fullstack Developer', 'JavaScript & PHP expert', 'Working with Vue.js, Node.js, and Laravel for 10+ years.'],
                'kk' => ['Fullstack әзірлеуші', 'JavaScript және PHP маманы', 'Vue.js, Node.js және Laravel-мен 10 жылдан астам жұмыс істеймін.'],
            ],
            4 => [
                'ru' => ['Frontend разработчик', 'Vue.js специалист', 'Создаю современные SPA интерфейсы на Vue 3.'],
                'en' => ['Frontend Developer', 'Vue.js specialist', 'Building modern SPA interfaces with Vue 3.'],
                'kk' => ['Frontend әзірлеуші', 'Vue.js маманы', 'Vue 3 арқылы заманауи SPA интерфейстер жасаймын.'],
            ],
            5 => [
                'ru' => ['Senior инженер', 'Архитектор систем', 'Проектирую масштабируемые backend системы.'],
                'en' => ['Senior Engineer', 'System Architect', 'Designing scalable backend systems.'],
                'kk' => ['Senior инженер', 'Жүйе архитектор', 'Масштабталатын backend жүйелерін жобалаймын.'],
            ],
            6 => [
                'ru' => ['Junior преподаватель', 'Начинающий разработчик', 'Помогаю новичкам освоить основы программирования.'],
                'en' => ['Junior Instructor', 'Beginner developer', 'Helping beginners learn programming basics.'],
                'kk' => ['Junior оқытушы', 'Бастапқы әзірлеуші', 'Жаңадан бастаушыларға бағдарламалауды үйретемін.'],
            ],
            7 => [
                'ru' => ['DevOps инженер', 'Docker и CI/CD специалист', 'Настраиваю инфраструктуру и автоматизацию деплоя.'],
                'en' => ['DevOps Engineer', 'Docker & CI/CD specialist', 'Setting up infrastructure and deployment automation.'],
                'kk' => ['DevOps инженері', 'Docker және CI/CD маманы', 'Инфрақұрылым мен деплой автоматизациясын орнатамын.'],
            ],
        ];

        foreach ($data as $profileId => $translations) {
            foreach ($translations as $locale => [$title, $short, $bio]) {
                SchoolInstructorProfileTranslation::updateOrCreate(
                    [
                        'school_instructor_profile_id' => $profileId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'short' => $short,
                        'bio' => $bio,
                        'meta_title' => $title,
                        'meta_keywords' => 'программирование, IT, онлайн обучение',
                        'meta_desc' => $short,
                    ]
                );
            }
        }
    }
}
