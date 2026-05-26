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
        Schema::create('school_track_translations', function (Blueprint $t) {
            $t->id();

            // Связь с категорией обучения
            $t->foreignId('school_track_id')
                ->constrained('school_tracks')
                ->cascadeOnDelete()
                ->comment('Категория обучения');

            // Локаль перевода
            $t->string('locale', 10)->comment('Локаль перевода: ru, en, kk');

            // Переводимые поля
            $t->string('name', 255)->comment('Название категории обучения');
            $t->string('short', 255)->nullable()->comment('Краткое описание');
            $t->text('description')->nullable()->comment('Описание');

            // SEO
            $t->string('meta_title', 255)->nullable()->comment('Meta Title');
            $t->string('meta_keywords', 255)->nullable()->comment('Meta Keywords');
            $t->text('meta_desc')->nullable()->comment('Meta Description');

            $t->timestamps();

            // Уникальности
            $t->unique(['school_track_id', 'locale'], 'uq_school_track_translation_locale');
            $t->index(['locale', 'name'], 'uq_school_track_translation_locale_name');

            // Индексы
            $t->index('locale', 'idx_school_track_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_track_translations');
    }
};
