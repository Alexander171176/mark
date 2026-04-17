<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_payment_histories', function (Blueprint $table) {
            $table->id()->comment('ID записи истории платежа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mph_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            // payment (обязательно)
            $table->unsignedBigInteger('payment_id')
                ->comment('Платёж (market_payments.id)');

            // Требование: в market_payments есть unique(company_id, storefront_id, id)
            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_mph_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->cascadeOnDelete();

            // order (опционально, денормализация для быстрых выборок)
            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), nullable');

            $table->foreign('order_id', 'fk_mph_order')
                ->references('id')
                ->on('market_orders')
                ->nullOnDelete();

            // provider (опционально, удобно для фильтров)
            $table->unsignedBigInteger('provider_id')
                ->nullable()
                ->comment('Провайдер (market_payment_providers.id), опционально');

            $table->foreign('provider_id', 'fk_mph_provider')
                ->references('id')
                ->on('market_payment_providers')
                ->nullOnDelete();

            // transaction/webhook (опционально)
            $table->unsignedBigInteger('payment_transaction_id')
                ->nullable()
                ->comment('Транзакция/событие провайдера (market_payment_transactions.id), опционально');

            $table->foreign('payment_transaction_id', 'fk_mph_tx')
                ->references('id')
                ->on('market_payment_transactions')
                ->nullOnDelete();

            /* =========================================================
             * EVENT / STATUS
             * ========================================================= */

            $table->string('event', 32)->comment('created/status_changed/paid/failed/cancelled/captured/refunded/amount_changed/note/...');

            $table->string('status_from', 32)->nullable()->comment('Статус ДО (market_payments.status)');
            $table->string('status_to', 32)->nullable()->comment('Статус ПОСЛЕ (market_payments.status)');

            /* =========================================================
             * AMOUNT DELTAS
             * ========================================================= */

            $table->decimal('amount_delta', 18, 2)->nullable()->comment('Дельта amount');
            $table->decimal('captured_delta', 18, 2)->nullable()->comment('Дельта captured_amount');
            $table->decimal('refunded_delta', 18, 2)->nullable()->comment('Дельта refunded_amount');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), если дельты заданы')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * SOURCE / ACTOR
             * ========================================================= */

            $table->string('source', 32)->default('system')->comment('system/admin/user/provider/webhook/cron');

            $table->foreignId('actor_user_id')
                ->nullable()
                ->comment('Пользователь-инициатор (users.id), если применимо')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * UI / AUDIT DATA
             * ========================================================= */

            $table->string('title', 255)->nullable()->comment('Заголовок события');
            $table->text('message')->nullable()->comment('Описание');

            $table->json('changes')->nullable()->comment('Изменённые поля (old/new)');
            $table->json('meta')->nullable()->comment('Доп. метаданные (ip, user_agent, payload refs...)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mph_tenant_time');
            $table->index(['payment_id', 'created_at'], 'ix_mph_payment_time');
            $table->index(['order_id', 'created_at'], 'ix_mph_order_time');
            $table->index(['provider_id', 'created_at'], 'ix_mph_provider_time');
            $table->index(['event', 'created_at'], 'ix_mph_event_time');
            $table->index(['source', 'created_at'], 'ix_mph_source_time');

            $table->comment('Маркет: история платежей (аудит статусов/сумм), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_payment_histories');
    }
};
