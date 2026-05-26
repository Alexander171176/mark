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
        Schema::create('school_track_images', function (Blueprint $t) {
            $t->id();

            // Порядок отображения изображения
            $t->unsignedInteger('order')
                ->default(0)
                ->index('idx_school_track_image_order')
                ->comment('Порядок сортировки изображения');

            // Данные изображения
            $t->string('alt')->nullable()->comment('Alt текст изображения');
            $t->string('caption')->nullable()->comment('Подпись изображения');

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_track_images');
    }
};
