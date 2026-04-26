<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_video_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Связь с видео
             */
            $table->unsignedBigInteger('video_id');

            $table->foreign('video_id', 'blog_video_translations_video_id_fk')
                ->references('id')
                ->on('blog_videos')
                ->cascadeOnDelete();

            /**
             * Локаль
             */
            $table->string('locale', 10)
                ->index('blog_video_translations_locale_idx');

            /**
             * Переводы
             */
            $table->string('title', 255);

            $table->string('short', 255)->nullable();

            $table->text('description')->nullable();

            $table->string('pseudonym', 255)->nullable();

            /**
             * SEO
             */
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->text('meta_desc')->nullable();

            $table->timestamps();

            /**
             * Уникальность
             */
            $table->unique(
                ['video_id', 'locale'],
                'blog_video_translations_video_locale_unique'
            );

            /**
             * Индексы
             */
            $table->index(
                ['locale', 'title'],
                'blog_video_translations_locale_title_idx'
            );

            $table->comment('Блог: переводы видео по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_video_translations');
    }
};
