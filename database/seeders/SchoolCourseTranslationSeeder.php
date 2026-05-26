<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\SchoolCourseTranslation;
use Illuminate\Database\Seeder;

class SchoolCourseTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $tracks = [
            1 => ['ru' => 'Веб-разработка', 'en' => 'Web Development', 'kk' => 'Веб-әзірлеу'],
            2 => ['ru' => 'Инженерия ПО', 'en' => 'Software Engineering', 'kk' => 'Бағдарламалық инженерия'],
            3 => ['ru' => 'Frontend-разработка', 'en' => 'Frontend Development', 'kk' => 'Frontend әзірлеу'],
            4 => ['ru' => 'Backend-разработка', 'en' => 'Backend Development', 'kk' => 'Backend әзірлеу'],
            5 => ['ru' => 'DevOps-инженерия', 'en' => 'DevOps Engineering', 'kk' => 'DevOps инженериясы'],
            6 => ['ru' => 'Инженерия баз данных', 'en' => 'Database Engineering', 'kk' => 'Деректер базасы инженериясы'],
            7 => ['ru' => 'HTML и CSS', 'en' => 'HTML and CSS', 'kk' => 'HTML және CSS'],
            8 => ['ru' => 'JavaScript и Vue.js', 'en' => 'JavaScript and Vue.js', 'kk' => 'JavaScript және Vue.js'],
            9 => ['ru' => 'PHP и Laravel', 'en' => 'PHP and Laravel', 'kk' => 'PHP және Laravel'],
            10 => ['ru' => 'API-разработка', 'en' => 'API Development', 'kk' => 'API әзірлеу'],
            11 => ['ru' => 'Docker и контейнеры', 'en' => 'Docker and Containers', 'kk' => 'Docker және контейнерлер'],
            12 => ['ru' => 'CI/CD', 'en' => 'CI/CD', 'kk' => 'CI/CD'],
            13 => ['ru' => 'Проектирование MySQL', 'en' => 'MySQL Design', 'kk' => 'MySQL жобалау'],
            14 => ['ru' => 'Оптимизация баз данных', 'en' => 'Database Optimization', 'kk' => 'Деректер базасын оңтайландыру'],
        ];

        $types = [
            1 => [
                'ru' => ['Основы', 'Базовый курс для уверенного старта.'],
                'en' => ['Fundamentals', 'A basic course for a confident start.'],
                'kk' => ['Негіздері', 'Сенімді бастауға арналған базалық курс.'],
            ],
            2 => [
                'ru' => ['Практика', 'Практический курс с задачами и примерами.'],
                'en' => ['Practice', 'A practical course with tasks and examples.'],
                'kk' => ['Практика', 'Тапсырмалар мен мысалдарға негізделген практикалық курс.'],
            ],
            3 => [
                'ru' => ['Продвинутый уровень', 'Углублённое изучение инструментов и подходов.'],
                'en' => ['Advanced Level', 'Deep learning of tools and approaches.'],
                'kk' => ['Жетілдірілген деңгей', 'Құралдар мен тәсілдерді тереңдетіп үйрену.'],
            ],
            4 => [
                'ru' => ['Проектный курс', 'Создание полноценного учебного проекта.'],
                'en' => ['Project Course', 'Building a complete educational project.'],
                'kk' => ['Жобалық курс', 'Толық оқу жобасын жасау.'],
            ],
            5 => [
                'ru' => ['Архитектура', 'Проектирование структуры и логики приложения.'],
                'en' => ['Architecture', 'Designing application structure and logic.'],
                'kk' => ['Архитектура', 'Қосымша құрылымы мен логикасын жобалау.'],
            ],
            6 => [
                'ru' => ['Production-подход', 'Подготовка проекта к реальной эксплуатации.'],
                'en' => ['Production Approach', 'Preparing a project for real-world use.'],
                'kk' => ['Production тәсілі', 'Жобаны нақты қолдануға дайындау.'],
            ],
        ];

        $id = 1;

        foreach ($tracks as $trackId => $trackLocales) {
            foreach ($types as $typeId => $typeLocales) {
                foreach (['ru', 'en', 'kk'] as $locale) {
                    $trackName = $trackLocales[$locale];
                    $typeName = $typeLocales[$locale][0];
                    $subtitle = $typeLocales[$locale][1];

                    $title = $trackName . ': ' . $typeName;

                    $description = match ($locale) {
                        'ru' => "Курс «{$title}» помогает изучить ключевые темы направления, закрепить знания на практике и подготовиться к разработке реальных IT-проектов.",
                        'en' => "The “{$title}” course helps learners study key topics, practice skills, and prepare for building real IT projects.",
                        'kk' => "«{$title}» курсы негізгі тақырыптарды меңгеруге, тәжірибеде бекітуге және нақты IT жобаларға дайындалуға көмектеседі.",
                    };

                    SchoolCourseTranslation::updateOrCreate(
                        [
                            'school_course_id' => $id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $title,
                            'subtitle' => $subtitle,
                            'short' => $subtitle,
                            'description' => $description,
                            'meta_title' => $title,
                            'meta_keywords' => $trackName . ', IT, программирование, онлайн школа, курс',
                            'meta_desc' => $subtitle,
                        ]
                    );
                }

                $id++;
            }
        }
    }
}
