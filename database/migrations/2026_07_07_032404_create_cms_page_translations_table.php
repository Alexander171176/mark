<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('cms_page_translations', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Связь со страницей
             */
            $table->unsignedBigInteger('cms_page_id')
                ->comment('CMS страница (cms_pages.id)');

            $table->foreign(
                'cms_page_id',
                'cms_page_translations_page_id_fk'
            )
                ->references('id')
                ->on('cms_pages')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('cms_page_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название страницы');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок страницы');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание страницы');

            $table->longText('description')
                ->nullable()
                ->comment('HTML содержимое страницы');

            /**
             * SEO
             */
            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO: meta title');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO: meta keywords');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO: meta description');

            $table->timestamps();

            /**
             * Одна страница = один перевод на одну локаль
             */
            $table->unique(
                ['cms_page_id', 'locale'],
                'cms_page_translations_page_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'cms_page_translations_locale_title_idx'
            );

            $table->comment(
                'CMS: переводы страниц сайта по локалям с HTML-контентом и SEO.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_page_translations');
    }
};
