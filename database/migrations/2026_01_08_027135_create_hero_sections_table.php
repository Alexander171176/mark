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
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();

            // Локаль секции (пример: 'ru', 'en')
            $table->string('locale', 10)->default('ru')->comment('Локаль секции');

            // Заголовок (Hello, I am Vulk.)
            $table->string('title')->comment('Главный заголовок секции');

            // Подзаголовок (Vue 3 Landing Page UI - v2.1.2)
            $table->string('subtitle')->nullable()->comment('Подзаголовок');

            // Бейдж справа от подзаголовка (SSG/SSR Ready)
            $table->string('badge_text')->nullable()->comment('Текст бейджа в подзаголовке');

            // Описание под заголовком
            $table->text('description')->nullable()->comment('Описание секции');

            // Кнопка 1 (текст + ссылка + target)
            $table->string('primary_btn_text')->nullable()->comment('Текст основной кнопки');
            $table->string('primary_btn_url')->nullable()->comment('Ссылка основной кнопки');
            $table->string('primary_btn_target', 16)->default('_self')->comment('target основной кнопки');

            // Кнопка 2 (текст + ссылка + target)
            $table->string('secondary_btn_text')->nullable()->comment('Текст второй кнопки');
            $table->string('secondary_btn_url')->nullable()->comment('Ссылка второй кнопки');
            $table->string('secondary_btn_target', 16)->default('_self')->comment('target второй кнопки');

            // Флаги по соглашению проекта
            $table->boolean('is_dark')->default(false)->comment('Вариант под тёмную тему');
            $table->boolean('activity')->default(true)->comment('Активна ли секция');

            $table->timestamps();

            // Частые выборки
            $table->index(['locale', 'activity'], 'hp_hero_sections_locale_activity_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
