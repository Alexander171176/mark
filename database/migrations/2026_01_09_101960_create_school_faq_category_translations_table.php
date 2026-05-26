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
        Schema::create('school_faq_category_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_faq_category_id')
                ->constrained('school_faq_categories')
                ->cascadeOnDelete();

            $t->string('locale', 10);

            $t->string('title')->comment('Название категории');
            $t->text('description')->nullable()->comment('Описание категории');

            $t->timestamps();

            $t->unique(
                ['school_faq_category_id', 'locale'],
                'uq_school_faq_cat_translation_locale'
            );

            $t->index(
                ['locale', 'title'],
                'uq_school_faq_cat_translation_title'
            );

            $t->index('locale', 'idx_school_faq_cat_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_faq_category_translations');
    }
};
