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
        Schema::create('school_faq_categories', function (Blueprint $t) {
            $t->id();

            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность');

            $t->string('slug')->unique()->comment('Уникальный ЧПУ категории');

            $t->timestamps();

            $t->index(['activity', 'sort'], 'idx_school_faq_cat_active_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_faq_categories');
    }
};
