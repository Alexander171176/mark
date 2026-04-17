<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_schedule_has_images', function (Blueprint $t) {
            // Связь с расписанием / потоком курса
            $t->foreignId('course_schedule_id')
                ->constrained('course_schedules')
                ->cascadeOnDelete();

            // Связь с таблицей изображений расписаний
            $t->foreignId('image_id')
                ->constrained('course_schedule_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у конкретного расписания
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['course_schedule_id', 'image_id']);

            // Индекс для сортировки изображений внутри расписания
            $t->index(
                ['course_schedule_id', 'order'],
                'idx_course_schedule_image_order'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_schedule_has_images');
    }
};
