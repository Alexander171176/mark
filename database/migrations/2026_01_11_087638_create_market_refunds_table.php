<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_refunds', function (Blueprint $table) {
            $table->id()->comment('ID возврата средств');

            /* =========================================================
             * TENANT-SAFE
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */
            $table->unsignedBigInteger('order_id')->comment('Заказ (market_orders.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mr_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('payment_id')
                ->nullable()
                ->comment('Платёж (market_payments.id), может быть null (внутренний баланс/бонусы)');

            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_mr_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->restrictOnDelete();

            $table->unsignedBigInteger('payment_transaction_id')
                ->nullable()
                ->comment('Транзакция платежа (market_payment_transactions.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'payment_transaction_id'], 'fk_mr_tx_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payment_transactions')
                ->restrictOnDelete(); // или cascade

            /* =========================================================
             * BUSINESS FIELDS
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность возврата');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('type', 32)->default('refund')
                ->comment('Тип: refund/partial_refund/chargeback/correction');

            $table->string('status', 32)->default('created')
                ->comment('Статус: created/pending/processing/succeeded/failed/cancelled');

            // Частичный/полный
            $table->boolean('is_partial')->default(false)
                ->comment('Частичный возврат (true) или полный (false)');

            $table->string('channel', 32)->default('provider')
                ->comment('Канал: provider/manual/internal_balance');

            /* =========================================================
             * AMOUNTS
             * ========================================================= */
            $table->decimal('amount', 18, 2)->comment('Сумма возврата');

            $table->foreignId('currency_id')
                ->comment('Валюта возврата (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            /* =========================================================
             * REASON
             * ========================================================= */
            $table->string('reason_code', 64)->nullable()
                ->comment('Код причины: customer_request/return_goods/defect/delivery_issue/fraud/...');

            $table->string('reason_text', 255)->nullable()
                ->comment('Текст причины (человекочитаемо)');

            /* =========================================================
             * PROVIDER / IDS
             * ========================================================= */
            $table->foreignId('provider_id')
                ->nullable()
                ->comment('Провайдер оплаты (market_payment_providers.id), если применимо')
                ->constrained('market_payment_providers')
                ->nullOnDelete();

            $table->string('provider_refund_id', 128)->nullable()->comment('ID возврата у провайдера');
            $table->string('provider_status', 64)->nullable()->comment('Статус у провайдера (сырой)');

            /* =========================================================
             * DEBUG / AUDIT
             * ========================================================= */
            $table->json('request_payload')->nullable()->comment('Запрос к провайдеру (для дебага)');
            $table->json('response_payload')->nullable()->comment('Ответ провайдера (для дебага)');
            $table->string('error_message', 255)->nullable()->comment('Текст ошибки (если failed)');

            /* =========================================================
             * TIMES
             * ========================================================= */
            $table->timestamp('processed_at')->nullable()->comment('Когда отправили в обработку');
            $table->timestamp('succeeded_at')->nullable()->comment('Когда успешно');
            $table->timestamp('failed_at')->nullable()->comment('Когда упал');
            $table->timestamp('cancelled_at')->nullable()->comment('Когда отменён');

            /* =========================================================
             * ACTOR
             * ========================================================= */
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал возврат (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->json('meta')->nullable()->comment('Доп. данные (комиссии, правила, ссылки, внутренние флаги)');

            $table->timestamps();

            /* =========================================================
             * INDEXES / UNIQUE
             * ========================================================= */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_refunds_tenant_id');
            $table->index(['company_id', 'storefront_id'], 'ix_mr_tenant');
            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mr_tenant_time');

            $table->index(['order_id', 'created_at'], 'ix_mr_order_time');
            $table->index(['payment_id', 'created_at'], 'ix_mr_payment_time');

            $table->index(['status', 'created_at'], 'ix_mr_status_time');
            $table->index(['provider_id', 'provider_refund_id'], 'ix_mr_provider_refund');

            // дедуп у провайдера (nullable => много NULL допустимо)
            $table->unique(['provider_id', 'provider_refund_id'], 'uq_mr_provider_refund_id');

            $table->comment('Маркет: возвраты средств по заказам/платежам (tenant-safe), OZON-подход');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_refunds');
    }
};
