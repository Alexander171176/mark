<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bundle_has_images', function (Blueprint $t) {
            // Связь с бандлами
            $t->foreignId('bundle_id')
                ->constrained('bundles')
                ->cascadeOnDelete();

            // Связь с таблицей изображений бандлов
            $t->foreignId('image_id')
                ->constrained('bundle_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у бандла
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['bundle_id', 'image_id']);

            // Индекс для сортировки
            $t->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_has_images');
    }
};
