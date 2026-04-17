<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_order_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции заказа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_moi_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * ORDER LINK (tenant-safe)
             * Требование: в market_orders есть uq_market_orders_tenant_id (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')->comment('Заказ (market_orders.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_moi_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            /* =========================================================
             * PRODUCT / VARIANT (optional link + tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), опционально (снапшот сохраняется)');

            $table->foreign(['storefront_id', 'product_id'], 'fk_moi_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->restrictOnDelete();

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id), опционально');

            $table->foreign(['storefront_id', 'product_variant_id'], 'fk_moi_storefront_variant')
                ->references(['storefront_id', 'id'])
                ->on('market_product_variants')
                ->restrictOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность позиции');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри заказа');

            $table->string('type', 32)->default('product')
                ->comment('Тип: product/service/shipping/discount/gift/fee');

            /**
             * ✅ Практично: ключ уникальности строки в заказе.
             * Пример: "p:123" или "v:456" или "custom:shipping"
             * Это решает проблему NULL-variant и не ломает multi-variant одного товара.
             */
            $table->string('item_key', 191)->comment('Ключ позиции для дедупликации в заказе (p:id / v:id / custom:...)');

            /* =========================================================
             * SNAPSHOT
             * ========================================================= */

            $table->string('sku', 191)->nullable()->comment('SKU (снапшот)');
            $table->string('title', 255)->comment('Название позиции (снапшот)');
            $table->string('variant_title', 255)->nullable()->comment('Название варианта (снапшот)');

            $table->string('unit', 30)->nullable()->comment('Ед. измерения (снапшот)');
            $table->unsignedInteger('weight')->default(0)->comment('Вес (граммы, снапшот)');
            $table->string('barcode', 64)->nullable()->comment('Штрихкод (снапшот)');

            $table->json('options')->nullable()->comment('Опции/характеристики (снапшот)');
            $table->string('image_url', 255)->nullable()->comment('Картинка (снапшот)');

            /* =========================================================
             * QUANTITY / PRICES
             * ========================================================= */

            $table->decimal('qty', 18, 6)->default(1)->comment('Количество (дробное поддерживается)');

            $table->foreignId('currency_id')
                ->comment('Валюта позиции (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->decimal('unit_price', 18, 2)->default(0)->comment('Цена за единицу (до скидок)');

            $table->string('discount_type', 20)->nullable()->comment('percent/fixed/manual/promo/bonus');
            $table->decimal('discount_rate', 8, 4)->nullable()->comment('Процент скидки');
            $table->decimal('discount_amount', 18, 2)->default(0)->comment('Сумма скидки по строке');

            $table->decimal('tax_rate', 8, 4)->nullable()->comment('Ставка налога/НДС');
            $table->decimal('tax_amount', 18, 2)->default(0)->comment('Сумма налога по строке');

            $table->decimal('subtotal', 18, 2)->default(0)->comment('qty * unit_price');
            $table->decimal('total', 18, 2)->default(0)->comment('subtotal - discount + tax');

            /* =========================================================
             * FULFILLMENT (tenant-safe where possible)
             * ========================================================= */

            $table->unsignedBigInteger('warehouse_id')
                ->nullable()
                ->comment('Склад отгрузки (market_warehouses.id)');

            $table->unsignedBigInteger('pickup_point_id')
                ->nullable()
                ->comment('ПВЗ (market_pickup_points.id)');

            $table->foreign(['company_id', 'storefront_id', 'warehouse_id'], 'fk_moi_warehouse_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_warehouses')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'pickup_point_id'], 'fk_moi_pickup_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_pickup_points')
                ->restrictOnDelete();

            /* =========================================================
             * META / TECH
             * ========================================================= */

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->json('meta')->nullable()->comment('Доп. данные (промо/бонусы/provider ids/и т.п.)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ Единственная правильная дедупликация строк в заказе
            $table->unique(['order_id', 'item_key'], 'uq_moi_order_item_key');
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_order_items_tenant_id');
            $table->index(['company_id', 'storefront_id'], 'ix_moi_tenant');
            $table->index(['storefront_id', 'order_id', 'activity', 'sort'], 'ix_moi_order_list');

            $table->index(['product_id'], 'ix_moi_product');
            $table->index(['product_variant_id'], 'ix_moi_variant');

            $table->index(['warehouse_id'], 'ix_moi_warehouse');
            $table->index(['pickup_point_id'], 'ix_moi_pickup_point');

            $table->index(['type'], 'ix_moi_type');

            $table->comment('Маркет: позиции заказа (снапшот товаров/цен), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_items');
    }
};
