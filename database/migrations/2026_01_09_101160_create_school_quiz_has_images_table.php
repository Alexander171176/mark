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
        Schema::create('school_quiz_has_images', function (Blueprint $t) {
            $t->foreignId('school_quiz_id')
                ->constrained('school_quizzes')
                ->cascadeOnDelete()
                ->comment('Квиз');

            $t->foreignId('image_id')
                ->constrained('school_quiz_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(['school_quiz_id', 'image_id'], 'pk_school_quiz_image');

            $t->index(['school_quiz_id', 'order'], 'idx_school_quiz_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_has_images');
    }
};
