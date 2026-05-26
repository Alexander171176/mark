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
        Schema::create('school_hashtags', function (Blueprint $t) {
            $t->id();

            // Управление порядком и активностью
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('activity')->default(true);

            // Основные непереводимые поля
            $t->string('slug');
            $t->string('color', 16)->nullable();

            // Статистика
            $t->unsignedBigInteger('views')->default(0);
            $t->unsignedBigInteger('likes')->default(0);

            $t->timestamps();

            $t->unique('slug', 'uq_school_hashtag_slug');

            $t->index(['activity', 'sort'], 'idx_school_hashtag_activity_sort');
            $t->index('views', 'idx_school_hashtag_views');
            $t->index('likes', 'idx_school_hashtag_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_hashtags');
    }
};
