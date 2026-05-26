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
        Schema::create('school_lesson_has_images', function (Blueprint $t) {

            $t->foreignId('school_lesson_id')
                ->constrained('school_lessons')
                ->cascadeOnDelete()
                ->comment('Урок');

            $t->foreignId('image_id')
                ->constrained('school_lesson_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(['school_lesson_id', 'image_id'], 'pk_school_lesson_image');

            $t->index(['school_lesson_id', 'order'], 'idx_school_lesson_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_lesson_has_images');
    }
};
