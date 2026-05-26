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
        Schema::create('school_assignment_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_assignment_id')
                ->constrained('school_assignments')
                ->cascadeOnDelete()
                ->comment('Задание');

            $t->string('locale', 10)->comment('Локаль');

            // Переводимые поля
            $t->string('title')->comment('Название задания');
            $t->string('subtitle')->nullable()->comment('Подзаголовок');
            $t->text('short')->nullable()->comment('Краткое описание');
            $t->text('description')->nullable()->comment('Описание');
            $t->longText('instructions')->nullable()->comment('Инструкции');

            $t->timestamps();

            $t->unique(['school_assignment_id', 'locale'], 'uq_school_assignment_translation_locale');
            $t->index(['locale', 'title'], 'uq_school_assignment_translation_title');

            $t->index('locale', 'idx_school_assignment_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_assignment_translations');
    }
};
