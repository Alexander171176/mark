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
        Schema::create('learning_category_images', function (Blueprint $table) {
            $table->id(); // <— ОБЯЗАТЕЛЬНО
            $table->unsignedInteger('order')->default(0)->index(); // unsigned + index
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->timestamps();
            // softDeletes() — по желанию
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_category_images');
    }
};
