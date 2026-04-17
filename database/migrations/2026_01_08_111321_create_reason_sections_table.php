<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reason_sections', function (Blueprint $table) {
            $table->id();

            // Локаль секции (одна запись на локаль)
            $table->string('locale', 10)->index();

            // Верхняя подпись и основной заголовок
            $table->string('subtitle')->nullable(); // "You'll love this product"
            $table->string('title');                // "3 Reasons to choose Vulk"

            // CTA-блок внизу
            $table->string('cta_title')->nullable();      // "Exclusively on Envato Market"
            $table->string('cta_btn_text')->nullable();   // "Get It Now"
            $table->string('cta_btn_url')->nullable();    // "https://..."
            $table->string('cta_btn_target', 16)->default('_self');

            // Технические поля
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            // По одной записи на локаль
            $table->unique(['locale']);
            $table->index(['activity', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reason_sections');
    }
};
