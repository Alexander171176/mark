<?php

namespace Database\Seeders;

use App\Models\Admin\School\CourseSchedule\SchoolCourseScheduleTranslation;
use Illuminate\Database\Seeder;

class SchoolCourseScheduleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => ['winter', 1],
            2 => ['winter', 2],
            3 => ['spring', 1],
            4 => ['spring', 2],
            5 => ['summer', 1],
            6 => ['summer', 2],
            7 => ['autumn', 1],
            8 => ['autumn', 2],
        ];

        $seasons = [
            'winter' => [
                'ru' => 'Зимний поток',
                'en' => 'Winter Cohort',
                'kk' => 'Қысқы топ',
            ],
            'spring' => [
                'ru' => 'Весенний поток',
                'en' => 'Spring Cohort',
                'kk' => 'Көктемгі топ',
            ],
            'summer' => [
                'ru' => 'Летний поток',
                'en' => 'Summer Cohort',
                'kk' => 'Жазғы топ',
            ],
            'autumn' => [
                'ru' => 'Осенний поток',
                'en' => 'Autumn Cohort',
                'kk' => 'Күзгі топ',
            ],
        ];

        foreach ($data as $scheduleId => [$season, $number]) {
            foreach (['ru', 'en', 'kk'] as $locale) {
                $seasonTitle = $seasons[$season][$locale];

                $title = match ($locale) {
                    'ru' => "{$seasonTitle} {$number}: программирование и веб-разработка",
                    'en' => "{$seasonTitle} {$number}: Programming and Web Development",
                    'kk' => "{$seasonTitle} {$number}: бағдарламалау және веб-әзірлеу",
                };

                $subtitle = match ($locale) {
                    'ru' => 'Групповое обучение с преподавателем, практикой и дедлайнами.',
                    'en' => 'Group-based learning with an instructor, practice, and deadlines.',
                    'kk' => 'Оқытушымен, практикамен және дедлайндармен топтық оқу.',
                };

                $description = match ($locale) {
                    'ru' => "Поток «{$title}» предназначен для последовательного изучения IT-навыков. Участники проходят занятия по расписанию, выполняют практические задания и получают обратную связь от преподавателя.",
                    'en' => "The “{$title}” cohort is designed for structured IT skills learning. Students follow a schedule, complete practical assignments, and receive instructor feedback.",
                    'kk' => "«{$title}» тобы IT дағдыларын жүйелі түрде үйренуге арналған. Қатысушылар сабақтарды кесте бойынша өтіп, практикалық тапсырмалар орындайды және оқытушыдан кері байланыс алады.",
                };

                SchoolCourseScheduleTranslation::updateOrCreate(
                    [
                        'school_course_schedule_id' => $scheduleId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'short' => $subtitle,
                        'description' => $description,
                        'meta_title' => $title,
                        'meta_keywords' => 'IT, программирование, онлайн школа, поток, расписание',
                        'meta_desc' => $subtitle,
                    ]
                );
            }
        }
    }
}
