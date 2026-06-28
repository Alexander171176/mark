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
        Schema::create('market_brand_has_images', function (Blueprint $table) {

            $table->unsignedBigInteger('market_brand_id')
                ->comment('Бренд (market_brands.id)');

            $table->foreign(
                'market_brand_id',
                'market_brand_has_images_brand_id_fk'
            )
                ->references('id')
                ->on('market_brands')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('market_brand_image_id')
                ->comment('Изображение бренда (market_brand_images.id)');

            $table->foreign(
                'market_brand_image_id',
                'market_brand_has_images_image_id_fk'
            )
                ->references('id')
                ->on('market_brand_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_brand_has_images_order_idx')
                ->comment('Порядок отображения изображения у бренда');

            /**
             * Один pivot между брендом и изображением.
             */
            $table->primary(
                [
                    'market_brand_id',
                    'market_brand_image_id',
                ],
                'market_brand_has_images_pk'
            );

            $table->comment(
                'Маркетплейс: связь брендов с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_brand_has_images');
    }
};
