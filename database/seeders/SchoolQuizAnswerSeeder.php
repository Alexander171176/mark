<?php

namespace Database\Seeders;

use App\Models\Admin\School\QuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Seeder;

class SchoolQuizAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        for ($questionId = 1; $questionId <= 36; $questionId++) {
            $question = SchoolQuizQuestion::find($questionId);

            if (!$question) {
                continue;
            }

            $answersCount = match ($question->question_type) {
                'true_false' => 2,
                'open_text' => 1,
                default => 4,
            };

            for ($answerNumber = 1; $answerNumber <= $answersCount; $answerNumber++) {
                $isCorrect = match ($question->question_type) {
                    'multiple_choice' => in_array($answerNumber, [1, 3], true),
                    'true_false' => $answerNumber === 1,
                    'open_text' => true,
                    default => $answerNumber === 1,
                };

                SchoolQuizAnswer::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_quiz_id' => $question->school_quiz_id,
                        'school_quiz_question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'weight' => $isCorrect ? 100 : 0,
                        'sort' => $id,
                        'activity' => true,
                    ]
                );

                $id++;
            }
        }
    }
}
