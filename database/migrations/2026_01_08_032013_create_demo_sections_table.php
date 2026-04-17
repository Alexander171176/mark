<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demo_sections', function (Blueprint $table) {
            $table->id();

            // Локаль — одна запись на язык
            $table->string('locale', 10)->unique()->index();

            // Заголовки секции
            $table->string('subtitle')->nullable(); // "Premium Designs"
            $table->string('title')->nullable();    // "Beautiful demo pages"

            // Поле для placeholder поиска
            $table->string('search_placeholder')->nullable(); // "Banking, Startup, Freelancer ..."

            // Управление отображением
            $table->integer('sort')->default(0);
            $table->boolean('is_dark')->default(false);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_sections');
    }
};
