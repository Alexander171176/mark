<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_referral_events', function (Blueprint $table) {
            $table->id()->comment('ID события рефералки');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // ✅ уникальное имя FK
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrefe_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * REFERRAL CODE (tenant-safe)
             * Требование: в market_referral_codes есть unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('referral_code_id')
                ->comment('Реферальный код (market_referral_codes.id)');

            $table->foreign(['company_id', 'storefront_id', 'referral_code_id'], 'fk_mrefe_referral_code_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_referral_codes')
                ->cascadeOnDelete();

            /* =========================================================
             * USERS
             * ========================================================= */

            $table->foreignId('referrer_user_id')
                ->comment('Реферер (users.id) — пригласивший (обычно владелец referral_code)')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('referee_user_id')
                ->nullable()
                ->comment('Приглашённый пользователь (users.id), если уже известен')
                ->constrained('users')
                ->nullOnDelete();

            // Снапшоты контактов (если юзер ещё не создан/не подтверждён)
            $table->string('referee_email', 255)->nullable()->comment('Email приглашённого (snapshot)');
            $table->string('referee_phone', 50)->nullable()->comment('Телефон приглашённого (snapshot)');

            /* =========================================================
             * EVENT
             * ========================================================= */

            $table->string('event_type', 32)->comment('Тип события: click/signup/first_order/rewarded');

            /* =========================================================
             * ORDER (tenant-safe, optional)
             * ⚠️ нельзя SET NULL для композитного FK с NOT NULL company_id/storefront_id
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), если событие связано с заказом');

            // ✅ вместо nullOnDelete() -> restrict/cascade
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mrefe_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            /* =========================================================
             * TECH / PAYLOAD
             * ========================================================= */

            $table->string('ip', 64)->nullable()->comment('IP (опционально)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent (опционально)');
            $table->json('payload')->nullable()->comment('Детали (utm, контекст, ответы сервисов)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mrefe_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mrefe_tenant');

            $table->index(['storefront_id', 'event_type', 'created_at'], 'ix_mrefe_storefront_type_time');
            $table->index(['company_id', 'storefront_id', 'referral_code_id', 'created_at'], 'ix_mrefe_code_time');

            $table->index(['referrer_user_id', 'created_at'], 'ix_mrefe_referrer_time');
            $table->index(['referee_user_id', 'created_at'], 'ix_mrefe_referee_time');

            $table->index(['company_id', 'storefront_id', 'order_id'], 'ix_mrefe_order');

            $table->comment('Маркет: события реферальной системы (клики/регистрации/заказы/награды), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_referral_events');
    }
};
