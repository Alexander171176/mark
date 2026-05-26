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
        Schema::create('school_quiz_attempt_items', function (Blueprint $t) {
            $t->id();

            // Попытка
            $t->foreignId('school_quiz_attempt_id')
                ->constrained('school_quiz_attempts')
                ->cascadeOnDelete()
                ->comment('Попытка прохождения квиза');

            // Вопрос
            $t->foreignId('school_quiz_question_id')
                ->constrained('school_quiz_questions')
                ->cascadeOnDelete()
                ->comment('Вопрос квиза');

            // Выбранный ответ
            $t->foreignId('selected_answer_id')->nullable()
                ->constrained('school_quiz_answers')
                ->nullOnDelete()
                ->comment('Выбранный ответ');

            // Несколько выбранных ответов
            $t->json('selected_answer_ids')
                ->nullable()
                ->comment('JSON-массив ID ответов');

            // Открытый ответ
            $t->text('free_text_answer')
                ->nullable()
                ->comment('Свободный ответ пользователя');

            // Проверка
            $t->boolean('is_correct')->default(false)->comment('Правильность ответа');
            $t->unsignedSmallInteger('score')->default(0)->comment('Начисленные баллы');
            $t->unsignedSmallInteger('max_score')->default(0)->comment('Максимальные баллы');

            // Ручная проверка
            $t->text('reviewer_comment')
                ->nullable()
                ->comment('Комментарий проверяющего');

            $t->timestamps();

            $t->index(
                ['school_quiz_attempt_id', 'school_quiz_question_id'],
                'idx_school_attempt_item_question'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_attempt_items');
    }
};
