<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_kit_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции комплекта');

            /* =========================================================
             * TENANT / STOREFRONT (изоляция company+storefront)
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_kit_items_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS
             * ========================================================= */

            $table->unsignedBigInteger('kit_id')
                ->comment('Комплект (market_kits.id)');

            $table->unsignedBigInteger('product_variant_id')
                ->comment('Вариант товара (market_product_variants.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность позиции в комплекте');
            $table->unsignedInteger('quantity')->default(1)->comment('Кол-во этого варианта в комплекте');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка позиции');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Уникальность позиции комплекта (в рамках tenant)
            $table->unique(
                ['company_id', 'storefront_id', 'kit_id', 'product_variant_id'],
                'uq_mki_tenant_kit_variant'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mki_tenant');
            $table->index(['storefront_id'], 'ix_mki_storefront');
            $table->index(['kit_id', 'activity', 'sort'], 'ix_mki_kit_list');
            $table->index(['product_variant_id'], 'ix_mki_variant');

            /* =========================================================
             * TENANT-SAFE FK (В КОНЦЕ, ПОСЛЕ ИНДЕКСОВ)
             * ========================================================= */

            // ✅ Комплект обязан принадлежать company+storefront
            // Требование: в market_kits есть UNIQUE(company_id, storefront_id, id)
            $table->foreign(['company_id', 'storefront_id', 'kit_id'], 'fk_mki_kit_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_kits')
                ->cascadeOnDelete();

            // ✅ Вариант обязан принадлежать company+storefront
            // Требование: в market_product_variants есть UNIQUE(company_id, storefront_id, id)
            $table->foreign(['company_id', 'storefront_id', 'product_variant_id'], 'fk_mki_variant_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_product_variants')
                ->cascadeOnDelete();

            $table->comment('Маркет: состав комплекта товаров (kit items), tenant-safe по company+storefront. Cascade delete по всем родителям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_kit_items');
    }
};
