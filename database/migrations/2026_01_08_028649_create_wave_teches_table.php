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
        Schema::create('wave_teches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wave_section_id')
                ->constrained('wave_sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // данные бейджа
            $table->string('title');                 // "Vue 3"
            $table->string('subtitle')->nullable();  // "Frontend framework"
            $table->text('description')->nullable(); // описание под сабтайтлом

            // изображение(я)
            $table->string('image_light', 2048)->nullable();
            $table->string('image_dark', 2048)->nullable();
            $table->string('alt')->nullable();

            // сортировка и активность
            $table->unsignedInteger('sort')->default(0)->index();
            $table->boolean('activity')->default(true)->index();

            // на всякий — уник по (section, title), чтобы upsert-ить удобнее
            $table->unique(['wave_section_id', 'title']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wave_teches');
    }
};
