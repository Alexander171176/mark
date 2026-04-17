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
        Schema::create('assignment_has_images', function (Blueprint $t) {
            // Связь с заданиями
            $t->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            // Связь с таблицей изображений заданий
            $t->foreignId('image_id')
                ->constrained('assignment_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у заданий
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['assignment_id', 'image_id']);

            // Индекс для сортировки изображений внутри заданий
            $t->index(['assignment_id', 'order'], 'idx_assignment_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_has_images');
    }
};
