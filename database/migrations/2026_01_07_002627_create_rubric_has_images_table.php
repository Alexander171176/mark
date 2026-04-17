<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubric_has_images', function (Blueprint $table) {
            $table->foreignId('rubric_id')
                ->comment('FK на rubrics.id')
                ->constrained('rubrics')
                ->cascadeOnDelete();

            $table->foreignId('image_id')
                ->comment('FK на rubric_images.id')
                ->constrained('rubric_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Сортировка изображения внутри рубрики (draggable)');

            $table->primary(['rubric_id', 'image_id']);

            // Основной индекс для сортировки внутри рубрики
            $table->index(['rubric_id', 'order'], 'rhi_rubric_order_idx');

            // Для обратных выборок / очистки
            $table->index('image_id', 'rhi_image_id_idx');

            $table->comment('Связь рубрик с изображениями (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubric_has_images');
    }
};
