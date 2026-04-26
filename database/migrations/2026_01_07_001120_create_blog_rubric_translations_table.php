<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_rubric_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Связь с основной рубрикой
             */
            $table->unsignedBigInteger('rubric_id')
                ->comment('Рубрика (blog_rubrics.id)');

            $table->foreign('rubric_id', 'blog_rubric_translations_rubric_id_fk')
                ->references('id')
                ->on('blog_rubrics')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('blog_rubric_translations_locale_idx')
                ->comment('Локаль перевода (например: ru, kk, en)');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название рубрики');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание (анонс)');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание рубрики');

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
             * Одна локаль = один перевод для одной рубрики
             */
            $table->unique(['rubric_id', 'locale'], 'blog_rubric_translations_rubric_locale_unique');

            /**
             * Индекс для выборок по локали и названию
             */
            $table->index(['locale', 'title'], 'blog_rubric_translations_locale_title_idx');

            $table->comment('Блог сообщества: переводы рубрик по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_rubric_translations');
    }
};
