<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('feature_section_id')
                ->constrained('feature_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Контент карточки
            $table->string('title');                 // "600+ Components"
            $table->string('subtitle')->nullable();  // (в шаблоне не используется, но оставим для гибкости)
            $table->text('description')->nullable(); // "Ready to go building blocks..."

            // Иконка
            $table->text('image_light')->nullable();
            $table->text('image_dark')->nullable();
            $table->string('alt')->nullable();

            // Сортировка и активность
            $table->unsignedInteger('sort')->default(0)->index();
            $table->boolean('activity')->default(true)->index();

            // Уникальность внутри секции по title — удобно для upsert
            $table->unique(['feature_section_id', 'title']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_items');
    }
};
