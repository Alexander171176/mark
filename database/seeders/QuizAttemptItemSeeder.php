<?php

namespace Database\Seeders;

use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAnswer\QuizAnswer;
use App\Models\Admin\School\QuizAttempt\QuizAttempt;
use App\Models\Admin\School\QuizAttemptItem\QuizAttemptItem;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuizAttemptItemSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('users') ||
            !Schema::hasTable('quizzes') ||
            !Schema::hasTable('quiz_questions') ||
            !Schema::hasTable('quiz_answers') ||
            !Schema::hasTable('quiz_attempts') ||
            !Schema::hasTable('quiz_attempt_items')
        ) {
            $this->command?->warn('Нет одной из таблиц users/quizzes/quiz_questions/quiz_answers/quiz_attempts/quiz_attempt_items — пропускаю QuizAttemptItemSeeder.');
            return;
        }

        $users = User::query()->inRandomOrder()->limit(8)->get(['id','name']);
        $quizzes = Quiz::query()
            ->with(['questions' => function ($q) { $q->orderBy('sort'); }])
            ->whereHas('questions')
            ->inRandomOrder()
            ->limit(5)
            ->get(['id','title','type','course_id','module_id','lesson_id','attempts_limit','time_limit_minutes','pass_score']);

        if ($users->isEmpty() || $quizzes->isEmpty()) {
            $this->command?->warn('Недостаточно пользователей/квизов для генерации попыток.');
            return;
        }

        DB::transaction(function () use ($users, $quizzes) {
            foreach ($users as $user) {
                // Не будем перегружать — 1–2 квиза на пользователя
                $forUser = $quizzes->shuffle()->take(rand(1, 2));
                foreach ($forUser as $quiz) {
                    // 1–2 попытки на квиз
                    $attemptsToCreate = rand(1, 2);
                    for ($a = 1; $a <= $attemptsToCreate; $a++) {
                        $this->makeAttemptWithItems($user->id, $quiz);
                    }
                }
            }
        });
    }

    private function makeAttemptWithItems(int $userId, Quiz $quiz): void
    {
        // Определяем следующий attempt_number для (user, quiz)
        $last = QuizAttempt::query()
            ->where('user_id', $userId)
            ->where('quiz_id', $quiz->id)
            ->max('attempt_number');
        $nextNumber = ($last ?? 0) + 1;

        $attempt = QuizAttempt::create([
            'user_id'        => $userId,
            'quiz_id'        => $quiz->id,
            'course_id'      => $quiz->course_id,
            'module_id'      => $quiz->module_id,
            'lesson_id'      => $quiz->lesson_id,
            'attempt_number' => $nextNumber,
            'status'         => 'in_progress',
            'started_at'     => now()->subMinutes(rand(5, 45)),
            'ip_address'     => '127.0.0.1',
            'user_agent'     => 'SeederBot/1.0',
        ]);

        $totalScore = 0;
        $totalMax   = 0;

        foreach ($quiz->questions as $question) {
            $points = $question->points ?? 1;
            $totalMax += $points;

            [$isCorrect, $score, $selectedAnswerId, $selectedAnswerIds, $freeText] =
                $this->simulateAnswer($quiz->id, $question, $points);

            QuizAttemptItem::create([
                'quiz_attempt_id'     => $attempt->id,
                'quiz_question_id'    => $question->id,
                'selected_answer_id'  => $selectedAnswerId,
                'selected_answer_ids' => $selectedAnswerIds,
                'free_text_answer'    => $freeText,
                'is_correct'          => $isCorrect,
                'score'               => $score,
                'max_score'           => $points,
                'reviewer_comment'    => null,
            ]);

            $totalScore += $score;
        }

        // Закрываем попытку
        $attempt->score       = $totalScore;
        $attempt->max_score   = $totalMax;
        $attempt->recalcPercent();
        $attempt->markFinished(); // выставит finished_at, duration_seconds и статус=completed
        $attempt->save();
    }

    /**
     * Возвращает: [is_correct, score, selected_answer_id, selected_answer_ids(json array), free_text]
     */
    private function simulateAnswer(int $quizId, QuizQuestion $question, int $points): array
    {
        $selectedAnswerId  = null;
        $selectedAnswerIds = null;
        $freeText          = null;
        $isCorrect         = false;
        $score             = 0;

        switch ($question->question_type) {
            case 'true_false': {
                $answers = QuizAnswer::query()
                    ->where('quiz_id', $quizId)
                    ->where('quiz_question_id', $question->id)
                    ->orderBy('sort')
                    ->get(['id','is_correct']);
                if ($answers->count() >= 2) {
                    $picked = $answers->random();
                    $selectedAnswerId = $picked->id;
                    $isCorrect = (bool)$picked->is_correct;
                    $score = $isCorrect ? $points : 0;
                }
                break;
            }

            case 'single_choice': {
                $answers = QuizAnswer::query()
                    ->where('quiz_id', $quizId)
                    ->where('quiz_question_id', $question->id)
                    ->orderBy('sort')
                    ->get(['id','is_correct']);
                if ($answers->isNotEmpty()) {
                    // иногда «угадываем» намеренно правильно (~55%)
                    $picked = (rand(0,100) < 55)
                        ? $answers->firstWhere('is_correct', 1) ?? $answers->random()
                        : $answers->random();

                    $selectedAnswerId = $picked->id;
                    $isCorrect = (bool)$picked->is_correct;
                    $score = $isCorrect ? $points : 0;
                }
                break;
            }

            case 'multiple_choice': {
                $answers = QuizAnswer::query()
                    ->where('quiz_id', $quizId)
                    ->where('quiz_question_id', $question->id)
                    ->orderBy('sort')
                    ->get(['id','is_correct']);
                if ($answers->isNotEmpty()) {
                    $correctIds = $answers->where('is_correct', 1)->pluck('id')->values();
                    $wrongIds   = $answers->where('is_correct', 0)->pluck('id')->values();

                    // Пользователь выбирает 1..N вариантов, чаще ближе к правильным
                    $pickCount = max(1, min($answers->count(), rand(1, 3)));
                    $picked = collect();

                    // добавим часть правильных
                    $picked = $picked->merge($correctIds->shuffle()->take(rand(1, min($pickCount, $correctIds->count()))));
                    // доберём до нужного объёма случайными
                    if ($picked->count() < $pickCount) {
                        $picked = $picked->merge(
                            $wrongIds->shuffle()->take($pickCount - $picked->count())
                        );
                    }

                    $picked = $picked->unique()->values();
                    $selectedAnswerIds = $picked->all();

                    $numCorrectPicked = $picked->intersect($correctIds)->count();
                    $numCorrectTotal  = max(1, $correctIds->count());
                    $fraction         = $numCorrectPicked / $numCorrectTotal;
                    $score            = (int) round($points * $fraction);
                    $isCorrect        = $numCorrectPicked === $numCorrectTotal;
                }
                break;
            }

            case 'open_text':
            default: {
                // Небольшой текст; оставляем на ручную проверку
                $freeText = 'Мой краткий ответ из сидера.';
                $isCorrect = false;
                $score = 0;
                break;
            }
        }

        return [$isCorrect, $score, $selectedAnswerId, $selectedAnswerIds, $freeText];
    }
}
