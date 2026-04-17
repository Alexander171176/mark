<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wave_sections', function (Blueprint $table) {
            $table->id();

            // одна запись на локаль
            $table->char('locale', 10)->unique()->comment('Локаль секции (ru, en, ...)');

            // заголовки
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();

            // текстовые колонки
            $table->text('left_text')->nullable();
            $table->text('right_text')->nullable();

            // сортировка между секциями
            $table->unsignedInteger('sort')->default(0)->index();

            // флаги
            $table->boolean('is_dark')->default(false);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_sections');
    }
};
