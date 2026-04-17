<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_warehouse_stocks', function (Blueprint $table) {
            $table->id()->comment('ID остатка на складе');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS
             * ========================================================= */

            $table->unsignedBigInteger('warehouse_id')
                ->comment('Склад (market_warehouses.id)');

            $table->unsignedBigInteger('product_variant_id')
                ->comment('Вариант товара (market_product_variants.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Учет остатка активен');

            $table->unsignedInteger('quantity')->default(0)->comment('Фактический остаток');
            $table->unsignedInteger('reserved_quantity')->default(0)->comment('Зарезервировано');
            $table->unsignedInteger('incoming_quantity')->default(0)->comment('Ожидается поступление');

            $table->unsignedInteger('low_stock_threshold')->default(0)->comment('Порог низкого остатка');

            // Кто последний менял (опционально)
            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->comment('Кто обновил (users.id)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES (сначала ключи, потом FK)
             * ========================================================= */

            // 1 запись на склад + вариант
            $table->unique(['warehouse_id', 'product_variant_id'], 'uq_market_warehouse_stock_unique');

            $table->index(['company_id', 'storefront_id'], 'ix_mws_tenant');
            $table->index(['warehouse_id', 'activity'], 'ix_mws_warehouse_active');
            $table->index(['storefront_id', 'activity'], 'ix_mws_storefront_active');

            $table->index(['product_variant_id'], 'ix_mws_variant');
            $table->index(['product_variant_id', 'activity'], 'ix_mws_variant_active');

            $table->index(['warehouse_id', 'low_stock_threshold'], 'ix_mws_low_stock');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mws_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // ✅ склад обязан быть в этой же company/storefront
            $table->foreign(['company_id', 'storefront_id', 'warehouse_id'], 'fk_mws_warehouse_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_warehouses')
                ->cascadeOnDelete();

            // ✅ вариант обязан быть в этой же company/storefront (строго tenant-safe)
            $table->foreign(['company_id', 'storefront_id', 'product_variant_id'], 'fk_mws_variant_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_product_variants')
                ->cascadeOnDelete();

            // ✅ кто обновил (nullable) — но единый подход: cascade
            $table->foreign('updated_by_user_id', 'fk_mws_updated_by_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->comment('Маркет: остатки товаров на складах (tenant-safe, cascade-only)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_warehouse_stocks');
    }
};
