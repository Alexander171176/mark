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
        Schema::create('school_course_has_images', function (Blueprint $t) {
            // Курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Изображение курса
            $t->foreignId('image_id')
                ->constrained('school_course_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            // Порядок отображения
            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения изображения у курса');

            $t->primary(['school_course_id', 'image_id'], 'pk_school_course_image');

            $t->index('order', 'idx_school_course_has_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_has_images');
    }
};
