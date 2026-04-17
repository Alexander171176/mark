<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_category_has_products', function (Blueprint $table) {

            /* ============================
             * TENANT / STOREFRONT
             * ============================ */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mchp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* ============================
             * LINKS
             * ============================ */

            $table->unsignedBigInteger('category_id')->comment('Категория (market_categories.id)');
            $table->unsignedBigInteger('product_id')->comment('Товар (market_products.id)');

            /* ============================
             * FLAGS / SORT
             * ============================ */

            $table->boolean('activity')->default(true)->comment('Активно в этой категории');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка товара внутри категории');

            $table->timestamps();

            /* ============================
             * KEYS / INDEXES
             * ============================ */

            // Уникальность связи в рамках tenant
            $table->primary(['company_id', 'storefront_id', 'category_id', 'product_id'], 'pk_mchp_tenant_category_product');

            /**
             * ✅ tenant-safe FK:
             * Категория и товар обязаны принадлежать этой же company/storefront
             *
             * Требования (у тебя уже заложены в миграциях):
             * - market_categories: UNIQUE(company_id, storefront_id, id)
             * - market_products:   UNIQUE(company_id, storefront_id, id)
             */
            $table->foreign(['company_id', 'storefront_id', 'category_id'], 'fk_mchp_category_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_categories')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'product_id'], 'fk_mchp_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            // Быстрый вывод товаров категории
            $table->index(['company_id', 'storefront_id', 'category_id', 'activity', 'sort'], 'ix_mchp_category_list');

            // Быстрый вывод категорий товара
            $table->index(['company_id', 'storefront_id', 'product_id'], 'ix_mchp_product');

            $table->comment('Маркет: связь категории и товаров (M:N) с tenant-изоляцией по company+storefront');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_category_has_products');
    }
};
