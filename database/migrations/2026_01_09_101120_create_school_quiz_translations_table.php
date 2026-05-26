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
        Schema::create('school_quiz_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_quiz_id')
                ->constrained('school_quizzes')
                ->cascadeOnDelete()
                ->comment('Квиз');

            $t->string('locale', 10)->comment('Локаль');

            // Переводимые поля
            $t->string('title')->comment('Название квиза');
            $t->text('short')->nullable()->comment('Краткое описание');
            $t->text('description')->nullable()->comment('Описание');

            $t->timestamps();

            $t->unique(['school_quiz_id', 'locale'], 'uq_school_quiz_translation_locale');
            $t->index(['locale', 'title'], 'uq_school_quiz_translation_title');

            $t->index('locale', 'idx_school_quiz_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_translations');
    }
};
