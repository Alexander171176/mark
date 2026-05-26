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
        Schema::create('school_instructor_profile_translations', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('school_instructor_profile_id')
                ->comment('Связь с профилем инструктора');

            $t->foreign('school_instructor_profile_id', 'fk_school_instr_tr_profile')
                ->references('id')
                ->on('school_instructor_profiles')
                ->cascadeOnDelete();

            $t->string('locale', 10)->comment('Локаль (ru, en, kk)');

            // Переводимые поля
            $t->string('title')->nullable()->comment('Должность/позиция');
            $t->string('short', 255)->nullable()->comment('Краткое описание');
            $t->text('bio')->nullable()->comment('Биография (HTML/Markdown)');

            // SEO
            $t->string('meta_title', 255)->nullable()->comment('Meta Title');
            $t->string('meta_keywords', 255)->nullable()->comment('Meta Keywords');
            $t->text('meta_desc')->nullable()->comment('Meta Description');

            $t->timestamps();

            // Уникальность перевода
            $t->unique(
                ['school_instructor_profile_id', 'locale'],
                'uq_school_instructor_translation_locale'
            );

            $t->index('locale', 'idx_school_instructor_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_instructor_profile_translations');
    }
};
