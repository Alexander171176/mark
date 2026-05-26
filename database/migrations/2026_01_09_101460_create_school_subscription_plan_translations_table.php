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
        Schema::create('school_subscription_plan_translations', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('school_subscription_plan_id')
                ->comment('Тарифный план');

            $t->foreign('school_subscription_plan_id', 'fk_school_plan_tr_plan')
                ->references('id')
                ->on('school_subscription_plans')
                ->cascadeOnDelete();

            $t->string('locale', 10)->comment('Локаль');

            // Переводимые поля
            $t->string('title')->comment('Название тарифа');
            $t->string('subtitle')->nullable()->comment('Подзаголовок');
            $t->string('short', 255)->nullable()->comment('Краткое описание');
            $t->text('description')->nullable()->comment('Описание');

            // SEO
            $t->string('meta_title', 160)->nullable()->comment('Meta Title');
            $t->string('meta_keywords', 255)->nullable()->comment('Meta Keywords');
            $t->string('meta_desc', 255)->nullable()->comment('Meta Description');

            $t->timestamps();

            $t->unique(
                ['school_subscription_plan_id', 'locale'],
                'uq_school_plan_translation_locale'
            );

            $t->index(
                ['locale', 'title'],
                'uq_school_plan_translation_title'
            );

            $t->index('locale', 'idx_school_plan_translation_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscription_plan_translations');
    }
};
