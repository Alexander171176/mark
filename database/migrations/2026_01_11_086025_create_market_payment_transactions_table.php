<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_payment_transactions', function (Blueprint $table) {
            $table->id()->comment('ID транзакции платежа (попытка/событие/вебхук)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mptx_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * PAYMENT (tenant-safe)
             * Требование: в market_payments есть unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('payment_id')
                ->comment('Платёж (market_payments.id)');

            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_mptx_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->cascadeOnDelete();

            /* =========================================================
             * PROVIDER (денормализация для фильтрации без join)
             * ========================================================= */

            $table->unsignedBigInteger('provider_id')
                ->nullable()
                ->comment('Провайдер (market_payment_providers.id), nullable');

            // Вариант A (как у тебя): FK и nullOnDelete
            $table->foreign('provider_id', 'fk_mptx_provider')
                ->references('id')
                ->on('market_payment_providers')
                ->nullOnDelete();

            /* =========================================================
             * EVENT
             * ========================================================= */

            $table->string('type', 32)->comment('Тип события: init/auth/capture/void/webhook/refund/...');

            $table->string('status', 32)->default('ok')->comment('Статус: ok/error/pending');

            /* =========================================================
             * PROVIDER IDS
             * ========================================================= */

            $table->string('provider_transaction_id', 128)->nullable()->comment('ID транзакции у провайдера');
            $table->string('provider_event_id', 128)->nullable()->comment('ID события/вебхука у провайдера');

            /* =========================================================
             * AMOUNT (если применимо)
             * ========================================================= */

            $table->decimal('amount', 18, 2)->nullable()->comment('Сумма операции (capture/refund и т.п.)');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта операции (currencies.id), nullable')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * RAW PAYLOADS
             * ========================================================= */

            $table->json('request_payload')->nullable()->comment('Запрос/данные (debug)');
            $table->json('response_payload')->nullable()->comment('Ответ/результат (debug)');

            $table->string('error_message', 255)->nullable()->comment('Текст ошибки');

            /* =========================================================
             * ACTOR
             * ========================================================= */

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто инициировал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /* =========================================================
             * INDEXES / UNIQUE
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mptx_tenant');
            $table->index(['payment_id', 'created_at'], 'ix_mptx_payment_time');

            $table->index(['provider_id', 'provider_transaction_id'], 'ix_mptx_provider_tx');
            $table->index(['provider_id', 'provider_event_id'], 'ix_mptx_provider_event');

            $table->index(['type', 'status'], 'ix_mptx_type_status');

            // Дедуп вебхуков (MySQL допускает много NULL — это нормально)
            $table->unique(['provider_id', 'provider_event_id'], 'uq_mptx_provider_event_id');

            $table->comment('Маркет: транзакции платежей (попытки/вебхуки/операции), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_payment_transactions');
    }
};
