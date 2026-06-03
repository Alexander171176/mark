<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolAssignment\SchoolAssignmentTranslation;
use Illuminate\Database\Seeder;

class SchoolAssignmentTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            1 => [
                'ru' => ['Проверка знаний', 'Короткое задание для проверки понимания темы.'],
                'en' => ['Knowledge Check', 'A short task to check understanding of the topic.'],
                'kk' => ['Білімді тексеру', 'Тақырыпты түсінуді тексеруге арналған қысқа тапсырма.'],
            ],
            2 => [
                'ru' => ['Практика кода', 'Напишите небольшой фрагмент кода по теме урока.'],
                'en' => ['Code Practice', 'Write a small code snippet based on the lesson topic.'],
                'kk' => ['Код практикасы', 'Сабақ тақырыбы бойынша шағын код жазыңыз.'],
            ],
            3 => [
                'ru' => ['Мини-проект', 'Соберите небольшой учебный проект.'],
                'en' => ['Mini Project', 'Build a small educational project.'],
                'kk' => ['Мини-жоба', 'Шағын оқу жобасын құрастырыңыз.'],
            ],
            4 => [
                'ru' => ['Поиск и исправление ошибок', 'Найдите ошибку в коде и предложите исправление.'],
                'en' => ['Debugging Task', 'Find an error in the code and suggest a fix.'],
                'kk' => ['Қатені табу және түзету', 'Кодтағы қатені тауып, түзету ұсыныңыз.'],
            ],
            5 => [
                'ru' => ['Архитектурное задание', 'Опишите структуру решения и связи между частями приложения.'],
                'en' => ['Architecture Task', 'Describe the solution structure and relationships between application parts.'],
                'kk' => ['Архитектуралық тапсырма', 'Шешім құрылымын және қосымша бөліктері арасындағы байланысты сипаттаңыз.'],
            ],
            6 => [
                'ru' => ['Итоговое задание', 'Выполните финальную работу по материалам урока.'],
                'en' => ['Final Task', 'Complete the final task based on the lesson materials.'],
                'kk' => ['Қорытынды тапсырма', 'Сабақ материалдары бойынша қорытынды жұмысты орындаңыз.'],
            ],
        ];

        $id = 1;

        for ($lessonId = 1; $lessonId <= 3024; $lessonId++) {
            foreach ($assignments as $assignmentNumber => $translations) {
                foreach ($translations as $locale => [$title, $subtitle]) {
                    $lessonPrefix = match ($locale) {
                        'ru' => "Урок {$lessonId}",
                        'en' => "Lesson {$lessonId}",
                        'kk' => "{$lessonId}-сабақ",
                    };

                    $fullTitle = $lessonPrefix . ': ' . $title;

                    $description = match ($locale) {
                        'ru' => "Задание «{$title}» помогает закрепить материал урока, проверить понимание ключевых идей и применить знания на практике.",
                        'en' => "The “{$title}” assignment helps reinforce the lesson material, check understanding of key ideas, and apply knowledge in practice.",
                        'kk' => "«{$title}» тапсырмасы сабақ материалын бекітуге, негізгі идеяларды түсінуді тексеруге және білімді тәжірибеде қолдануға көмектеседі.",
                    };

                    $instructions = match ($locale) {
                        'ru' => "1. Внимательно изучите материал урока.\n2. Выполните задание самостоятельно.\n3. Проверьте результат.\n4. Отправьте ответ на проверку.",
                        'en' => "1. Carefully study the lesson material.\n2. Complete the task independently.\n3. Check your result.\n4. Submit your answer for review.",
                        'kk' => "1. Сабақ материалын мұқият оқыңыз.\n2. Тапсырманы өз бетіңізше орындаңыз.\n3. Нәтижені тексеріңіз.\n4. Жауапты тексеруге жіберіңіз.",
                    };

                    SchoolAssignmentTranslation::updateOrCreate(
                        [
                            'school_assignment_id' => $id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $fullTitle,
                            'subtitle' => $subtitle,
                            'short' => $subtitle,
                            'description' => $description,
                            'instructions' => $instructions,
                        ]
                    );
                }

                $id++;
            }
        }
    }
}
