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
        Schema::create('school_track_has_images', function (Blueprint $t) {
            // Связь с категорией обучения
            $t->foreignId('school_track_id')
                ->constrained('school_tracks')
                ->cascadeOnDelete()
                ->comment('Категория обучения');

            // Связь с изображением
            $t->foreignId('image_id')
                ->constrained('school_track_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            // Порядок отображения
            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            // Составной первичный ключ
            $t->primary(['school_track_id', 'image_id'], 'pk_school_track_image');

            // Индекс для сортировки
            $t->index('order', 'idx_school_track_has_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_track_has_images');
    }
};
