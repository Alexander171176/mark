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
        Schema::create('bundle_images', function (Blueprint $t) {

            $t->id();

            // Основные поля
            $t->unsignedInteger('order')->default(0)->index(); // Порядок отображения
            $t->string('alt', 255)->nullable();                // Alt-текст
            $t->string('caption', 255)->nullable();            // Подпись к изображению

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_images');
    }
};
