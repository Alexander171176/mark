<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Конкретный ответ пользователя в рамках попытки на конкретный вопрос
    public function up(): void
    {
        Schema::create('quiz_attempt_items', function (Blueprint $t) {
            $t->id();

            // Принадлежность элементу попытки
            $t->foreignId('quiz_attempt_id')
                ->constrained('quiz_attempts')
                ->cascadeOnDelete();

            // Какой вопрос
            $t->foreignId('quiz_question_id')
                ->constrained('quiz_questions')
                ->cascadeOnDelete();

            // Что выбрал пользователь (для закрытых вопросов),
            // для множественного выбора допускается хранить несколько записей,
            // либо JSON ниже — зависит от вашей логики.
            $t->foreignId('selected_answer_id')->nullable()
                ->constrained('quiz_answers')
                ->nullOnDelete();

            // Для множественного выбора можно хранить массив id ответов
            $t->json('selected_answer_ids')->nullable()
                ->comment('JSON-массив ID ответов для multiple-choice');

            // Для открытых вопросов
            $t->text('free_text_answer')->nullable()
                ->comment('Ответ пользователя (свободный ввод)');

            // Вычисление
            $t->boolean('is_correct')->default(false)
                ->comment('Автоматически определённая правильность (если применимо)');
            $t->unsignedSmallInteger('score')->default(0)
                ->comment('Начисленные баллы за этот вопрос');
            $t->unsignedSmallInteger('max_score')->default(0)
                ->comment('Максимальные баллы по вопросу');

            // Для ручной проверки/комментариев
            $t->text('reviewer_comment')->nullable()
                ->comment('Комментарий проверяющего (ручная проверка)');

            $t->timestamps();

            // Полезные индексы
            $t->index(['quiz_attempt_id', 'quiz_question_id'], 'idx_attempt_item_attempt_question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_items');
    }
};
