<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerTranslation;
use Illuminate\Database\Seeder;

class SchoolQuizAnswerTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $texts = [
            'ru' => [
                1 => 'Правильный вариант ответа',
                2 => 'Неполный вариант ответа',
                3 => 'Дополнительный правильный вариант',
                4 => 'Неверный вариант ответа',
            ],
            'en' => [
                1 => 'Correct answer option',
                2 => 'Incomplete answer option',
                3 => 'Additional correct option',
                4 => 'Incorrect answer option',
            ],
            'kk' => [
                1 => 'Дұрыс жауап нұсқасы',
                2 => 'Толық емес жауап нұсқасы',
                3 => 'Қосымша дұрыс нұсқа',
                4 => 'Қате жауап нұсқасы',
            ],
        ];

        $explanations = [
            'ru' => 'Пояснение помогает понять, почему этот вариант считается правильным или неправильным.',
            'en' => 'The explanation helps understand why this option is considered correct or incorrect.',
            'kk' => 'Түсіндірме бұл нұсқаның неге дұрыс немесе қате екенін түсінуге көмектеседі.',
        ];

        $answerPosition = [];

        $answers = SchoolQuizAnswer::orderBy('id')->get();

        foreach ($answers as $answer) {
            $questionId = $answer->school_quiz_question_id;

            if (!isset($answerPosition[$questionId])) {
                $answerPosition[$questionId] = 0;
            }

            $answerPosition[$questionId]++;

            $position = $answerPosition[$questionId];

            foreach (['ru', 'en', 'kk'] as $locale) {
                SchoolQuizAnswerTranslation::updateOrCreate(
                    [
                        'school_quiz_answer_id' => $answer->id,
                        'locale' => $locale,
                    ],
                    [
                        'text' => $texts[$locale][$position] ?? $texts[$locale][1],
                        'explanation' => $explanations[$locale],
                    ]
                );
            }
        }
    }
}
