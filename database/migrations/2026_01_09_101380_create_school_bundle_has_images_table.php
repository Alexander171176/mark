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
        Schema::create('school_bundle_has_images', function (Blueprint $t) {
            $t->foreignId('school_bundle_id')
                ->constrained('school_bundles')
                ->cascadeOnDelete()
                ->comment('Набор курсов');

            $t->foreignId('image_id')
                ->constrained('school_bundle_images')
                ->cascadeOnDelete()
                ->comment('Изображение');

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(['school_bundle_id', 'image_id'], 'pk_school_bundle_image');

            $t->index('order', 'idx_school_bundle_has_image_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bundle_has_images');
    }
};
