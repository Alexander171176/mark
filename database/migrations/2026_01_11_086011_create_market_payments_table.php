<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_payments', function (Blueprint $table) {
            $table->id()->comment('ID платежа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_payments_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * ORDER (tenant-safe по storefront)
             * Требование: в market_orders есть unique(storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->comment('Заказ (market_orders.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_market_payments_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            /* =========================================================
             * PROVIDER / SETTINGS
             * ========================================================= */

            $table->unsignedBigInteger('provider_id')
                ->nullable()
                ->comment('Провайдер (market_payment_providers.id), nullable');

            // базовый справочник провайдеров (глобальный)
            $table->foreign('provider_id', 'fk_market_payments_provider')
                ->references('id')
                ->on('market_payment_providers')
                ->nullOnDelete(); // или ->restrictOnDelete() если хочешь запретить удаление провайдера

            // Подключение провайдера к витрине (аудит "каким подключением платили")
            $table->unsignedBigInteger('storefront_provider_setting_id')
                ->nullable()
                ->comment('Подключение провайдера к витрине (market_storefront_payment_provider_settings.id), nullable');

            $table->foreign(
                'storefront_provider_setting_id',
                'fk_market_payments_storefront_provider_setting'
            )
                ->references('id')
                ->on('market_storefront_payment_provider_settings')
                ->nullOnDelete();

            /* =========================================================
             * PAYMENT FIELDS
             * ========================================================= */

            $table->string('method', 32)->default('card')
                ->comment('Метод: card/bank_transfer/qr/cash_on_delivery/wallet/...');

            $table->string('status', 32)->default('created')
                ->comment('Статус: created/pending/paid/failed/cancelled/refunded/partial_refunded');

            // Идентификаторы провайдера
            $table->string('provider_payment_id', 128)->nullable()->comment('ID платежа у провайдера');
            $table->string('provider_order_id', 128)->nullable()->comment('ID заказа/инвойса у провайдера');
            $table->string('provider_customer_id', 128)->nullable()->comment('ID покупателя у провайдера (если есть)');

            /* =========================================================
             * AMOUNTS
             * ========================================================= */

            $table->decimal('amount', 18, 2)->comment('Сумма платежа');
            $table->decimal('captured_amount', 18, 2)->default(0)->comment('Фактически списано/захвачено');
            $table->decimal('refunded_amount', 18, 2)->default(0)->comment('Возвращено');

            $table->foreignId('currency_id')
                ->comment('Валюта платежа (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->decimal('fee_amount', 18, 2)->nullable()->comment('Комиссия провайдера');
            $table->decimal('net_amount', 18, 2)->nullable()->comment('Сумма за вычетом комиссии');

            /* =========================================================
             * META / URLS / PAYLOAD
             * ========================================================= */

            $table->string('locale', 10)->nullable()->comment('Локаль платежа');
            $table->string('return_url', 255)->nullable()->comment('URL возврата после оплаты');
            $table->string('payment_url', 255)->nullable()->comment('URL на оплату (redirect)');

            $table->json('request_payload')->nullable()->comment('Запрос к провайдеру (debug)');
            $table->json('response_payload')->nullable()->comment('Ответ провайдера (debug)');

            /* =========================================================
             * TIMES
             * ========================================================= */

            $table->timestamp('paid_at')->nullable()->comment('Когда оплачен');
            $table->timestamp('failed_at')->nullable()->comment('Когда упал');
            $table->timestamp('cancelled_at')->nullable()->comment('Когда отменён');

            /* =========================================================
             * ACTOR
             * ========================================================= */

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал платеж (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ для будущих tenant-safe FK (если понадобится)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_payments_tenant_id');
            $table->index(['company_id', 'storefront_id', 'order_id'], 'ix_market_payments_order_tenant');

            $table->index(['company_id', 'storefront_id'], 'ix_market_payments_tenant');
            $table->index(['order_id'], 'ix_market_payments_order');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_market_payments_list');

            $table->index(['provider_id', 'provider_payment_id'], 'ix_market_payments_provider_payment');
            $table->index(['provider_id', 'provider_order_id'], 'ix_market_payments_provider_order');
            $table->index(['currency_id'], 'ix_market_payments_currency');
            $table->index(['paid_at'], 'ix_market_payments_paid_at');

            // Дедупликация платежей у провайдера (nullable поля — MySQL допускает много NULL)
            $table->unique(['provider_id', 'provider_payment_id'], 'uq_market_payments_provider_payment_id');

            $table->comment('Маркет: платежи по заказам (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_payments');
    }
};
