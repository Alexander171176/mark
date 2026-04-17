<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_rule_conditions', function (Blueprint $table) {
            $table->id()->comment('ID условия бонусного правила');

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
             * CONDITION
             * ========================================================= */

            $table->string('field', 64)->comment('Поле: order_total/category_id/payment_method/is_first_order');
            $table->string('operator', 16)->comment('Оператор: =, >, >=, in, not_in, between');
            $table->json('value')->comment('Значение условия (json: scalar/array/object)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mbrc_tenant');
            $table->index(['rule_id'], 'ix_mbrc_rule');
            $table->index(['company_id', 'storefront_id', 'rule_id'], 'ix_mbrc_tenant_rule');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mbrc_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mbrc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe привязка к правилу
            $table->foreign(['company_id', 'storefront_id', 'rule_id'], 'fk_mbrc_rule_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_rules')
                ->cascadeOnDelete();

            $table->comment('Маркет: условия применения бонусных правил (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_rule_conditions');
    }
};
