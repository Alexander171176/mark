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
        Schema::create('component_sections', function (Blueprint $table) {
            $table->id();
            // Локаль секции (как в остальных секциях)
            $table->string('locale', 10)->default('ru');
            $table->string('subtitle')->nullable();
            $table->string('title')->nullable();

            // Кнопка под заголовком
            $table->string('cta_text')->nullable();   // "All Components"
            $table->string('cta_url', 1024)->nullable()->default('/blocks');

            // Управление отображением
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            $table->unique('locale');                // одна секция на локаль
            $table->index(['activity', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_sections');
    }
};
