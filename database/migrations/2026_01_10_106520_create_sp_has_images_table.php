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
        Schema::create('subscription_plan_has_images', function (Blueprint $t) {
            $t->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            $t->foreignId('image_id')
                ->constrained('subscription_plan_images')
                ->cascadeOnDelete();

            $t->unsignedInteger('order')->default(0);

            $t->primary(['subscription_plan_id', 'image_id']);

            // сортировка картинок внутри тарифа
            $t->index(['subscription_plan_id', 'order'], 'idx_subscription_plan_image_order');

            // быстрый поиск "где используется это изображение"
            $t->index(['image_id'], 'idx_subscription_plan_has_images_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_has_images');
    }
};
