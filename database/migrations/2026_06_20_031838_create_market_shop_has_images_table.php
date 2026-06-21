<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_shop_has_images', function (Blueprint $table) {
            $table->unsignedBigInteger('market_shop_id')
                ->comment('Магазин (market_shops.id)');

            $table->foreign('market_shop_id', 'market_shop_has_images_shop_id_fk')
                ->references('id')
                ->on('market_shops')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('market_shop_image_id')
                ->comment('Изображение магазина (market_shop_images.id)');

            $table->foreign('market_shop_image_id', 'market_shop_has_images_image_id_fk')
                ->references('id')
                ->on('market_shop_images')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_shop_has_images_order_idx')
                ->comment('Порядок отображения изображения у магазина');

            $table->primary(
                ['market_shop_id', 'market_shop_image_id'],
                'market_shop_has_images_pk'
            );

            $table->comment('Маркетплейс: связь магазинов с изображениями.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_shop_has_images');
    }
};
