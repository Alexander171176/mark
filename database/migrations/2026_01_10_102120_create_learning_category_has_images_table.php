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
        Schema::create('learning_category_has_images', function (Blueprint $table) {
            $table->foreignId('learning_category_id')->constrained('learning_categories')->onDelete('cascade');
            $table->foreignId('image_id')->constrained('learning_category_images')->onDelete('cascade');
            $table->unsignedInteger('order')->default(0); // Добавляем поле order

            // Добавляем первичный ключ
            $table->primary(['learning_category_id', 'image_id']);
            // Индекс на order для сортировки внутри статьи (опционально, но может быть полезно)
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_category_has_images');
    }
};
