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
        Schema::create('school_quiz_question_translations', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('school_quiz_question_id')
                ->comment('Вопрос');

            $t->foreign('school_quiz_question_id', 'fk_school_q_question_tr')
                ->references('id')
                ->on('school_quiz_questions')
                ->cascadeOnDelete();

            $t->string('locale', 10)->comment('Локаль');

            // Переводимые поля
            $t->text('question_text')
                ->comment('Текст вопроса');

            $t->text('explanation')
                ->nullable()
                ->comment('Объяснение ответа');

            $t->timestamps();

            $t->unique(
                ['school_quiz_question_id', 'locale'],
                'uq_school_question_translation_locale'
            );

            $t->index('locale', 'idx_school_question_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_question_translations');
    }
};
