<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Баннеры/промо‑блоки CMS
    public function up(): void
    {
        Schema::create('cms_banners', function (Blueprint $t) {
            $t->id();

            $t->string('title');                     // Заголовок баннера
            $t->string('subtitle')->nullable();      // Подзаголовок/описание (кратко)

            // Где будет показан баннер (ключ позиции в интерфейсе)
            // Примеры: home_hero, home_promo, course_sidebar, blog_top и т.п.
            $t->string('placement', 64)->index();

            // Ссылка (варианты: прямой URL или имя роута + параметры)
            $t->string('link_type', 16)->default('url'); // url|route|none
            $t->string('link_url')->nullable();          // Прямой URL (для link_type=url)
            $t->string('link_route')->nullable();        // Имя роута (для link_type=route)
            $t->json('link_params')->nullable();         // Параметры роута (JSON)
            $t->string('link_target', 16)->default('_self'); // _self|_blank
            $t->string('button_label', 64)->nullable();  // Текст кнопки CTA

            // Временное окно публикации
            $t->timestamp('starts_at')->nullable();      // С какого момента показывать
            $t->timestamp('ends_at')->nullable();        // До какого момента показывать

            // Флаги/сортировка/видимость
            $t->boolean('activity')->default(true);     // Включён/выключен
            $t->unsignedInteger('sort')->default(0); // Порядок внутри одного placement

            // Визуальные настройки (можно хранить цвета, выравнивания и пр.)
            $t->json('meta')->nullable();               // Произвольные настройки

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры
            $t->index(['placement', 'activity', 'sort'], 'idx_banner_place_active_pos');
            $t->index(['starts_at', 'ends_at'], 'idx_banner_window');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_banners');
    }
};
