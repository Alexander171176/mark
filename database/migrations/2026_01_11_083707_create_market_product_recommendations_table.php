<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_product_recommendations', function (Blueprint $table) {
            $table->id()->comment('ID рекомендации');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * SOURCE / RECOMMENDED
             * ========================================================= */

            // Один из источников может быть задан: товар или категория (оба nullable)
            $table->unsignedBigInteger('source_product_id')
                ->nullable()
                ->comment('Товар-источник (market_products.id), nullable');

            $table->unsignedBigInteger('source_category_id')
                ->nullable()
                ->comment('Категория-источник (market_categories.id), nullable');

            $table->unsignedBigInteger('recommended_product_id')
                ->comment('Рекомендуемый товар (market_products.id)');

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность рекомендации');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->timestamps();

            /* =========================================================
             * TENANT-SAFE FOREIGN KEYS
             * ========================================================= */

            // source_product в рамках company+storefront
            $table->foreign(['company_id', 'storefront_id', 'source_product_id'], 'fk_mpr_source_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            // source_category в рамках company+storefront
            $table->foreign(['company_id', 'storefront_id', 'source_category_id'], 'fk_mpr_source_category_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_categories')
                ->cascadeOnDelete();

            // recommended_product в рамках company+storefront
            $table->foreign(['company_id', 'storefront_id', 'recommended_product_id'], 'fk_mpr_recommended_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            /* =========================================================
             * UNIQUES / INDEXES
             * ========================================================= */

            // Уникальность рекомендаций для товара-источника (когда source_product_id заполнен)
            $table->unique(
                ['storefront_id', 'locale', 'source_product_id', 'recommended_product_id'],
                'uq_mpr_storefront_locale_source_product_recommended'
            );

            // Уникальность рекомендаций для категории-источника (когда source_category_id заполнен)
            $table->unique(
                ['storefront_id', 'locale', 'source_category_id', 'recommended_product_id'],
                'uq_mpr_storefront_locale_source_category_recommended'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mpr_tenant');
            $table->index(['storefront_id', 'locale'], 'ix_mpr_storefront_locale');

            $table->index(
                ['storefront_id', 'locale', 'source_product_id', 'activity', 'sort'],
                'ix_mpr_source_product_list'
            );

            $table->index(
                ['storefront_id', 'locale', 'source_category_id', 'activity', 'sort'],
                'ix_mpr_source_category_list'
            );

            $table->index(['recommended_product_id'], 'ix_mpr_recommended_product');
            $table->index(['locale'], 'ix_mpr_locale');

            $table->comment('Маркет: рекомендованные товары (tenant-safe), источник товар/категория, каскадное удаление без "мёртвых" записей');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_product_recommendations');
    }
};
