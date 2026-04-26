<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_has_images', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на blog_articles.id')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на blog_article_images.id')
                ->constrained('blog_article_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка изображения внутри статьи (draggable)');

            $table->primary(['article_id', 'image_id'], 'blog_article_has_images_primary');

            /**
             * Основной индекс для сортировки внутри статьи
             */
            $table->index(
                ['article_id', 'order'],
                'blog_article_has_images_article_order_idx'
            );

            /**
             * Для обратных выборок / очистки
             */
            $table->index(
                'image_id',
                'blog_article_has_images_image_id_idx'
            );

            $table->comment('Связь статей блога с изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_has_images');
    }
};
