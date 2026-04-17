<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('developer_section_id')
                ->constrained('developer_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Контент карточки
            $table->string('title');                 // "Vue 3", "Typescript", ...
            $table->string('subtitle')->nullable();  // на будущее
            $table->text('description')->nullable(); // абзац под заголовком

            // Иконка: храним inline SVG текстом (как и в Feature)
            $table->text('image_light')->nullable();
            $table->text('image_dark')->nullable();
            $table->string('alt')->nullable();

            // Сортировка и активность
            $table->unsignedInteger('sort')->default(0)->index();
            $table->boolean('activity')->default(true)->index();

            // Уникальность внутри секции по title — удобно для upsert
            $table->unique(['developer_section_id', 'title']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_items');
    }
};
