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
        Schema::create('location_translations', function (Blueprint $table) {
            $table->id();

            // Локация
            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnDelete();

            // Локаль перевода
            $table->string('locale', 10);

            // Название локации
            $table->string('title');

            // Формы названия для формирования текстов и SEO
            // Например: Астана -> в Астане -> из Астаны
            $table->string('title_in')->nullable();
            $table->string('title_from')->nullable();

            // Контент
            $table->text('short')->nullable();
            $table->longText('description')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_desc')->nullable();

            $table->timestamps();

            $table->unique(['location_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_translations');
    }
};
