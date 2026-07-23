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
        Schema::create('market_product_variant_has_images', function (Blueprint $table) {

            /**
             * Вариант товара.
             */
            $table->unsignedBigInteger('market_product_variant_id')
                ->comment('Вариант товара (market_product_variants.id)');

            $table->foreign(
                'market_product_variant_id',
                'market_product_variant_has_images_variant_id_fk'
            )
                ->references('id')
                ->on('market_product_variants')
                ->cascadeOnDelete();

            /**
             * Изображение варианта.
             */
            $table->unsignedBigInteger('market_product_variant_image_id')
                ->comment('Изображение варианта (market_product_variant_images.id)');

            $table->foreign(
                'market_product_variant_image_id',
                'market_product_variant_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_product_variant_images')
                ->cascadeOnDelete();

            /**
             * Порядок отображения изображения
             * внутри конкретного варианта.
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_variant_has_images_order_idx')
                ->comment('Порядок отображения изображения у варианта');

            /**
             * Один pivot между вариантом
             * и изображением.
             */
            $table->primary(
                [
                    'market_product_variant_id',
                    'market_product_variant_image_id',
                ],
                'market_product_variant_has_images_pk'
            );

            /**
             * Быстрая загрузка изображений
             * варианта в нужном порядке.
             */
            $table->index(
                [
                    'market_product_variant_id',
                    'order',
                ],
                'market_product_variant_has_images_variant_order_idx'
            );

            $table->comment(
                'Маркетплейс: связь вариантов товаров с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_variant_has_images');
    }
};
