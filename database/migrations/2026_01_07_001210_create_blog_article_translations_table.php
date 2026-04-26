<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Связь с основной статьёй
             */
            $table->unsignedBigInteger('article_id')
                ->comment('Статья (blog_articles.id)');

            $table->foreign('article_id', 'blog_article_translations_article_id_fk')
                ->references('id')
                ->on('blog_articles')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('blog_article_translations_locale_idx')
                ->comment('Локаль перевода (например: ru, kk, en)');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Заголовок статьи');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание (анонс)');

            $table->text('description')
                ->nullable()
                ->comment('Текст/контент статьи');

            $table->string('pseudonym', 255)
                ->nullable()
                ->comment('Псевдоним автора (если не хотим показывать имя пользователя)');

            /**
             * SEO-поля
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
             * Одна локаль = один перевод для одной статьи
             */
            $table->unique(
                ['article_id', 'locale'],
                'blog_article_translations_article_locale_unique'
            );

            /**
             * Индекс для выборок по локали и заголовку
             */
            $table->index(
                ['locale', 'title'],
                'blog_article_translations_locale_title_idx'
            );

            $table->comment('Блог сообщества: переводы статей по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_translations');
    }
};
