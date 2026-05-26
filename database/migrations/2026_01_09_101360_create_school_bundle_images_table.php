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
        Schema::create('school_bundle_images', function (Blueprint $t) {
            $t->id();

            $t->unsignedInteger('order')
                ->default(0)
                ->index('idx_school_bundle_image_order')
                ->comment('Порядок отображения');

            $t->string('alt', 255)->nullable()->comment('Alt текст');
            $t->string('caption', 255)->nullable()->comment('Подпись');

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bundle_images');
    }
};
