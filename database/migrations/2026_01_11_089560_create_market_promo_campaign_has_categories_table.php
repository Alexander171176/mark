<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_campaign_has_categories', function (Blueprint $table) {

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpc_hc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * Требование:
             * - market_promo_campaigns: unique(company_id, storefront_id, id)
             * - market_categories:      unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpc_hc_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('category_id')
                ->comment('Категория (market_categories.id)');

            $table->foreign(['company_id', 'storefront_id', 'category_id'], 'fk_mpc_hc_category_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_categories')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри кампании');

            $table->boolean('include_children')->default(true)
                ->comment('Распространять на подкатегории (логика в приложении)');

            $table->boolean('is_excluded')->default(false)
                ->comment('Исключить категорию из кампании (deny override)');

            $table->timestamps();

            /* =========================================================
             * KEYS / INDEXES
             * ========================================================= */

            $table->primary(
                ['company_id', 'storefront_id', 'campaign_id', 'category_id'],
                'pk_mpc_hc'
            );

            $table->index(
                ['company_id', 'storefront_id', 'campaign_id', 'activity', 'sort'],
                'ix_mpc_hc_campaign_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'category_id', 'activity'],
                'ix_mpc_hc_category_active'
            );

            $table->comment('Маркет: ограничение кампании по категориям (allow/deny), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_campaign_has_categories');
    }
};
