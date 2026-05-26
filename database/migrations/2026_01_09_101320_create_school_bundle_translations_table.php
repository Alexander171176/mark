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
        Schema::create('school_bundle_translations', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_bundle_id')
                ->constrained('school_bundles')
                ->cascadeOnDelete()
                ->comment('Набор курсов');

            $t->string('locale', 10)->comment('Локаль');

            $t->string('title')->comment('Название набора');
            $t->string('subtitle')->nullable()->comment('Подзаголовок');
            $t->text('short')->nullable()->comment('Краткое описание');
            $t->text('description')->nullable()->comment('Описание');

            $t->string('meta_title', 160)->nullable()->comment('Meta Title');
            $t->string('meta_keywords', 255)->nullable()->comment('Meta Keywords');
            $t->string('meta_desc', 255)->nullable()->comment('Meta Description');

            $t->timestamps();

            $t->unique(['school_bundle_id', 'locale'], 'uq_school_bundle_translation_locale');
            $t->index(['locale', 'title'], 'uq_school_bundle_translation_title');

            $t->index('locale', 'idx_school_bundle_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bundle_translations');
    }
};
