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
        Schema::create('school_quiz_answers', function (Blueprint $t) {
            $t->id();

            // Квиз
            $t->foreignId('school_quiz_id')
                ->constrained('school_quizzes')
                ->cascadeOnDelete()
                ->comment('Квиз');

            // Вопрос квиза
            $t->foreignId('school_quiz_question_id')
                ->constrained('school_quiz_questions')
                ->cascadeOnDelete()
                ->comment('Вопрос квиза');

            // Правильность ответа
            $t->boolean('is_correct')
                ->default(false)
                ->comment('Правильный ответ');

            // Вес ответа
            $t->unsignedTinyInteger('weight')
                ->default(0)
                ->comment('Вес ответа 0-100');

            // Порядок сортировки
            $t->unsignedInteger('sort')
                ->default(0)
                ->comment('Порядок отображения');

            // Активность
            $t->boolean('activity')
                ->default(true)
                ->comment('Активность ответа');

            $t->timestamps();

            // Индексы
            $t->index(
                ['school_quiz_id', 'school_quiz_question_id'],
                'idx_school_answer_quiz_question'
            );

            $t->index(
                ['activity', 'sort'],
                'idx_school_answer_active_sort'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_answers');
    }
};
