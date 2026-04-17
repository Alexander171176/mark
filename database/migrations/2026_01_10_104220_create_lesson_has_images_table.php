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
        Schema::create('lesson_has_images', function (Blueprint $t) {
            // Связь с уроками
            $t->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();

            // Связь с таблицей изображений уроков
            $t->foreignId('image_id')
                ->constrained('lesson_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у урока
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['lesson_id', 'image_id']);

            // Индекс для сортировки изображений внутри урока
            $t->index(['lesson_id', 'order'], 'idx_lesson_image_order');

            // ВАЖНО:
            // - Отдельные индексы на lesson_id и image_id не нужны, их даёт foreignId()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_has_images');
    }
};
