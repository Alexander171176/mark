<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Связь "многие ко многим" между курсами и категориями
    public function up(): void
    {
        Schema::create('course_has_learning_category', function (Blueprint $t) {
            $t->id();

            // FK на курсы
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // FK на категории
            $t->foreignId('learning_category_id')
                ->constrained('learning_categories')
                ->cascadeOnDelete();

            $t->timestamps();

            // Один и тот же курс не должен повторяться в одной и той же категории
            $t->unique(['course_id', 'learning_category_id'], 'uq_course_category');

            // Ускоряем выборки по любой стороне связи
            $t->index('learning_category_id');
            $t->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_has_learning_category');
    }
};
