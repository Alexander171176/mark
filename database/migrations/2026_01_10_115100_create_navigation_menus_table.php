<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Меню навигации (контейнер для пунктов меню)
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $t) {
            $t->id();

            $t->string('name');                 // Человекочитаемое имя меню (напр. "Главное меню")
            $t->string('slug')->unique();       // Технический ключ/slug (напр. "header", "footer")
            $t->string('location', 64)          // Зона размещения (для темы/фронта)
            ->default('header')
                ->comment('header|footer|sidebar|custom');
            $t->boolean('activity')->default(true); // Включено/отключено
            $t->unsignedInteger('sort')->default(0); // Порядок среди меню одной зоны

            $t->json('meta')->nullable();       // Произвольные настройки (иконки, стили и т.п.)

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры
            $t->index(['location', 'activity', 'sort'], 'idx_navmenu_loc_active_pos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
    }
};
