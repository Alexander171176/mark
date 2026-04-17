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
        Schema::create('hero_icons', function (Blueprint $table) {
            $table->id();

            // FK на hero_sections
            $table->foreignId('hero_section_id')
                ->constrained('hero_sections')
                ->cascadeOnDelete()
                ->comment('FK на секцию Hero');

            $table->string('label')->nullable()->comment('Название/лейбл иконки');
            $table->longText('svg')->nullable()->comment('Inline SVG, если сохраняем в БД');

            $table->unsignedInteger('sort')->default(0)->comment('Порядок отображения');
            $table->boolean('activity')->default(true)->comment('Видима ли иконка');

            $table->timestamps();

            // Частые выборки: для получения иконок секции, активных, отсортированных
            $table->index(['hero_section_id', 'activity', 'sort'], 'hero_icons_section_activity_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_icons');
    }
};
