<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_has_images', function (Blueprint $table) {
            $table->foreignId('video_id')
                ->comment('FK на videos.id')
                ->constrained('videos')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на video_images.id')
                ->constrained('video_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка превью внутри видео (draggable)');

            $table->primary(['video_id', 'image_id']);

            // Основной индекс для draggable сортировки внутри видео
            $table->index(['video_id', 'order'], 'vhi_video_order_idx');

            // Для обратных выборок / очистки
            $table->index('image_id', 'vhi_image_id_idx');

            $table->comment('Связь видео с превью-изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_has_images');
    }
};
