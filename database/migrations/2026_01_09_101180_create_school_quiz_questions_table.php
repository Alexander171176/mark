<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_quiz_questions', function (Blueprint $t) {
            $t->id();

            // Квиз
            $t->foreignId('school_quiz_id')
                ->constrained('school_quizzes')
                ->cascadeOnDelete()
                ->comment('Квиз');

            // Порядок
            $t->unsignedInteger('sort')
                ->default(0)
                ->comment('Порядок отображения');

            // Тип вопроса
            $t->enum('question_type', [
                'single_choice',
                'multiple_choice',
                'true_false',
                'open_text',
            ])->default('single_choice')
                ->comment('Тип вопроса');

            // Баллы
            $t->unsignedSmallInteger('points')
                ->default(1)
                ->comment('Баллы за вопрос');

            // Метаданные (варианты, медиа и т.д.)
            $t->json('meta')
                ->nullable()
                ->comment('Доп. настройки');

            // Активность
            $t->boolean('activity')
                ->default(true)
                ->comment('Активность');

            $t->timestamps();

            // Индексы
            $t->index(['school_quiz_id', 'sort'], 'idx_school_question_order');
            $t->index(['school_quiz_id', 'activity'], 'idx_school_question_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_questions');
    }
};
