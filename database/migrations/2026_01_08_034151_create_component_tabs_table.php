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
        Schema::create('component_tabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('component_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Ключ для кода (base/intermediate/blocks/advanced/utilities)
            $table->string('slug', 64);
            // Лейбл для отображения (Base / Intermediate / …)
            $table->string('label');

            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            $table->unique(['section_id', 'slug']); // ключ уникален в пределах секции
            $table->index(['section_id', 'activity', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_tabs');
    }
};
