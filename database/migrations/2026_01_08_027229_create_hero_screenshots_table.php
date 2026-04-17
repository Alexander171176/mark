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
        Schema::create('hero_screenshots', function (Blueprint $table) {
            $table->id();

            // FK на hero_sections
            $table->foreignId('hero_section_id')
                ->constrained('hero_sections')
                ->cascadeOnDelete()
                ->comment('FK на секцию Hero');

            $table->string('alt')->nullable()->comment('Alt-текст скриншота');

            $table->unsignedInteger('sort')->default(0)->comment('Порядок отображения');
            $table->boolean('activity')->default(true)->comment('Виден ли скриншот');

            $table->timestamps();

            // Частые выборки: для получения скриншотов секции, активных, отсортированных
            $table->index(['hero_section_id', 'activity', 'sort'], 'hero_shots_section_activity_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_screenshots');
    }
};
