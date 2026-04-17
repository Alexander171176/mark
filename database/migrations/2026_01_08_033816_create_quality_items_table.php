<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_items', function (Blueprint $table) {
            $table->id();

            // Привязка к секции (по локали)
            $table->foreignId('section_id')
                ->constrained('quality_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Тексты элемента
            $table->string('top_title');         // "Modern Framework"
            $table->string('title');             // "Vue 3"
            $table->text('description')->nullable();

            // Иконка (inline SVG как текст) + alt
            $table->string('icon_alt')->nullable();
            $table->longText('icon_svg_light')->nullable(); // можно хранить один svg, но оставим 2 для гибкости темы
            $table->longText('icon_svg_dark')->nullable();

            // Анимация
            // 'bottom' | 'top' | 'left' | 'right'
            $table->string('reveal_from', 8)->default('bottom');
            $table->unsignedSmallInteger('delay')->default(0);         // мс
            $table->decimal('threshold', 4, 2)->nullable();            // по умолчанию 0.15 в компоненте
            $table->unsignedSmallInteger('distance')->nullable();      // px, по умолчанию 28

            // Управление отображением
            $table->integer('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            // Индексы
            $table->index(['section_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_items');
    }
};
