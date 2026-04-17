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
        Schema::create('component_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('component_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('box_class')->nullable()->default('is-primary'); // модификатор цвета
            $table->string('title');
            $table->text('text')->nullable();

            // SVG-иконки как текст (сразу дадим поддержку светлой/тёмной тем)
            $table->longText('icon_svg_light')->nullable();
            $table->longText('icon_svg_dark')->nullable();
            $table->string('icon_alt')->nullable();

            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            $table->index(['section_id', 'activity', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_features');
    }
};
