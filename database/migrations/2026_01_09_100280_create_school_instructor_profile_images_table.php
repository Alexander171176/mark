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
        Schema::create('school_instructor_profile_images', function (Blueprint $t) {
            $t->id();

            // Порядок отображения
            $t->unsignedInteger('order')->default(0)->index()->comment('Порядок сортировки изображения');

            // Подписи (можно оставить без переводов, как в старой версии)
            $t->string('alt', 255)->nullable()->comment('Alt текст');
            $t->string('caption', 255)->nullable()->comment('Подпись к изображению');

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_instructor_profile_images');
    }
};
