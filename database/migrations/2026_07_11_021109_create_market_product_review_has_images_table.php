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
        Schema::create('market_product_review_has_images', function (Blueprint $table) {
            $table->unsignedBigInteger('market_product_review_id')
                ->comment('Отзыв товара (market_product_reviews.id)');

            $table->foreign(
                'market_product_review_id',
                'market_product_review_has_images_review_id_fk'
            )
                ->references('id')
                ->on('market_product_reviews')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('market_product_review_image_id')
                ->comment('Изображение отзыва товара (market_product_review_images.id)');

            $table->foreign(
                'market_product_review_image_id',
                'market_product_review_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_product_review_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_review_has_images_order_idx')
                ->comment('Порядок отображения изображения в отзыве');

            /**
             * Один pivot между отзывом и изображением.
             */
            $table->primary(
                [
                    'market_product_review_id',
                    'market_product_review_image_id',
                ],
                'market_product_review_has_images_pk'
            );

            $table->comment(
                'Маркетплейс: связь отзывов товаров с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_review_has_images');
    }
};
