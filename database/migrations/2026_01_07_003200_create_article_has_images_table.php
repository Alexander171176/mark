<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_has_images', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на articles.id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на article_images.id')
                ->constrained('article_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка изображения внутри статьи (draggable)');

            $table->primary(['article_id', 'image_id']);

            // Основной индекс для draggable сортировки внутри статьи
            $table->index(['article_id', 'order'], 'ahi_article_order_idx');

            // Для обратных выборок/чисток
            $table->index('image_id', 'ahi_image_id_idx');

            $table->comment('Связь статей с изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_has_images');
    }
};
