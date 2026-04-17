<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_codes', function (Blueprint $table) {
            $table->id()->comment('ID промокода/купона');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // ✅ имя FK должно быть уникальным во всей БД
            $table->foreign(['company_id', 'storefront_id'], 'fk_mpcodes_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * CAMPAIGN (tenant-safe)
             * Требование: в market_promo_campaigns есть unique на (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpcodes_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            /* =========================================================
             * BATCH (tenant-safe, optional)
             * Требование: в market_promo_code_batches есть unique на (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('batch_id')
                ->nullable()
                ->comment('Пачка промокодов (market_promo_code_batches.id), если код из генерации');

            // ❗ нельзя nullOnDelete() на композитном FK где company_id/storefront_id NOT NULL
            $table->foreign(['company_id', 'storefront_id', 'batch_id'], 'fk_mpcodes_batch_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_code_batches')
                ->restrictOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность кода');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * CODE
             * ========================================================= */

            $table->string('code', 64)->comment('Код (то, что вводит пользователь)');

            $table->string('code_type', 32)->default('public')
                ->comment('Тип кода: public/personal/single_use');

            /* =========================================================
             * SINGLE USE / LIMITS
             * ========================================================= */

            $table->boolean('is_single_use')->default(false)
                ->comment('Одноразовый код (после использования становится недоступным)');

            $table->boolean('is_single_use_per_user')->default(false)
                ->comment('Одноразовый на пользователя (каждый пользователь может использовать 1 раз)');

            $table->unsignedInteger('max_uses_total')->nullable()
                ->comment('Лимит использований этого кода общий (override кампании)');

            $table->unsignedInteger('max_uses_per_user')->nullable()
                ->comment('Лимит использований этого кода на пользователя (override кампании)');

            $table->unsignedInteger('used_count')->default(0)
                ->comment('Счётчик использований (денормализация)');

            $table->timestamp('burned_at')->nullable()
                ->comment('Когда код был использован и “сожжён” (для single_use)');

            /* =========================================================
             * PERSONAL ASSIGNMENT
             * ========================================================= */

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->comment('Кому назначен код (users.id), если personal')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * DATES (override)
             * ========================================================= */

            $table->timestamp('starts_at')->nullable()->comment('Начало действия (override)');
            $table->timestamp('ends_at')->nullable()->comment('Окончание действия (override)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Для будущих tenant-safe ссылок на promo_codes
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpc_tenant_id');

            // Код уникален в рамках витрины
            $table->unique(['storefront_id', 'code'], 'uq_mpc_storefront_code');

            $table->index(['company_id', 'storefront_id'], 'ix_mpc_tenant');

            $table->index(['company_id', 'storefront_id', 'campaign_id', 'activity', 'sort'], 'ix_mpc_campaign_list');
            $table->index(['company_id', 'storefront_id', 'batch_id', 'activity', 'sort'], 'ix_mpc_batch_list');

            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mpc_storefront_list');
            $table->index(['assigned_user_id', 'activity'], 'ix_mpc_assigned_user');

            $table->index(['storefront_id', 'is_single_use', 'activity'], 'ix_mpc_single_use');
            $table->index(['storefront_id', 'starts_at', 'ends_at'], 'ix_mpc_dates');

            $table->comment('Маркет: промокоды/купоны (коды внутри кампаний), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_codes');
    }
};
