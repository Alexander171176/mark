<?php

namespace Database\Seeders;

use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestionTranslation;
use Illuminate\Database\Seeder;

class SchoolQuizQuestionTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            1 => [
                'ru' => ['Что является основной целью изучения этой темы?', 'Правильный ответ помогает понять назначение темы и её место в разработке.'],
                'en' => ['What is the main goal of studying this topic?', 'The correct answer helps understand the purpose of the topic and its role in development.'],
                'kk' => ['Бұл тақырыпты оқудың негізгі мақсаты қандай?', 'Дұрыс жауап тақырыптың мақсатын және әзірлеудегі орнын түсінуге көмектеседі.'],
            ],
            2 => [
                'ru' => ['Какой инструмент чаще всего используется в этой теме?', 'Инструмент выбирается исходя из задач урока и практического применения.'],
                'en' => ['Which tool is most commonly used in this topic?', 'The tool is selected based on lesson tasks and practical usage.'],
                'kk' => ['Бұл тақырыпта қай құрал жиі қолданылады?', 'Құрал сабақ тапсырмалары мен практикалық қолдануға қарай таңдалады.'],
            ],
            3 => [
                'ru' => ['Какие утверждения относятся к хорошей практике разработки?', 'Хорошая практика помогает писать понятный, поддерживаемый и надёжный код.'],
                'en' => ['Which statements describe good development practices?', 'Good practices help write clear, maintainable, and reliable code.'],
                'kk' => ['Қай тұжырымдар жақсы әзірлеу тәжірибесіне жатады?', 'Жақсы тәжірибе түсінікті, қолдауға ыңғайлы және сенімді код жазуға көмектеседі.'],
            ],
            4 => [
                'ru' => ['Верно ли, что практические задания помогают лучше закрепить материал?', 'Практика помогает превратить теорию в реальный навык.'],
                'en' => ['Is it true that practical tasks help reinforce the material better?', 'Practice helps turn theory into a real skill.'],
                'kk' => ['Практикалық тапсырмалар материалды жақсы бекітуге көмектесе ме?', 'Практика теорияны нақты дағдыға айналдыруға көмектеседі.'],
            ],
            5 => [
                'ru' => ['Опишите своими словами, как вы примените эту тему в проекте.', 'Открытый ответ показывает глубину понимания и умение применять знания.'],
                'en' => ['Describe in your own words how you would apply this topic in a project.', 'An open answer shows depth of understanding and ability to apply knowledge.'],
                'kk' => ['Бұл тақырыпты жобада қалай қолданатыныңызды өз сөзіңізбен сипаттаңыз.', 'Ашық жауап түсіну тереңдігін және білімді қолдану қабілетін көрсетеді.'],
            ],
            6 => [
                'ru' => ['Какой следующий шаг после изучения теории?', 'После теории важно перейти к примерам, практике и самостоятельной работе.'],
                'en' => ['What is the next step after studying theory?', 'After theory, it is important to move on to examples, practice, and independent work.'],
                'kk' => ['Теорияны оқығаннан кейінгі келесі қадам қандай?', 'Теориядан кейін мысалдарға, практикаға және өздік жұмысқа көшу маңызды.'],
            ],
        ];

        $id = 1;

        for ($quizId = 1; $quizId <= 6; $quizId++) {
            foreach ($questions as $questionNumber => $translations) {
                foreach ($translations as $locale => [$questionText, $explanation]) {
                    $quizPrefix = match ($locale) {
                        'ru' => "Викторина {$quizId}",
                        'en' => "Quiz {$quizId}",
                        'kk' => "{$quizId}-викторина",
                    };

                    SchoolQuizQuestionTranslation::updateOrCreate(
                        [
                            'school_quiz_question_id' => $id,
                            'locale' => $locale,
                        ],
                        [
                            'question_text' => $quizPrefix . ': ' . $questionText,
                            'explanation' => $explanation,
                        ]
                    );
                }

                $id++;
            }
        }
    }
}
