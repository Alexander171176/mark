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
        Schema::create('school_module_has_images', function (Blueprint $t) {

            $t->foreignId('school_module_id')
                ->constrained('school_modules')
                ->cascadeOnDelete()
                ->comment('Модуль');

            $t->foreignId('image_id')
                ->constrained('school_module_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(['school_module_id', 'image_id'], 'pk_school_module_image');

            $t->index(['school_module_id', 'order'], 'idx_school_module_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_module_has_images');
    }
};
