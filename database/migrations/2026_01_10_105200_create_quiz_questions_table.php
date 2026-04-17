<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Вопросы квизов
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $t) {
            $t->id();

            $t->foreignId('quiz_id')
                ->constrained('quizzes')
                ->cascadeOnDelete();                 // удаление квиза удалит его вопросы

            $t->unsignedInteger('sort')->default(0)
                ->comment('Порядок отображения в квизе');

            $t->enum('question_type', [
                'single_choice',   // один правильный ответ
                'multiple_choice', // несколько правильных ответов
                'true_false',      // верно/неверно
                'open_text',       // развернутый ответ
            ])->default('single_choice');

            $t->text('question_text');            // текст вопроса (HTML/Markdown)
            $t->text('explanation')->nullable();  // объяснение правильного ответа

            $t->unsignedSmallInteger('points')->default(1)
                ->comment('Сколько баллов даёт вопрос');

            $t->json('meta')->nullable()
                ->comment('Произвольные настройки вопроса (подсказки, медиа, варианты и пр.)');

            $t->boolean('activity')->default(true); // скрывать/показывать вопрос
            $t->timestamps();

            // Индексы
            $t->index(['quiz_id', 'sort'], 'idx_question_order');
            $t->index(['quiz_id', 'activity'], 'idx_question_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
