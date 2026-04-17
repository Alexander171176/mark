<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_brand_has_products', function (Blueprint $table) {

            /**
             * TENANT (строго по витрине)
             */
            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /**
             * ENTITIES
             */
            $table->unsignedBigInteger('brand_id')
                ->comment('Бренд (market_brands.id)');

            $table->unsignedBigInteger('product_id')
                ->comment('Товар (market_products.id)');

            $table->boolean('activity')->default(true)->comment('Активность связи');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка товара внутри бренда');

            $table->timestamps();

            /**
             * PRIMARY KEY
             * Уникальность связи в рамках витрины
             */
            $table->primary(['storefront_id', 'brand_id', 'product_id'], 'pk_mbhp_storefront_brand_product');

            /**
             * ✅ FK (единый подход): только CASCADE, без SET NULL
             * Требование: в родительских таблицах есть UNIQUE(storefront_id, id)
             * - market_brands:   uq_market_brands_storefront_id (storefront_id, id)
             * - market_products: uq_market_products_storefront_id (storefront_id, id)
             */
            $table->foreign(['storefront_id', 'brand_id'], 'fk_mbhp_storefront_brand')
                ->references(['storefront_id', 'id'])
                ->on('market_brands')
                ->cascadeOnDelete();

            $table->foreign(['storefront_id', 'product_id'], 'fk_mbhp_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * INDEXES
             */
            $table->index(['storefront_id', 'brand_id', 'activity', 'sort'], 'ix_mbhp_brand_list');
            $table->index(['storefront_id', 'product_id'], 'ix_mbhp_product');

            $table->comment('Маркет: связь брендов и товаров (M:N), tenant-safe по витрине');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_brand_has_products');
    }
};
