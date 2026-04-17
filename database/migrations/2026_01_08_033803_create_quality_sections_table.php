<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_sections', function (Blueprint $table) {
            $table->id();

            // Локаль — одна запись на язык
            $table->string('locale', 10)->unique()->index();

            // Заголовки секции
            $table->string('subtitle')->nullable(); // "Carefully Handcrafted"
            $table->string('title')->nullable();    // "Vulk is a Top-Tier product"

            // Alt для длинного скриншота (общий alt для light/dark)
            $table->string('screenshot_alt')->nullable(); // "Long screenshot"

            // Управление отображением
            $table->integer('sort')->default(0);
            $table->boolean('is_dark')->default(false);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_sections');
    }
};
