<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Ответы на вопросы квиза
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $t) {
            $t->id();

            // Прямая ссылка на квиз — удобно для быстрых выборок ответов по квизу
            $t->foreignId('quiz_id')
                ->constrained('quizzes')
                ->cascadeOnDelete(); // удаляем ответы вместе с квизом

            // Ссылка на вопрос, к которому относится ответ
            $t->foreignId('quiz_question_id')
                ->constrained('quiz_questions')
                ->cascadeOnDelete(); // удаляем ответы вместе с вопросом

            $t->text('text'); // Текст ответа (вариант ответа)

            $t->boolean('is_correct')->default(false)
                ->comment('Флаг правильного ответа');

            $t->unsignedTinyInteger('weight')->default(0)
                ->comment('Вес ответа для частичного начисления баллов (0..100)');

            $t->unsignedInteger('sort')->default(0)
                ->comment('Порядок для сортировки вариантов');

            $t->text('explanation')->nullable()
                ->comment('Пояснение/объяснение к ответу (показывать после попытки)');

            $t->boolean('activity')->default(true)
                ->comment('Публикация варианта ответа');

            $t->timestamps();

            // Полезные индексы
            $t->index(['quiz_id', 'quiz_question_id'], 'idx_answer_quiz_question');
            $t->index(['activity', 'sort'], 'idx_answer_active_pos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
