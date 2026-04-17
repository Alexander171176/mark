<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_order_documents', function (Blueprint $table) {
            $table->id()->comment('ID документа по заказу');

            /* =========================================================
             * TENANT-SAFE
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mod_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe, все опционально, кроме order_id)
             * ========================================================= */
            $table->unsignedBigInteger('order_id')
                ->comment('Заказ (market_orders.id)');

            // tenant-safe order link
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mod_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('payment_id')
                ->nullable()
                ->comment('Платёж (market_payments.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'payment_id'], 'fk_mod_payment_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_payments')
                ->restrictOnDelete();

            $table->unsignedBigInteger('invoice_id')
                ->nullable()
                ->comment('Инвойс (market_invoices.id), опционально');

            // ВАЖНО: тут достаточно обычного FK, т.к. invoice уже tenant-safe внутри себя (company/storefront)
            // и нам не нужно делать композитный FK с NULL (company/storefront NOT NULL).
            $table->foreign('invoice_id', 'fk_mod_invoice')
                ->references('id')
                ->on('market_invoices')
                ->nullOnDelete();

            $table->unsignedBigInteger('fiscal_receipt_id')
                ->nullable()
                ->comment('Фискальный чек (market_fiscal_receipts.id), опционально');

            $table->foreign('fiscal_receipt_id', 'fk_mod_fiscal_receipt')
                ->references('id')
                ->on('market_fiscal_receipts')
                ->nullOnDelete();

            /* =========================================================
             * TYPE / STATUS / VISIBILITY
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность документа');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('type', 32)->comment('Тип: invoice/fiscal_receipt/packing_list/act/warranty/other');
            $table->string('status', 32)->default('ready')->comment('Статус: ready/processing/failed/archived');
            $table->boolean('is_public')->default(true)->comment('Показывать клиенту в ЛК');

            $table->string('title', 255)->comment('Название для UI (например: "Фискальный чек", "Счёт №...")');
            $table->string('number', 64)->nullable()->comment('Номер документа (если есть)');
            $table->string('locale', 10)->nullable()->comment('Локаль документа (если есть)');

            /* =========================================================
             * FILE / URL
             * ========================================================= */
            $table->string('file_url', 2048)->nullable()->comment('Прямая ссылка на документ (если хранится внешне)');
            $table->string('storage_disk', 64)->nullable()->comment('Диск (public/s3/...)');
            $table->string('storage_path', 1024)->nullable()->comment('Путь к файлу на диске');
            $table->string('mime', 128)->nullable()->comment('MIME тип (application/pdf и т.п.)');
            $table->unsignedBigInteger('size_bytes')->nullable()->comment('Размер файла (байты)');
            $table->string('sha256', 64)->nullable()->comment('Хеш файла (sha256)');

            $table->string('note', 255)->nullable()->comment('Комментарий/заметка');
            $table->json('meta')->nullable()->comment('Доп. данные (провайдер, qr, ссылки проверки, ошибки)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */
            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mod_tenant_time');
            $table->index(['company_id', 'storefront_id', 'order_id', 'activity', 'sort'], 'ix_mod_order_list');

            $table->index(['type', 'status'], 'ix_mod_type_status');
            $table->index(['is_public', 'activity'], 'ix_mod_public_active');

            $table->index(['payment_id'], 'ix_mod_payment');
            $table->index(['invoice_id'], 'ix_mod_invoice');
            $table->index(['fiscal_receipt_id'], 'ix_mod_fiscal_receipt');

            $table->comment('Маркет: документы по заказу (PDF/URL) — инвойсы, чеки, накладные и др., tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_documents');
    }
};
