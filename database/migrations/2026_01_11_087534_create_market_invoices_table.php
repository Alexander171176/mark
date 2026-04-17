<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_invoices', function (Blueprint $table) {
            $table->id()->comment('ID инвойса/счёта');

            /* =========================================================
             * TENANT-SAFE
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_invoices_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->comment('Заказ (market_orders.id)');

            $table->unsignedBigInteger('payment_id')
                ->nullable()
                ->comment('Платёж (market_payments.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_market_invoices_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            /**
             * ✅ Платёж tenant-safe через (company_id, storefront_id, payment_id)
             * Требование: в market_payments должен быть UNIQUE(company_id, storefront_id, id)
             * (если его ещё нет — добавим отдельной правкой в market_payments)
             *
             * ВАЖНО: payment_id nullable → нельзя ON DELETE SET NULL на композитный FK,
             * т.к. company_id/storefront_id NOT NULL. Поэтому RESTRICT.
             */
            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_market_invoices_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->restrictOnDelete();

            /* =========================================================
             * INVOICE DATA
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность инвойса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('status', 32)->default('issued')->comment('draft/issued/sent/paid/cancelled/void');
            $table->string('type', 32)->default('invoice')->comment('invoice/act/packing_list/...');

            $table->string('number', 64)->comment('Номер инвойса/счёта (уникален в рамках витрины)');
            $table->string('locale', 10)->nullable()->comment('Локаль документа (ru/kk/en), если отличается');

            $table->timestamp('issued_at')->nullable()->comment('Дата выставления');
            $table->timestamp('due_at')->nullable()->comment('Срок оплаты');
            $table->timestamp('paid_at')->nullable()->comment('Дата оплаты');

            /* =========================================================
             * PAYER SNAPSHOT
             * ========================================================= */

            $table->string('payer_type', 16)->default('individual')->comment('individual/legal');

            $table->string('buyer_first_name', 100)->nullable()->comment('Имя (физ. лицо)');
            $table->string('buyer_last_name', 100)->nullable()->comment('Фамилия (физ. лицо)');
            $table->string('buyer_phone', 50)->nullable()->comment('Телефон');
            $table->string('buyer_email', 255)->nullable()->comment('Email');

            $table->string('buyer_messenger_type', 32)->nullable()->comment('whatsapp/telegram/viber/... ');
            $table->string('buyer_messenger_contact', 255)->nullable()->comment('номер/@username/ссылка');

            $table->string('legal_company_name', 255)->nullable()->comment('Юр. лицо: наименование');
            $table->string('legal_bin_iin', 32)->nullable()->comment('Юр. лицо: БИН/ИИН');
            $table->string('legal_vat_id', 64)->nullable()->comment('Юр. лицо: НДС/рег. номер');
            $table->string('legal_contact_name', 255)->nullable()->comment('Контактное лицо');
            $table->string('legal_address', 512)->nullable()->comment('Юр. адрес');

            /* =========================================================
             * AMOUNTS
             * ========================================================= */

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_total', 18, 2)->default(0);
            $table->decimal('delivery_total', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->foreignId('currency_id')
                ->comment('Валюта документа (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            /* =========================================================
             * FILE / STORAGE
             * ========================================================= */

            $table->string('pdf_url', 2048)->nullable()->comment('URL PDF');
            $table->string('storage_disk', 64)->nullable()->comment('public/s3/... ');
            $table->string('storage_path', 1024)->nullable()->comment('Путь к файлу');

            $table->json('meta')->nullable()->comment('Доп. данные');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_invoices_tenant_id');
            $table->unique(['storefront_id', 'number'], 'uq_market_invoices_storefront_number');

            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_market_invoices_tenant_time');
            $table->index(['storefront_id', 'status', 'issued_at'], 'ix_market_invoices_list');

            $table->index(['order_id'], 'ix_market_invoices_order');
            $table->index(['payment_id'], 'ix_market_invoices_payment');
            $table->index(['status'], 'ix_market_invoices_status');

            $table->comment('Маркет: инвойсы/счета/документы оплаты, tenant-safe (снимок плательщика)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_invoices');
    }
};
