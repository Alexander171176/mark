<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_catalog_group_items', function (Blueprint $table) {
            $table->id()->comment('ID элемента группы каталога');

            /* =========================================================
             * GROUP
             * ========================================================= */
            $table->foreignId('group_id')
                ->comment('Группа (market_catalog_groups.id)')
                ->constrained('market_catalog_groups')
                ->cascadeOnDelete();

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец витрины (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // гарантия: витрина принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mcgi_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TARGET (товар или вариант)
             * ========================================================= */
            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), если привязка на уровне товара');

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант (market_product_variants.id), если привязка на уровне варианта');

            /**
             * ВАЖНО:
             * SET NULL здесь невозможен, потому что FK включает storefront_id (NOT NULL).
             * Поэтому используем RESTRICT (или CASCADE по желанию).
             */

            // индекс под FK (не обязателен всегда, но полезен и часто спасает от 1822)
            $table->index(['storefront_id', 'product_id'], 'ix_mcgi_storefront_product_fk');
            $table->index(['storefront_id', 'product_variant_id'], 'ix_mcgi_storefront_variant_fk');

            // tenant-safe: товар/вариант должны быть в этой витрине
            $table->foreign(['storefront_id', 'product_id'], 'fk_mcgi_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->restrictOnDelete();

            $table->foreign(['storefront_id', 'product_variant_id'], 'fk_mcgi_storefront_variant')
                ->references(['storefront_id', 'id'])
                ->on('market_product_variants')
                ->restrictOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность элемента в группе');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри группы');
            $table->boolean('is_pinned')->default(false)->comment('Закрепить сверху');

            $table->json('meta')->nullable()->comment('Метаданные элемента (лейблы, причины попадания, A/B и т.п.)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Дедуп: один товар/вариант не повторяется в группе в рамках витрины
            $table->unique(['group_id', 'storefront_id', 'product_id'], 'uq_mcgi_group_storefront_product');
            $table->unique(['group_id', 'storefront_id', 'product_variant_id'], 'uq_mcgi_group_storefront_variant');

            // Частые запросы: элементы группы для витрины
            $table->index(['group_id', 'company_id', 'storefront_id', 'activity', 'is_pinned', 'sort'], 'ix_mcgi_list');

            // Поиск: в каких группах товар/вариант
            $table->index(['company_id', 'storefront_id', 'product_id'], 'ix_mcgi_product');
            $table->index(['company_id', 'storefront_id', 'product_variant_id'], 'ix_mcgi_variant');

            // Списки по витрине
            $table->index(['company_id', 'storefront_id', 'activity'], 'ix_mcgi_storefront_active');

            $table->comment('Маркет: элементы глобальных групп каталога (привязка к товарам/вариантам), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_catalog_group_items');
    }
};
