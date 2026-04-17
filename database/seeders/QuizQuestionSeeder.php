<?php

namespace Database\Seeders;

use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('quizzes') || !Schema::hasTable('quiz_questions')) {
            $this->command?->warn('Таблицы quizzes/quiz_questions отсутствуют — пропускаю QuizQuestionSeeder.');
            return;
        }

        if (!Schema::hasTable('quiz_answers')) {
            $this->command?->warn('Таблица quiz_answers отсутствует — создам вопросы без вариантов.');
        }

        $quizzes = Quiz::query()->select('id','title','type')->get();
        if ($quizzes->isEmpty()) {
            $this->command?->warn('Квизы не найдены — сначала посейте QuizSeeder.');
            return;
        }

        foreach ($quizzes as $quiz) {
            $count = rand(5, 8);

            for ($i = 1; $i <= $count; $i++) {
                $sort = $i * 10;
                $qType    = $this->pickQuestionType($quiz->type);
                [$text, $explanation] = $this->makeQuestionText($qType, $quiz->title, $i);

                /** @var QuizQuestion $question */
                $question = QuizQuestion::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'sort' => $sort],
                    [
                        'question_type' => $qType,
                        'question_text' => $text,
                        'explanation'   => $explanation,
                        'points'        => [1,1,1,2,2,3][rand(0,5)],
                        'meta'          => [
                            'difficulty' => ['easy','easy','medium','medium','hard'][rand(0,4)],
                            'hint'       => rand(0,100) < 30 ? 'Подумайте о ключевых терминах из урока.' : null,
                        ],
                        'activity'     => true,
                    ]
                );

                // Если есть таблица ответов — создаём варианты (кроме open_text)
                if (Schema::hasTable('quiz_answers') && $question->question_type !== 'open_text') {
                    $this->seedAnswers($quiz->id, $question);
                }
            }
        }
    }

    private function pickQuestionType(string $quizType): string
    {
        if ($quizType === 'practice') {
            return collect(['true_false','true_false','open_text','single_choice','multiple_choice'])->random();
        }
        return collect(['single_choice','single_choice','multiple_choice','true_false','open_text'])->random();
    }

    private function makeQuestionText(string $type, string $quizTitle, int $i): array
    {
        switch ($type) {
            case 'true_false':
                return ["Утверждение верно? (Q{$i}) — {$quizTitle}", 'Пояснение: проверьте базовые определения в материале.'];
            case 'multiple_choice':
                return ["Выберите все корректные варианты (Q{$i}) — {$quizTitle}", 'Несколько правильных ответов. Внимательно перечитайте формулировки.'];
            case 'open_text':
                return ["Коротко опишите ключевую идею (Q{$i}) — {$quizTitle}", 'Ответ оценивается вручную. Приведите 1–2 примера из практики.'];
            default: // single_choice
                return ["Выберите один правильный вариант (Q{$i}) — {$quizTitle}", 'Только один ответ является правильным.'];
        }
    }

    private function seedAnswers(int $quizId, QuizQuestion $question): void
    {
        $make = function (int $sort, string $text, bool $correct = false) use ($quizId, $question) {
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
                    'weight'      => $correct ? 100 : 0,
                    'explanation' => null,
                    'activity'   => true,
                    'updated_at'  => now(),
                    'created_at'  => DB::table('quiz_answers')
                        ->where([
                            'quiz_id'          => $quizId,
                            'quiz_question_id' => $question->id,
                            'sort'         => $sort,
                        ])->exists() ? DB::raw('created_at') : now(),
                ]
            );
        };

        switch ($question->question_type) {
            case 'single_choice':
                $right = rand(1, 4);
                for ($i = 1; $i <= 4; $i++) {
                    $make($i * 10, "Вариант {$i}", $i === $right);
                }
                break;

            case 'multiple_choice':
                $rights = collect([1,2,3,4])->shuffle()->take(2)->all();
                for ($i = 1; $i <= 4; $i++) {
                    $make($i * 10, "Вариант {$i}", in_array($i, $rights, true));
                }
                break;

            case 'true_false':
                $isTrueCorrect = (bool)rand(0, 1);
                $make(10, 'Верно',   $isTrueCorrect);
                $make(20, 'Неверно', !$isTrueCorrect);
                break;
        }
    }
}
