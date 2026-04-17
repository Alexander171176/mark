<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Пункты меню (вложенные, привязаны к navigation_menus)
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $t) {
            $t->id();

            // Какому меню принадлежит пункт
            $t->foreignId('menu_id')
                ->constrained('navigation_menus')
                ->cascadeOnDelete();

            // Вложенность: parent -> children (nullable = корневой пункт)
            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('navigation_items')
                ->nullOnDelete();

            $t->string('title');                       // Текст ссылки, видимый пользователю

            // Тип ссылки:
            //  - custom: произвольный URL (заполняем "url")
            //  - internal: абсолют/относит. URL в пределах сайта (заполняем "url")
            //  - route: по имени роута (заполняем "route_name" и "route_params")
            $t->string('type', 16)->default('custom'); // custom|internal|route

            $t->string('url')->nullable();             // URL для custom/internal
            $t->string('route_name')->nullable();      // Имя роута (для type=route)
            $t->json('route_params')->nullable();      // Параметры роута (JSON)

            $t->string('target', 16)->default('_self'); // _self|_blank
            $t->string('icon', 64)->nullable();         // Иконка (класс/ключ)
            $t->boolean('activity')->default(true);    // Вкл/выкл пункт
            $t->unsignedInteger('sort')->default(0);// Порядок внутри родителя

            $t->json('meta')->nullable();              // Доп. поля (атрибуты, стили)

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры/поиск
            $t->index(['menu_id', 'parent_id', 'sort'], 'idx_navitem_tree_order');
            $t->index(['menu_id', 'activity'], 'idx_navitem_menu_active');
            $t->index('type', 'idx_navitem_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
