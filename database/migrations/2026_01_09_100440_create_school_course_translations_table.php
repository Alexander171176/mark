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
        Schema::create('school_course_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            $t->string('locale', 10)->comment('Локаль перевода: ru, en, kk');

            // Переводимые поля
            $t->string('title')->comment('Название курса');
            $t->string('subtitle')->nullable()->comment('Подзаголовок/оффер курса');
            $t->text('short')->nullable()->comment('Краткое описание курса');
            $t->longText('description')->nullable()->comment('Полное описание курса');

            // SEO
            $t->string('meta_title', 160)->nullable()->comment('Meta Title');
            $t->string('meta_keywords', 255)->nullable()->comment('Meta Keywords');
            $t->string('meta_desc', 255)->nullable()->comment('Meta Description');

            $t->timestamps();

            $t->unique(['school_course_id', 'locale'], 'uq_school_course_translation_locale');
            $t->index(['locale', 'title'], 'uq_school_course_translation_locale_title');

            $t->index('locale', 'idx_school_course_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_translations');
    }
};
