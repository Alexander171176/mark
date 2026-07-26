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
        Schema::create('market_product_bundle_has_images', function (Blueprint $table) {

            /**
             * Комплект товаров.
             */
            $table->unsignedBigInteger('market_product_bundle_id')
                ->comment('Комплект товаров (market_product_bundles.id)');

            $table->foreign(
                'market_product_bundle_id',
                'market_product_bundle_has_images_bundle_id_fk'
            )
                ->references('id')
                ->on('market_product_bundles')
                ->cascadeOnDelete();

            /**
             * Изображение комплекта товаров.
             */
            $table->unsignedBigInteger('market_product_bundle_image_id')
                ->comment('Изображение комплекта (market_product_bundle_images.id)');

            $table->foreign(
                'market_product_bundle_image_id',
                'market_product_bundle_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_product_bundle_images')
                ->cascadeOnDelete();

            /**
             * Порядок отображения изображения
             * внутри конкретного комплекта.
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_bundle_has_images_order_idx')
                ->comment('Порядок отображения изображения у комплекта');

            /**
             * Одна связь между комплектом
             * и изображением может существовать только один раз.
             */
            $table->primary(
                [
                    'market_product_bundle_id',
                    'market_product_bundle_image_id',
                ],
                'market_product_bundle_has_images_pk'
            );

            /**
             * Быстрая загрузка изображений комплекта
             * в установленном порядке.
             */
            $table->index(
                [
                    'market_product_bundle_id',
                    'order',
                ],
                'market_product_bundle_has_images_bundle_order_idx'
            );

            $table->comment(
                'Маркетплейс: связь комплектов товаров с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_bundle_has_images');
    }
};
