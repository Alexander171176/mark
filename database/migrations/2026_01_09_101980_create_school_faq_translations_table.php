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
        Schema::create('school_faq_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_faq_id')
                ->constrained('school_faqs')
                ->cascadeOnDelete();

            $t->string('locale', 10);

            $t->string('question')->comment('Вопрос');
            $t->text('answer')->comment('Ответ');

            $t->string('meta_title', 160)->nullable();
            $t->string('meta_description', 255)->nullable();

            $t->timestamps();

            $t->unique(['school_faq_id', 'locale'], 'uq_school_faq_translation_locale');
            $t->index('locale', 'idx_school_faq_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_faq_translations');
    }
};
