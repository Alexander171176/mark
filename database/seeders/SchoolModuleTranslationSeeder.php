<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolModule\SchoolModuleTranslation;
use Illuminate\Database\Seeder;

class SchoolModuleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            1 => [
                'ru' => ['Введение в курс', 'Знакомство с темой и структурой обучения.'],
                'en' => ['Course Introduction', 'Getting familiar with the topic and course structure.'],
                'kk' => ['Курсқа кіріспе', 'Тақырыппен және оқу құрылымымен танысу.'],
            ],
            2 => [
                'ru' => ['Ключевые концепции', 'Основные идеи, термины и принципы направления.'],
                'en' => ['Core Concepts', 'Main ideas, terms, and principles of the topic.'],
                'kk' => ['Негізгі тұжырымдамалар', 'Бағыттың негізгі идеялары, терминдері және принциптері.'],
            ],
            3 => [
                'ru' => ['Базовая практика', 'Практические задания для закрепления основ.'],
                'en' => ['Basic Practice', 'Practical tasks for reinforcing the fundamentals.'],
                'kk' => ['Базалық практика', 'Негіздерді бекітуге арналған практикалық тапсырмалар.'],
            ],
            4 => [
                'ru' => ['Продвинутые инструменты', 'Изучение более сложных инструментов и подходов.'],
                'en' => ['Advanced Tools', 'Learning more advanced tools and approaches.'],
                'kk' => ['Жетілдірілген құралдар', 'Күрделірек құралдар мен тәсілдерді үйрену.'],
            ],
            5 => [
                'ru' => ['Проектная работа', 'Создание учебного проекта на основе изученного материала.'],
                'en' => ['Project Work', 'Building an educational project based on the learned material.'],
                'kk' => ['Жобалық жұмыс', 'Өтілген материал негізінде оқу жобасын жасау.'],
            ],
            6 => [
                'ru' => ['Итоговое повторение', 'Повторение ключевых тем и подготовка к следующему этапу.'],
                'en' => ['Final Review', 'Reviewing key topics and preparing for the next stage.'],
                'kk' => ['Қорытынды қайталау', 'Негізгі тақырыптарды қайталау және келесі кезеңге дайындалу.'],
            ],
        ];

        $id = 1;

        for ($courseId = 1; $courseId <= 84; $courseId++) {
            foreach ($modules as $moduleNumber => $translations) {
                foreach ($translations as $locale => [$title, $subtitle]) {
                    $coursePrefix = match ($locale) {
                        'ru' => "Курс {$courseId}",
                        'en' => "Course {$courseId}",
                        'kk' => "{$courseId}-курс",
                    };

                    $fullTitle = $coursePrefix . ': ' . $title;

                    $description = match ($locale) {
                        'ru' => "Модуль «{$title}» помогает последовательно изучить материал курса, закрепить знания на практике и подготовиться к выполнению проекта.",
                        'en' => "The “{$title}” module helps learners study the course material step by step, practice skills, and prepare for project work.",
                        'kk' => "«{$title}» модулі курс материалын кезең-кезеңімен меңгеруге, тәжірибеде бекітуге және жоба жұмысына дайындалуға көмектеседі.",
                    };

                    SchoolModuleTranslation::updateOrCreate(
                        [
                            'school_module_id' => $id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $fullTitle,
                            'subtitle' => $subtitle,
                            'short' => $subtitle,
                            'description' => $description,
                            'meta_title' => $fullTitle,
                            'meta_keywords' => $title . ', IT, программирование, онлайн школа, модуль',
                            'meta_desc' => $subtitle,
                        ]
                    );
                }

                $id++;
            }
        }
    }
}
