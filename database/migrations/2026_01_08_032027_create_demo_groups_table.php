<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demo_groups', function (Blueprint $table) {
            $table->id();

            // Родительская секция (по локали)
            $table->foreignId('section_id')
                ->constrained('demo_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Слаг группы (landing, about, pricing, ...)
            $table->string('slug', 64);
            $table->unique(['section_id', 'slug']); // уникальность внутри секции

            // Заголовок и подпись под заголовком
            $table->string('title');                    // "Landing pages", "About pages", ...
            $table->string('description')->nullable();  // "Prebuilt page demos" и т.п.

            // Alt для иконки группы
            $table->string('icon_alt')->nullable();

            // Inline SVG для светлой/тёмной темы (храним как текст, без Spatie)
            $table->longText('icon_svg_light')->nullable();
            $table->longText('icon_svg_dark')->nullable();

            // Управление отображением
            $table->integer('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_groups');
    }
};
