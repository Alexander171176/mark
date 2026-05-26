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
        Schema::create('school_subscription_plan_has_images', function (Blueprint $t) {
            $t->unsignedBigInteger('school_subscription_plan_id')
                ->comment('Тарифный план');

            $t->unsignedBigInteger('image_id')
                ->comment('Изображение');

            $t->foreign('school_subscription_plan_id', 'fk_school_plan_img_plan')
                ->references('id')
                ->on('school_subscription_plans')
                ->cascadeOnDelete();

            $t->foreign('image_id', 'fk_school_plan_img_image')
                ->references('id')
                ->on('school_subscription_plan_images')
                ->cascadeOnDelete();

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(
                ['school_subscription_plan_id', 'image_id'],
                'pk_school_plan_image'
            );

            $t->index(
                ['school_subscription_plan_id', 'order'],
                'idx_school_plan_has_image_order'
            );

            $t->index('image_id', 'idx_school_plan_has_images_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscription_plan_has_images');
    }
};
