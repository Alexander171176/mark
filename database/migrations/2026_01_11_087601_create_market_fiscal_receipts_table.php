<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_fiscal_receipts', function (Blueprint $table) {
            $table->id()->comment('ID фискального чека');

            /* =========================================================
             * TENANT-SAFE
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_fiscal_receipts_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * Важно: order/payment/tx nullable => используем RESTRICT
             * (nullOnDelete с композитом не подходит)
             * Требования к родителям (MySQL):
             * - market_orders: уник./индекс на (company_id, storefront_id, id)
             * - market_payments: уник./индекс на (company_id, storefront_id, id)
             * - market_payment_transactions: уник./индекс на (company_id, storefront_id, id)
             * ========================================================= */
            $table->unsignedBigInteger('order_id')->nullable()
                ->comment('Заказ (market_orders.id), опционально');

            $table->unsignedBigInteger('payment_id')->nullable()
                ->comment('Платёж (market_payments.id), опционально');

            $table->unsignedBigInteger('payment_transaction_id')->nullable()
                ->comment('Транзакция (market_payment_transactions.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mfr_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_mfr_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'payment_transaction_id'], 'fk_mfr_payment_tx_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payment_transactions')
                ->restrictOnDelete();

            /* =========================================================
             * PROVIDER / CURRENCY
             * ========================================================= */
            $table->foreignId('provider_id')
                ->nullable()
                ->comment('Провайдер оплаты/ОФД/кассы (market_payment_providers.id), опционально')
                ->constrained('market_payment_providers')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->comment('Валюта чека (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            /* =========================================================
             * TYPE / STATUS
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность чека');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('receipt_type', 16)->default('sale')->comment('Тип: sale/refund/correction');
            $table->string('status', 32)->default('created')->comment('Статус: created/processing/issued/failed/cancelled');

            /* =========================================================
             * FISCAL REQUISITES (часть в meta/payload)
             * ========================================================= */
            $table->string('fiscal_provider', 64)->nullable()->comment('Провайдер/касса/ОФД (код/название)');
            $table->string('inn', 32)->nullable()->comment('ИНН/БИН продавца (если применимо)');
            $table->string('kkt_reg_number', 64)->nullable()->comment('Рег. номер ККТ/кассы (если применимо)');
            $table->string('fn_number', 64)->nullable()->comment('Номер ФН (если применимо)');
            $table->string('fd_number', 64)->nullable()->comment('Номер ФД (если применимо)');
            $table->string('fp', 64)->nullable()->comment('ФП/фискальный признак (если применимо)');

            $table->string('shift_number', 32)->nullable()->comment('Номер смены (если применимо)');
            $table->string('receipt_number', 64)->nullable()->comment('Номер чека у провайдера');

            $table->timestamp('issued_at')->nullable()->comment('Дата/время фискализации (выдачи чека)');

            /* =========================================================
             * AMOUNTS
             * ========================================================= */
            $table->decimal('total', 18, 2)->default(0)->comment('Итого по чеку');
            $table->decimal('tax_total', 18, 2)->default(0)->comment('Налоги/НДС всего');

            /* =========================================================
             * URLS / FILES
             * ========================================================= */
            $table->string('receipt_url', 2048)->nullable()->comment('Ссылка на чек у провайдера/ОФД');
            $table->string('pdf_url', 2048)->nullable()->comment('Ссылка на PDF чека (если есть)');
            $table->string('qr_url', 2048)->nullable()->comment('Ссылка на QR/проверку чека (если есть)');

            /* =========================================================
             * PAYLOAD / META / ERROR
             * ========================================================= */
            $table->json('payload')->nullable()->comment('Сырой ответ провайдера/ОФД (для аудита)');
            $table->json('meta')->nullable()->comment('Доп. данные (страна, формат, маппинги, ошибки)');
            $table->string('error_message', 255)->nullable()->comment('Ошибка (если failed)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_fiscal_receipts_tenant_id');
            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mfr_tenant_time');
            $table->index(['storefront_id', 'status', 'issued_at'], 'ix_mfr_list');
            $table->index(['provider_id', 'status'], 'ix_mfr_provider_status');
            $table->index(['receipt_type', 'status'], 'ix_mfr_type_status');

            // Быстрые выборки по связям + поддержка композитных FK
            $table->index(['order_id'], 'ix_mfr_order_id');
            $table->index(['payment_id'], 'ix_mfr_payment_id');
            $table->index(['payment_transaction_id'], 'ix_mfr_payment_tx_id');

            $table->index(['company_id', 'storefront_id', 'order_id'], 'ix_mfr_order_tenant');
            $table->index(['company_id', 'storefront_id', 'payment_id'], 'ix_mfr_payment_tenant');
            $table->index(['company_id', 'storefront_id', 'payment_transaction_id'], 'ix_mfr_payment_tx_tenant');

            $table->comment('Маркет: фискальные чеки (sale/refund/correction), tenant-safe, с реквизитами и ссылками');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_fiscal_receipts');
    }
};
