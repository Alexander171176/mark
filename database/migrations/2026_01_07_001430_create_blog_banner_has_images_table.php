<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_banner_has_images', function (Blueprint $table) {
            $table->foreignId('banner_id')
                ->comment('FK на blog_banners.id')
                ->constrained('blog_banners')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на blog_banner_images.id')
                ->constrained('blog_banner_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка изображения внутри баннера (draggable)');

            /**
             * Композитный первичный ключ
             */
            $table->primary(
                ['banner_id', 'image_id'],
                'blog_banner_has_images_primary'
            );

            /**
             * Основной индекс для sortable/draggable внутри баннера
             */
            $table->index(
                ['banner_id', 'order'],
                'blog_banner_has_images_banner_order_idx'
            );

            /**
             * Для обратных выборок / очистки
             */
            $table->index(
                'image_id',
                'blog_banner_has_images_image_id_idx'
            );

            $table->comment('Связь баннеров блога с изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_banner_has_images');
    }
};
