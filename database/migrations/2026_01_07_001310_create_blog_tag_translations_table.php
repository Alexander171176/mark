<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tag_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Связь с тегом
             */
            $table->unsignedBigInteger('tag_id')
                ->comment('Тег (blog_tags.id)');

            $table->foreign('tag_id', 'blog_tag_translations_tag_id_fk')
                ->references('id')
                ->on('blog_tags')
                ->cascadeOnDelete();

            /**
             * Локаль
             */
            $table->string('locale', 10)
                ->index('blog_tag_translations_locale_idx')
                ->comment('Локаль (ru, en, kk...)');

            /**
             * Переводимые поля
             */
            $table->string('name', 255)
                ->comment('Название тега');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание');

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
             * Уникальность: одна локаль на тег
             */
            $table->unique(
                ['tag_id', 'locale'],
                'blog_tag_translations_tag_locale_unique'
            );

            /**
             * Индексы
             */
            $table->index(
                ['locale', 'name'],
                'blog_tag_translations_locale_name_idx'
            );

            $table->comment('Блог: переводы тегов по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tag_translations');
    }
};
