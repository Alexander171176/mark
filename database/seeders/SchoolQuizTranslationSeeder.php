<?php

namespace Database\Seeders;

use App\Models\Admin\School\Quiz\SchoolQuizTranslation;
use Illuminate\Database\Seeder;

class SchoolQuizTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = [
            1 => [
                'ru' => ['Базовая проверка знаний', 'Короткая викторина для проверки основных понятий.'],
                'en' => ['Basic Knowledge Check', 'A short quiz to check the main concepts.'],
                'kk' => ['Базалық білімді тексеру', 'Негізгі ұғымдарды тексеруге арналған қысқа викторина.'],
            ],
            2 => [
                'ru' => ['Теория и практика', 'Вопросы по теории и практическим примерам урока.'],
                'en' => ['Theory and Practice', 'Questions about lesson theory and practical examples.'],
                'kk' => ['Теория және практика', 'Сабақ теориясы мен практикалық мысалдары бойынша сұрақтар.'],
            ],
            3 => [
                'ru' => ['Понимание кода', 'Проверка умения читать и анализировать код.'],
                'en' => ['Code Understanding', 'Checking the ability to read and analyze code.'],
                'kk' => ['Кодты түсіну', 'Кодты оқу және талдау қабілетін тексеру.'],
            ],
            4 => [
                'ru' => ['Навыки отладки', 'Викторина по поиску ошибок и исправлению проблем.'],
                'en' => ['Debugging Skills', 'A quiz about finding errors and fixing issues.'],
                'kk' => ['Қатені түзету дағдылары', 'Қателерді табу және мәселелерді түзету бойынша викторина.'],
            ],
            5 => [
                'ru' => ['Архитектурное мышление', 'Проверка понимания структуры приложения и связей между компонентами.'],
                'en' => ['Architecture Thinking', 'Checking understanding of application structure and component relationships.'],
                'kk' => ['Архитектуралық ойлау', 'Қосымша құрылымы мен компоненттер байланысын түсінуді тексеру.'],
            ],
            6 => [
                'ru' => ['Итоговая викторина', 'Финальная проверка знаний по материалам урока.'],
                'en' => ['Final Quiz', 'A final knowledge check based on the lesson materials.'],
                'kk' => ['Қорытынды викторина', 'Сабақ материалдары бойынша қорытынды білім тексеруі.'],
            ],
        ];

        foreach ($quizzes as $quizId => $translations) {
            foreach ($translations as $locale => [$title, $short]) {
                $description = match ($locale) {
                    'ru' => "Викторина «{$title}» помогает закрепить материал, проверить понимание ключевых тем и подготовиться к следующим урокам.",
                    'en' => "The “{$title}” quiz helps reinforce the material, check understanding of key topics, and prepare for the next lessons.",
                    'kk' => "«{$title}» викторинасы материалды бекітуге, негізгі тақырыптарды түсінуді тексеруге және келесі сабақтарға дайындалуға көмектеседі.",
                };

                SchoolQuizTranslation::updateOrCreate(
                    [
                        'school_quiz_id' => $quizId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'short' => $short,
                        'description' => $description,
                    ]
                );
            }
        }
    }
}
