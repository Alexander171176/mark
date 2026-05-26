<?php

namespace Database\Seeders;

use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Seeder;

class SchoolQuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questionTypes = [
            1 => 'single_choice',
            2 => 'single_choice',
            3 => 'multiple_choice',
            4 => 'true_false',
            5 => 'open_text',
            6 => 'single_choice',
        ];

        $id = 1;

        for ($quizId = 1; $quizId <= 6; $quizId++) {
            foreach ($questionTypes as $questionNumber => $type) {
                SchoolQuizQuestion::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_quiz_id' => $quizId,
                        'sort' => $id,
                        'question_type' => $type,
                        'points' => match ($questionNumber) {
                            1, 2 => 1,
                            3, 4 => 2,
                            5 => 3,
                            default => 5,
                        },
                        'meta' => [
                            'source' => 'seeder',
                            'demo' => true,
                            'question_number' => $questionNumber,
                        ],
                        'activity' => true,
                    ]
                );

                $id++;
            }
        }
    }
}
