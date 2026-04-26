<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_banner_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Связь с баннером
             */
            $table->unsignedBigInteger('banner_id')
                ->comment('Баннер (blog_banners.id)');

            $table->foreign('banner_id', 'blog_banner_translations_banner_id_fk')
                ->references('id')
                ->on('blog_banners')
                ->cascadeOnDelete();

            /**
             * Локаль
             */
            $table->string('locale', 10)
                ->index('blog_banner_translations_locale_idx')
                ->comment('Локаль баннера (например: ru, kk, en)');

            /**
             * Переводимые поля
             */
            $table->string('title', 255)
                ->comment('Название/заголовок баннера');

            $table->text('link')
                ->nullable()
                ->comment('Ссылка (URL), куда ведёт баннер');

            $table->string('short', 255)
                ->nullable()
                ->comment('Короткий текст (анонс)');

            $table->timestamps();

            /**
             * Одна локаль = один перевод для одного баннера
             */
            $table->unique(
                ['banner_id', 'locale'],
                'blog_banner_translations_banner_locale_unique'
            );

            /**
             * Индекс для выборок по локали и заголовку
             */
            $table->index(
                ['locale', 'title'],
                'blog_banner_translations_locale_title_idx'
            );

            $table->comment('Блог: переводы баннеров по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_banner_translations');
    }
};
