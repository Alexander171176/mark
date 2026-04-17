<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_campaign_has_products', function (Blueprint $table) {

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpc_hp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * Требование:
             * - market_promo_campaigns: unique(company_id, storefront_id, id)
             * - market_products:        unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpc_hp_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(['company_id', 'storefront_id', 'product_id'], 'fk_mpc_hp_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри кампании');

            $table->boolean('is_excluded')->default(false)
                ->comment('Исключить товар из кампании (deny override)');

            $table->timestamps();

            /* =========================================================
             * KEYS / INDEXES
             * ========================================================= */

            $table->primary(
                ['company_id', 'storefront_id', 'campaign_id', 'product_id'],
                'pk_mpc_hp'
            );

            $table->index(
                ['company_id', 'storefront_id', 'campaign_id', 'activity', 'sort'],
                'ix_mpc_hp_campaign_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'product_id', 'activity'],
                'ix_mpc_hp_product_active'
            );

            $table->comment('Маркет: ограничение кампании по товарам (allow/deny), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_campaign_has_products');
    }
};
