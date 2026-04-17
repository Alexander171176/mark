<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_rules', function (Blueprint $table) {
            $table->id()->comment('ID бонусного правила');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность правила');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок применения');

            /* =========================================================
             * CONTENT
             * ========================================================= */

            $table->string('code', 64)->comment('Код правила (registration_bonus, review_bonus, order_cashback)');
            $table->string('title', 255)->comment('Название правила');
            $table->string('type', 32)->comment('Тип: registration/order/review/promo/manual');
            $table->string('trigger_event', 64)->comment('Событие: user_registered/order_completed/review_created');

            $table->timestamp('starts_at')->nullable()->comment('Начало действия');
            $table->timestamp('ends_at')->nullable()->comment('Окончание действия');

            $table->json('meta')->nullable()->comment('Доп. параметры правила');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для композитных ссылок из других таблиц (если понадобится)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_bonus_rules_tenant_id');

            // Код правила уникален в пределах витрины
            $table->unique(['storefront_id', 'code'], 'uq_market_bonus_rules_storefront_code');

            // списки/применение правил (обычно внутри витрины)
            $table->index(['company_id', 'storefront_id'], 'ix_market_bonus_rules_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_market_bonus_rules_storefront_list');
            $table->index(['storefront_id', 'type', 'activity', 'sort'], 'ix_market_bonus_rules_type_list');
            $table->index(['storefront_id', 'trigger_event', 'activity', 'sort'], 'ix_market_bonus_rules_event_list');

            $table->index(['starts_at', 'ends_at'], 'ix_market_bonus_rules_period');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_market_bonus_rules_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_bonus_rules_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment('Маркет: правила начисления/списания бонусов (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_rules');
    }
};
