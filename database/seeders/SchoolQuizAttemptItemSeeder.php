<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Seeder;

class SchoolQuizAttemptItemSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        $attempts = SchoolQuizAttempt::orderBy('id')->get();

        foreach ($attempts as $attempt) {
            $questions = SchoolQuizQuestion::where('school_quiz_id', $attempt->school_quiz_id)
                ->orderBy('sort')
                ->get();

            foreach ($questions as $question) {
                $answers = SchoolQuizAnswer::where('school_quiz_question_id', $question->id)
                    ->orderBy('sort')
                    ->get();

                $correctAnswers = $answers->where('is_correct', true)->values();

                $selectedAnswerId = null;
                $selectedAnswerIds = null;
                $freeTextAnswer = null;

                if ($question->question_type === 'multiple_choice') {
                    $selectedAnswerIds = $correctAnswers->pluck('id')->values()->toArray();
                } elseif ($question->question_type === 'open_text') {
                    $freeTextAnswer = 'Демо-ответ пользователя по теме урока.';
                } else {
                    $selectedAnswerId = $correctAnswers->first()?->id;
                }

                SchoolQuizAttemptItem::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_quiz_attempt_id' => $attempt->id,
                        'school_quiz_question_id' => $question->id,

                        'selected_answer_id' => $selectedAnswerId,
                        'selected_answer_ids' => $selectedAnswerIds,
                        'free_text_answer' => $freeTextAnswer,

                        'is_correct' => true,
                        'score' => $question->points,
                        'max_score' => $question->points,

                        'reviewer_comment' => $question->question_type === 'open_text'
                            ? 'Ответ принят в демонстрационных данных.'
                            : null,
                    ]
                );

                $id++;
            }
        }
    }
}
