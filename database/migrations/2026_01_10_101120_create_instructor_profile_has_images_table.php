<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('instructor_profile_has_images', function (Blueprint $t) {
            // Связи
            $t->foreignId('instructor_profile_id')
                ->constrained('instructor_profiles')
                ->cascadeOnDelete();

            $t->foreignId('image_id')
                ->constrained('instructor_profile_images')
                ->cascadeOnDelete();

            // Порядок отображения
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['instructor_profile_id', 'image_id']);

            // Индекс для сортировки
            $t->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_profile_has_images');
    }
};
