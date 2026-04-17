<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('instructor_profile_images', function (Blueprint $t) {
            $t->id();

            // Основные поля
            $t->unsignedInteger('order')->default(0)->index(); // и больше нигде индекс не добавляем
            $t->string('alt', 255)->nullable();
            $t->string('caption', 255)->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_profile_images');
    }
};
