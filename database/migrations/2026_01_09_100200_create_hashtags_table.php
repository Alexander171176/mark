<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hashtags', function (Blueprint $t) {
            $t->id();

            // Управление порядком и активностью
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('activity')->default(true);

            // Основные поля
            $t->string('name');                 // Человеческое имя хештега (#Laravel)
            $t->string('slug');                 // Слаг, например "laravel", "vuejs"
            $t->string('locale', 10)->nullable()
                ->comment('Локаль хештега (ru/en/kk и т.п.) или null, если глобальный');

            // Оформление / краткое описание
            $t->string('color', 16)->nullable();
            $t->string('short', 255)->nullable();
            $t->text('description')->nullable();

            // Статистика
            $t->unsignedBigInteger('views')->default(0);
            $t->unsignedBigInteger('likes')->default(0); // 👍 Лайки/реакции

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            // Тех. поля
            $t->timestamps();
            $t->softDeletes();

            /* ---------- Уникальности ---------- */
            $t->unique(['locale', 'slug'], 'uq_hashtag_locale_slug');
            $t->unique(['locale', 'name'], 'uq_hashtag_locale_name');

            /* ---------- Индексы ---------- */
            $t->index(['locale', 'sort'], 'idx_hashtag_locale_sort');
            $t->index(['locale', 'activity'], 'idx_hashtag_locale_activity');
            $t->index(['activity', 'sort'], 'idx_hashtag_activity_sort');
            $t->index('views', 'idx_hashtag_views');
            $t->index('likes', 'idx_hashtag_likes'); // индекс по лайкам
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hashtags');
    }
};
