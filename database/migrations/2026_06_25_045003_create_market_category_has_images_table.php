<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('market_category_has_images', function (Blueprint $table) {

            $table->unsignedBigInteger('market_category_id')
                ->comment('Категория (market_categories.id)');

            $table->foreign(
                'market_category_id',
                'market_category_has_images_category_id_fk'
            )
                ->references('id')
                ->on('market_categories')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('market_category_image_id')
                ->comment('Изображение категории (market_category_images.id)');

            $table->foreign(
                'market_category_image_id',
                'market_category_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_category_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_category_has_images_order_idx')
                ->comment('Порядок отображения изображения у категории');

            /**
             * Один pivot между категорией и изображением.
             */
            $table->primary(
                [
                    'market_category_id',
                    'market_category_image_id',
                ],
                'market_category_has_images_pk'
            );

            $table->comment(
                'Маркетплейс: связь категорий товаров с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_category_has_images');
    }
};
