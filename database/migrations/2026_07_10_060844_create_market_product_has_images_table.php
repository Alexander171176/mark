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
        Schema::create('market_product_has_images', function (Blueprint $table) {
            $table->unsignedBigInteger('market_product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_has_images_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('market_product_image_id')
                ->comment('Изображение товара (market_product_images.id)');

            $table->foreign(
                'market_product_image_id',
                'market_product_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_product_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_has_images_order_idx')
                ->comment('Порядок отображения изображения у товара');

            /**
             * Один pivot между товаром и изображением.
             */
            $table->primary(
                [
                    'market_product_id',
                    'market_product_image_id',
                ],
                'market_product_has_images_pk'
            );

            $table->comment(
                'Маркетплейс: связь товаров с изображениями.'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_has_images');
    }
};
