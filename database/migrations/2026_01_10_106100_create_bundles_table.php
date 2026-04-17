<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Набор (bundle) — объединение нескольких курсов под одним предложением
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $t) {
            $t->id();

            $t->unsignedInteger('sort')->default(0);     // Сортировка в списках
            $t->boolean('activity')->default(true);      // Публикация (виден в каталоге)

            $t->string('locale', 10)->default('ru');      // Локаль
            $t->string('title');                         // Заголовок набора
            $t->string('slug')->unique();                // ✅ глобально уникальный ЧПУ
            $t->string('subtitle')->nullable();          // Подзаголовок/оффер
            $t->text('short')->nullable();               // Краткое описание
            $t->text('description')->nullable();         // Описание
            $t->timestamp('published_at')->nullable();   // Дата публикации

            $t->unsignedBigInteger('views')->default(0); // Просмотры
            $t->unsignedBigInteger('likes')->default(0); // Лайки

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            $t->json('meta')->nullable();                // Метаданные (иконки/баннеры)
            $t->timestamps();
            $t->softDeletes();

            /**
             * Индексы для частых выборок (витрина/каталог)
             */

            // активные + публикация (фильтр + сортировка ленты)
            $t->index(['activity', 'published_at'], 'idx_bundle_active_pub');

            // локаль + активность + публикация (каталог/лента в рамках локали)
            $t->index(['locale', 'activity', 'published_at'], 'idx_bundle_loc_act_pub');

            // локаль + активность + сортировка (каталог/списки)
            $t->index(['locale', 'activity', 'sort'], 'idx_bundle_loc_act_sort');

            // если часто сортируешь/фильтруешь по sort независимо
            $t->index('sort');

            // метрики (если реально используешь в сортировках/фильтрах)
            $t->index('views');
            $t->index('likes');

            // ВАЖНО:
            // - НЕ делаем $t->index('slug'), т.к. ->unique() уже индекс
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
