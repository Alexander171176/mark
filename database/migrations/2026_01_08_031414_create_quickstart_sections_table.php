<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quickstart_sections', function (Blueprint $table) {
            $table->id();

            // Локаль (одна запись на локаль)
            $table->string('locale', 10)->index();
            $table->unique('locale');

            // Заголовки секции
            $table->string('title')->nullable();       // "Get Started with Vulk"
            $table->string('subtitle')->nullable();    // "Quickstart Tutorial"

            // CTA #1 (например, Discord)
            $table->string('primary_label')->nullable();                 // "Discord Access"
            $table->string('primary_url', 2048)->nullable();             // https://go.cssninja.io/discord
            $table->text('primary_icon')->nullable();                    // inline SVG иконки (опционально)

            // CTA #2 (например, GitHub) + поповер
            $table->string('secondary_label')->nullable();               // "GitHub Access"
            $table->string('secondary_url', 2048)->nullable();
            $table->text('secondary_icon')->nullable();                  // inline SVG иконки (опционально)
            $table->boolean('secondary_popover_enabled')->default(true);
            $table->string('secondary_popover_title')->nullable();       // "GitHub access"
            $table->text('secondary_popover_text')->nullable();          // текст поповера

            // Видео (метаданные; сам файл и постер — через Spatie)
            $table->string('video_alt')->nullable();                     // alt/описание для постера/видео
            $table->string('video_caption')->nullable();                 // подпись под видео (если понадобится)
            $table->json('video_options')->nullable();                   // { "autoplay": false, "loop": false, ... } — опционально

            // Служебные поля
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_dark')->default(false);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quickstart_sections');
    }
};
