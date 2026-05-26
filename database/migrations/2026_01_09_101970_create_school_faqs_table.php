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
        Schema::create('school_faqs', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_faq_category_id')
                ->nullable()
                ->constrained('school_faq_categories')
                ->nullOnDelete()
                ->comment('Категория FAQ');

            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность');

            $t->timestamps();

            $t->index(['activity', 'sort'], 'idx_school_faq_active_sort');
            $t->index(['school_faq_category_id', 'sort'], 'idx_school_faq_cat_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_faqs');
    }
};
