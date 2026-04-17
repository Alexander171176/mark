<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_spend_allocations', function (Blueprint $table) {
            $table->id()->comment('ID распределения списания бонусов по партиям начислений');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            // Операция бонусов (spend/refund/release/...)
            $table->unsignedBigInteger('operation_id')
                ->comment('Операция бонусов (market_bonus_operations.id)');

            // Партия начисления (FIFO)
            $table->unsignedBigInteger('expiration_id')
                ->comment('Партия начисления (market_bonus_expirations.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->decimal('amount', 18, 2)
                ->comment('Сумма распределения (+ вернули / - списали)');

            // Контекст (оставляем как nullable-поля без FK, пока не зафиксированы tenant-safe ключи этих таблиц)
            $table->unsignedBigInteger('order_id')->nullable()->comment('Заказ (market_orders.id), опционально');
            $table->unsignedBigInteger('refund_id')->nullable()->comment('Возврат денег (market_refunds.id), опционально');
            $table->unsignedBigInteger('return_id')->nullable()->comment('Возврат товара (market_returns.id), опционально');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Обычно достаточно 1 строки на пару operation + expiration
            $table->unique(
                ['company_id', 'storefront_id', 'operation_id', 'expiration_id'],
                'uq_mba_tenant_operation_expiration'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mba_tenant');
            $table->index(['company_id', 'storefront_id', 'operation_id'], 'ix_mba_operation_tenant');
            $table->index(['company_id', 'storefront_id', 'expiration_id'], 'ix_mba_expiration_tenant');

            $table->index(['order_id'], 'ix_mba_order');
            $table->index(['refund_id'], 'ix_mba_refund');
            $table->index(['return_id'], 'ix_mba_return');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // company
            $table->foreign('company_id', 'fk_mbsa_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mbsa_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe operation (ВАЖНО: порядок company_id, storefront_id, operation_id)
            $table->foreign(['company_id', 'storefront_id', 'operation_id'], 'fk_mbsa_operation_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_operations')
                ->cascadeOnDelete();

            // tenant-safe expiration (ВАЖНО: порядок company_id, storefront_id, expiration_id)
            $table->foreign(['company_id', 'storefront_id', 'expiration_id'], 'fk_mbsa_expiration_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_expirations')
                ->cascadeOnDelete();

            $table->comment('Маркет: распределение списания/возврата бонусов по партиям начислений (FIFO), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_spend_allocations');
    }
};
