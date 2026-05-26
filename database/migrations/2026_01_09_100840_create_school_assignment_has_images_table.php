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
        Schema::create('school_assignment_has_images', function (Blueprint $t) {
            $t->foreignId('school_assignment_id')
                ->constrained('school_assignments')
                ->cascadeOnDelete()
                ->comment('Задание');

            $t->foreignId('image_id')
                ->constrained('school_assignment_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(['school_assignment_id', 'image_id'], 'pk_school_assignment_image');

            $t->index(['school_assignment_id', 'order'], 'idx_school_assignment_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_assignment_has_images');
    }
};
