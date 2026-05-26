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
        Schema::create('school_quiz_answer_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_quiz_answer_id')
                ->constrained('school_quiz_answers')
                ->cascadeOnDelete()
                ->comment('Ответ');

            $t->string('locale', 10)->comment('Локаль');

            // Переводимые поля
            $t->text('text')->comment('Текст ответа');

            $t->text('explanation')
                ->nullable()
                ->comment('Пояснение к ответу');

            $t->timestamps();

            $t->unique(
                ['school_quiz_answer_id', 'locale'],
                'uq_school_answer_translation_locale'
            );

            $t->index('locale', 'idx_school_answer_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_answer_translations');
    }
};
