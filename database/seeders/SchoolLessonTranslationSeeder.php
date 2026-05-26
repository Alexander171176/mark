<?php

namespace Database\Seeders;

use App\Models\Admin\School\Lesson\SchoolLessonTranslation;
use Illuminate\Database\Seeder;

class SchoolLessonTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $lessons = [
            1 => [
                'ru' => ['Обзор темы', 'Краткое знакомство с темой урока.'],
                'en' => ['Topic Overview', 'A short introduction to the lesson topic.'],
                'kk' => ['Тақырыпқа шолу', 'Сабақ тақырыбымен қысқаша танысу.'],
            ],
            2 => [
                'ru' => ['Теория и основные понятия', 'Разбор ключевых терминов и принципов.'],
                'en' => ['Theory and Core Concepts', 'Explaining key terms and principles.'],
                'kk' => ['Теория және негізгі ұғымдар', 'Негізгі терминдер мен принциптерді түсіндіру.'],
            ],
            3 => [
                'ru' => ['Примеры реализации', 'Практические примеры применения материала.'],
                'en' => ['Implementation Examples', 'Practical examples of applying the material.'],
                'kk' => ['Іске асыру мысалдары', 'Материалды қолданудың практикалық мысалдары.'],
            ],
            4 => [
                'ru' => ['Практическое задание', 'Закрепление темы через самостоятельную работу.'],
                'en' => ['Practical Task', 'Reinforcing the topic through independent work.'],
                'kk' => ['Практикалық тапсырма', 'Тақырыпты өздік жұмыс арқылы бекіту.'],
            ],
            5 => [
                'ru' => ['Типичные ошибки', 'Разбор частых ошибок и способов их избежать.'],
                'en' => ['Common Mistakes', 'Reviewing common mistakes and how to avoid them.'],
                'kk' => ['Жиі кездесетін қателер', 'Жиі қателерді және олардан сақтану жолдарын талдау.'],
            ],
            6 => [
                'ru' => ['Домашнее задание', 'Финальное упражнение для закрепления урока.'],
                'en' => ['Homework', 'A final exercise to reinforce the lesson.'],
                'kk' => ['Үй тапсырмасы', 'Сабақты бекітуге арналған қорытынды жаттығу.'],
            ],
        ];

        $id = 1;

        for ($moduleId = 1; $moduleId <= 504; $moduleId++) {
            foreach ($lessons as $lessonNumber => $translations) {
                foreach ($translations as $locale => [$title, $subtitle]) {
                    $modulePrefix = match ($locale) {
                        'ru' => "Модуль {$moduleId}",
                        'en' => "Module {$moduleId}",
                        'kk' => "{$moduleId}-модуль",
                    };

                    $fullTitle = $modulePrefix . ': ' . $title;

                    $description = match ($locale) {
                        'ru' => "Урок «{$title}» помогает изучить материал пошагово, разобрать примеры и закрепить знания через практику.",
                        'en' => "The “{$title}” lesson helps learners study the material step by step, review examples, and reinforce skills through practice.",
                        'kk' => "«{$title}» сабағы материалды кезең-кезеңімен меңгеруге, мысалдарды талдауға және білімді практика арқылы бекітуге көмектеседі.",
                    };

                    SchoolLessonTranslation::updateOrCreate(
                        [
                            'school_lesson_id' => $id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $fullTitle,
                            'subtitle' => $subtitle,
                            'short' => $subtitle,
                            'description' => $description,
                            'meta_title' => $fullTitle,
                            'meta_keywords' => $title . ', IT, программирование, онлайн школа, урок',
                            'meta_desc' => $subtitle,
                        ]
                    );
                }

                $id++;
            }
        }
    }
}
