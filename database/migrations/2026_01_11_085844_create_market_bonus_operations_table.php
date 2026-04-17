<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_operations', function (Blueprint $table) {
            $table->id()->comment('ID операции по бонусному счёту (ledger)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * ACCOUNT (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('account_id')
                ->comment('Бонусный счёт (market_bonus_accounts.id)');

            /**
             * ВАЖНО:
             * Мы ссылаемся на market_bonus_accounts по индексу:
             * uq_mba_id_tenant = (id, company_id, storefront_id)
             * Поэтому порядок колонок в FK должен быть:
             * (account_id, company_id, storefront_id) -> (id, company_id, storefront_id)
             */

            /* =========================================================
             * OPERATION
             * ========================================================= */

            $table->string('type', 32)
                ->comment('Тип: earn/spend/hold/release/expire/refund/adjust/cancel');

            $table->string('status', 32)
                ->default('posted')
                ->comment('Статус: pending/posted/cancelled/failed');

            $table->decimal('amount', 18, 2)
                ->comment('Сумма операции (+/-)');

            $table->decimal('balance_before', 18, 2)
                ->default(0)
                ->comment('Баланс до');

            $table->decimal('balance_after', 18, 2)
                ->default(0)
                ->comment('Баланс после');

            $table->timestamp('posted_at')
                ->nullable()
                ->comment('Когда проведено');

            $table->timestamp('available_at')
                ->nullable()
                ->comment('Когда станет доступно (earn_delay_days / hold)');

            /* =========================================================
             * OPTIONAL LINKS (FK добавляем только если таблицы уже существуют)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')->nullable()->comment('Заказ (market_orders.id), опционально');
            $table->unsignedBigInteger('payment_id')->nullable()->comment('Платёж (market_payments.id), опционально');
            $table->unsignedBigInteger('refund_id')->nullable()->comment('Возврат денег (market_refunds.id), опционально');
            $table->unsignedBigInteger('return_id')->nullable()->comment('Возврат товара (market_returns.id), опционально');

            /* =========================================================
             * META
             * ========================================================= */

            $table->string('external_id', 128)->nullable()
                ->comment('Внешний ID/ключ (для дедупликации)');

            $table->string('reason_code', 64)->nullable()->comment('Код причины');
            $table->string('reason_text', 255)->nullable()->comment('Текст причины');

            $table->unsignedBigInteger('created_by_user_id')->nullable()
                ->comment('Кто инициировал (users.id), опционально');

            $table->json('meta')->nullable()->comment('Любые доп. данные');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe support (на будущее, если понадобится ссылаться (id, company_id, storefront_id))
            $table->unique(['company_id', 'storefront_id', 'id' ], 'uq_mbo_id_tenant');

            $table->index(['company_id', 'storefront_id'], 'ix_mbo_tenant');
            $table->index(['account_id', 'created_at'], 'ix_mbo_account_time');

            $table->index(['type', 'status', 'created_at'], 'ix_mbo_type_status_time');
            $table->index(['storefront_id', 'status', 'available_at'], 'ix_mbo_storefront_status_available');

            $table->index(['order_id'], 'ix_mbo_order');
            $table->index(['payment_id'], 'ix_mbo_payment');
            $table->index(['refund_id'], 'ix_mbo_refund');
            $table->index(['return_id'], 'ix_mbo_return');

            // MySQL допускает много NULL в UNIQUE — это ок
            $table->unique(['storefront_id', 'external_id'], 'uq_mbo_storefront_external_id');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mbo_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mbo_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe account
            $table->foreign(['account_id', 'company_id', 'storefront_id'], 'fk_mbo_account_tenant')
                ->references(['id', 'company_id', 'storefront_id'])
                ->on('market_bonus_accounts')
                ->cascadeOnDelete();

            $table->foreign('created_by_user_id', 'fk_mbo_created_by_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Маркет: операции бонусного кошелька (ledger), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_operations');
    }
};
