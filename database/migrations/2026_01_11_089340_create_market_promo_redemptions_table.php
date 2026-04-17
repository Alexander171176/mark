<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_redemptions', function (Blueprint $table) {
            $table->id()->comment('ID применения промокода/купона');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // ✅ Уникальное имя FK во всей БД
            $table->foreign(['company_id', 'storefront_id'], 'fk_mpred_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('promo_code_id')
                ->comment('Промокод (market_promo_codes.id)');

            // tenant-safe: промокод обязан быть из этой витрины/компании
            $table->foreign(['company_id', 'storefront_id', 'promo_code_id'], 'fk_mpred_promo_code_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_codes')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            // tenant-safe: кампания обязана быть из этой витрины/компании
            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpred_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            // Кто применил
            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            // К чему применили (обычно заказ)
            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), если применение было при оформлении');

            // ⚠️ нельзя nullOnDelete() на композитном FK с NOT NULL company_id/storefront_id
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mpred_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            /* =========================================================
             * STATUS / SNAPSHOT
             * ========================================================= */

            $table->string('status', 32)->default('applied')
                ->comment('Статус: applied/voided/reverted (если отменили/откатили)');

            $table->decimal('discount_amount', 18, 2)->default(0)
                ->comment('Сумма скидки по применению');

            $table->decimal('bonus_amount', 18, 2)->default(0)
                ->comment('Сумма/кол-во бонусов по применению');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта сумм (currencies.id), если применимо')
                ->constrained('currencies')
                ->nullOnDelete();

            // Если бонусы реально выдали — привязка к операции ledger
            $table->unsignedBigInteger('bonus_operation_id')
                ->nullable()
                ->comment('Операция бонусов (market_bonus_operations.id), если начисляли/списывали бонусы');

            // ⚠️ также композитный FK -> только RESTRICT/CASCADE, но не SET NULL
            $table->foreign(['company_id', 'storefront_id', 'bonus_operation_id'], 'fk_mpred_bonus_operation_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_operations')
                ->restrictOnDelete();

            $table->json('payload')->nullable()->comment('Детали расчёта/контекста (json)');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Один и тот же пользователь не может применить один и тот же код дважды
            $table->unique(['promo_code_id', 'user_id'], 'uq_mpr_code_user');

            // На один заказ обычно одно применение конкретного кода
            $table->unique(['promo_code_id', 'order_id'], 'uq_mpr_code_order');

            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mpr_tenant_time');
            $table->index(['campaign_id', 'created_at'], 'ix_mpr_campaign_time');
            $table->index(['storefront_id', 'created_at'], 'ix_mpr_storefront_time');
            $table->index(['user_id', 'created_at'], 'ix_mpr_user_time');
            $table->index(['order_id'], 'ix_mpr_order');

            $table->comment('Маркет: история применений промокодов/купонов (аудит + суммы), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_redemptions');
    }
};
