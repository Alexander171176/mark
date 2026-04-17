<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_rule_rewards', function (Blueprint $table) {
            $table->id()->comment('ID награды бонусного правила');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * RULE (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('rule_id')
                ->comment('Бонусное правило (market_bonus_rules.id)');

            /* =========================================================
             * REWARD
             * ========================================================= */

            $table->string('reward_type', 32)->comment('Тип: fixed/percent');
            $table->decimal('value', 18, 4)->comment('Значение: 500 или 5.0 (%)');

            $table->decimal('max_amount', 18, 2)->nullable()->comment('Максимальный бонус по правилу');
            $table->unsignedInteger('expires_in_days')->nullable()->comment('Срок жизни бонусов (дней), null = не сгорает');

            $table->boolean('hold_until_order_complete')
                ->default(false)
                ->comment('Держать бонусы в hold до завершения заказа');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mbrr_tenant');
            $table->index(['rule_id'], 'ix_mbrr_rule');
            $table->index(['company_id', 'storefront_id', 'rule_id'], 'ix_mbrr_tenant_rule');

            // если у правила предполагается ровно 1 награда — включи unique:
            // $table->unique(['company_id', 'storefront_id', 'rule_id'], 'uq_mbrr_rule_one_reward');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mbrr_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mbrr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe FK на правило
            $table->foreign(['company_id', 'storefront_id', 'rule_id'], 'fk_mbrr_rule_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_rules')
                ->cascadeOnDelete();

            $table->comment('Маркет: награды бонусных правил (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_rule_rewards');
    }
};
