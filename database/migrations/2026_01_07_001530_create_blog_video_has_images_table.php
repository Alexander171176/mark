<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_video_has_images', function (Blueprint $table) {
            $table->foreignId('video_id')
                ->comment('FK на blog_videos.id')
                ->constrained('blog_videos')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на blog_video_images.id')
                ->constrained('blog_video_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка превью внутри видео (draggable)');

            /**
             * Композитный первичный ключ
             */
            $table->primary(
                ['video_id', 'image_id'],
                'blog_video_has_images_primary'
            );

            /**
             * Основной индекс для draggable сортировки внутри видео
             */
            $table->index(
                ['video_id', 'order'],
                'blog_video_has_images_video_order_idx'
            );

            /**
             * Для обратных выборок / очистки
             */
            $table->index(
                'image_id',
                'blog_video_has_images_image_id_idx'
            );

            $table->comment('Связь видео блога с превью-изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_video_has_images');
    }
};
