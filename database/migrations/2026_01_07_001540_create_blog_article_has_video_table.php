<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_has_video', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на blog_articles.id')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->foreignId('video_id')
                ->comment('FK на blog_videos.id')
                ->constrained('blog_videos')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка видео внутри статьи (draggable)');

            /**
             * Композитный первичный ключ
             */
            $table->primary(
                ['article_id', 'video_id'],
                'blog_article_has_video_primary'
            );

            /**
             * Основной индекс для выдачи видео по статье
             */
            $table->index(
                ['article_id', 'sort'],
                'blog_article_has_video_article_sort_idx'
            );

            /**
             * Для обратных выборок
             */
            $table->index(
                'video_id',
                'blog_article_has_video_video_id_idx'
            );

            $table->comment('Связь статей блога с видео (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_has_video');
    }
};
