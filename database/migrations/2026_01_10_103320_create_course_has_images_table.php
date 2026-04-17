<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Пивот-таблица связь "многие ко многим" между курсами и их изображениями
    public function up(): void
    {
        Schema::create('course_has_images', function (Blueprint $t) {

            // Связь с курсами
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // Связь с таблицей изображений курсов
            $t->foreignId('image_id')
                ->constrained('course_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у курса
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['course_id', 'image_id']);

            // Индекс для сортировки
            $t->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_has_images');
    }
};
