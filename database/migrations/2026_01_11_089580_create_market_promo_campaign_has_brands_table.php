<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_campaign_has_brands', function (Blueprint $table) {

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpc_hb_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * Требование:
             * - market_promo_campaigns: unique(company_id, storefront_id, id)
             * - market_brands:         unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpc_hb_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('brand_id')
                ->comment('Бренд (market_brands.id)');

            $table->foreign(['company_id', 'storefront_id', 'brand_id'], 'fk_mpc_hb_brand_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_brands')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри кампании');

            $table->boolean('is_excluded')->default(false)
                ->comment('Исключить бренд из кампании (deny override)');

            $table->timestamps();

            /* =========================================================
             * KEYS / INDEXES
             * ========================================================= */

            $table->primary(
                ['company_id', 'storefront_id', 'campaign_id', 'brand_id'],
                'pk_mpc_hb'
            );

            $table->index(
                ['company_id', 'storefront_id', 'campaign_id', 'activity', 'sort'],
                'ix_mpc_hb_campaign_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'brand_id', 'activity'],
                'ix_mpc_hb_brand_active'
            );

            $table->comment('Маркет: ограничение кампании по брендам (allow/deny), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_campaign_has_brands');
    }
};
