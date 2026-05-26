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
        Schema::create('school_hashtag_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_hashtag_id')
                ->constrained('school_hashtags')
                ->cascadeOnDelete();

            $t->string('locale', 10);

            // Переводимые поля
            $t->string('name');
            $t->string('short', 255)->nullable();
            $t->text('description')->nullable();

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            $t->timestamps();

            $t->unique(['school_hashtag_id', 'locale'], 'uq_school_hashtag_translation_locale');
            $t->index(['locale', 'name'], 'uq_school_hashtag_translation_locale_name');

            $t->index('locale', 'idx_school_hashtag_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_hashtag_translations');
    }
};
