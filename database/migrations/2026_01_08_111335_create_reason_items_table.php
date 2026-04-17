<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reason_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->constrained('reason_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Контент
            $table->string('title');          // "Focused on UI and CSS"
            $table->text('text')->nullable(); // Описание
            $table->string('align', 10)->default('left'); // 'left' | 'right'
            $table->string('image_alt')->nullable();
            $table->string('light_alt')->nullable();
            $table->string('dark_alt')->nullable();

            // Технические
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            $table->index(['section_id', 'activity', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reason_items');
    }
};
