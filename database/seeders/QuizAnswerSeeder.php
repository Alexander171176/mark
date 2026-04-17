<?php

namespace Database\Seeders;

use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAnswer\QuizAnswer;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuizAnswerSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('quizzes') ||
            !Schema::hasTable('quiz_questions') ||
            !Schema::hasTable('quiz_answers')
        ) {
            $this->command?->warn('Нет одной из таблиц quizzes/quiz_questions/quiz_answers — пропускаю QuizAnswerSeeder.');
            return;
        }

        // Берём активные квизы с вопросами
        $quizzes = Quiz::query()
            ->with(['questions' => function ($q) {
                $q->orderBy('sort');
            }])
            ->get(['id','title','type']);

        if ($quizzes->isEmpty()) {
            $this->command?->warn('Квизы не найдены — сперва запустите QuizSeeder/QuizQuestionSeeder.');
            return;
        }

        DB::transaction(function () use ($quizzes) {
            foreach ($quizzes as $quiz) {
                foreach ($quiz->questions as $question) {
                    // Пропускаем, если ответы уже есть (идемпотентность)
                    $exists = QuizAnswer::query()
                        ->where('quiz_id', $quiz->id)
                        ->where('quiz_question_id', $question->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $this->seedAnswersForQuestion($quiz->id, $question);
                }
            }
        });
    }

    private function seedAnswersForQuestion(int $quizId, QuizQuestion $question): void
    {
        $make = function (int $sort, string $text, bool $correct = false, int $weight = 0, ?string $explanation = null) use ($quizId, $question) {
            // upsert по (quiz_id, quiz_question_id, sort)
            DB::table('quiz_answers')->updateOrInsert(
                [
                    'quiz_id'          => $quizId,
                    'quiz_question_id' => $question->id,
                    'sort'             => $sort,
                ],
                [
                    'text'        => $text,
                    'is_correct'  => $correct,
                    'weight'      => $weight,
                    'explanation' => $explanation,
                    'activity'    => true,
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );
        };

        switch ($question->question_type) {
            case 'true_false':
                $trueIsCorrect = (bool)rand(0,1);
                $make(10, 'Верно',   $trueIsCorrect,   $trueIsCorrect ? 100 : 0);
                $make(20, 'Неверно', !$trueIsCorrect, !$trueIsCorrect ? 100 : 0);
                break;

            case 'single_choice':
                $correct = rand(1, 4);
                for ($i = 1; $i <= 4; $i++) {
                    $make($i * 10, "Вариант {$i}", $i === $correct, $i === $correct ? 100 : 0);
                }
                break;

            case 'multiple_choice':
                // 2–3 правильных варианта, вес по 100 для правильных (частичное начисление сделаем по доле правильных)
                $countCorrect = rand(2, 3);
                $correctSet   = collect([1,2,3,4])->shuffle()->take($countCorrect)->all();
                for ($i = 1; $i <= 4; $i++) {
                    $isRight = in_array($i, $correctSet, true);
                    $make($i * 10, "Вариант {$i}", $isRight, $isRight ? 100 : 0);
                }
                break;

            case 'open_text':
            default:
                // Для открытого вопроса вариантов нет
                break;
        }
    }
}
