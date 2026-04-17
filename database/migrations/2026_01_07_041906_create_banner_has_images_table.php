<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner_has_images', function (Blueprint $table) {
            $table->foreignId('banner_id')
                ->comment('FK на banners.id')
                ->constrained('banners')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на banner_images.id')
                ->constrained('banner_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка изображения внутри баннера (draggable)');

            $table->primary(['banner_id', 'image_id']);

            // Основной индекс для draggable сортировки внутри баннера
            $table->index(['banner_id', 'order'], 'bhi_banner_order_idx');

            // Для обратных выборок / очистки
            $table->index('image_id', 'bhi_image_id_idx');

            $table->comment('Связь баннеров с изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_has_images');
    }
};
