<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_refund_documents', function (Blueprint $table) {
            $table->id()->comment('ID документа возврата');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrd_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            // Основная связь — возврат (tenant-safe)
            $table->unsignedBigInteger('refund_id')
                ->comment('Возврат (market_refunds.id)');

            $table->foreign(['company_id', 'storefront_id', 'refund_id'], 'fk_mrd_refund_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_refunds')
                ->cascadeOnDelete();

            // Дублируем order_id для удобного вывода в карточке заказа
            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mrd_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete(); // ✅

            // Документ может быть связан с фискальным чеком (tenant-safe)
            $table->unsignedBigInteger('fiscal_receipt_id')
                ->nullable()
                ->comment('Фискальный чек (market_fiscal_receipts.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'fiscal_receipt_id'], 'fk_mrd_fiscal_receipt_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_fiscal_receipts')
                ->restrictOnDelete(); // ✅

            /* =========================================================
             * FLAGS / VISIBILITY
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность документа');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');
            $table->boolean('is_public')->default(true)->comment('Показывать покупателю в ЛК');

            /* =========================================================
             * TYPE / STATUS
             * ========================================================= */

            $table->string('type', 32)
                ->comment('Тип: refund_statement/refund_act/fiscal_receipt/photo/other');

            $table->string('status', 32)
                ->default('ready')
                ->comment('Статус: ready/processing/failed/archived');

            /* =========================================================
             * UI / IDENTIFIERS
             * ========================================================= */

            $table->string('title', 255)->comment('Название документа для UI');
            $table->string('number', 64)->nullable()->comment('Номер документа (если есть)');
            $table->string('locale', 10)->nullable()->comment('Локаль документа');

            /* =========================================================
             * FILE / STORAGE
             * ========================================================= */

            $table->string('file_url', 2048)->nullable()->comment('Прямая ссылка на документ (если хранится внешне)');
            $table->string('storage_disk', 64)->nullable()->comment('Диск хранения (public/s3/...)');
            $table->string('storage_path', 1024)->nullable()->comment('Путь к файлу в хранилище');

            $table->string('mime', 128)->nullable()->comment('MIME тип файла');
            $table->unsignedBigInteger('size_bytes')->nullable()->comment('Размер файла (байты)');
            $table->string('sha256', 64)->nullable()->comment('Хеш файла (sha256)');

            /* =========================================================
             * META / NOTES
             * ========================================================= */

            $table->string('note', 255)->nullable()->comment('Комментарий/заметка');
            $table->json('meta')->nullable()->comment('Доп. данные (qr, ссылки проверки, провайдеры, ошибки)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mrd_tenant_time');
            $table->index(['company_id', 'storefront_id', 'refund_id', 'activity', 'sort'], 'ix_mrd_refund_list');

            $table->index(['order_id'], 'ix_mrd_order');
            $table->index(['fiscal_receipt_id'], 'ix_mrd_fiscal_receipt');

            $table->index(['type', 'status'], 'ix_mrd_type_status');
            $table->index(['is_public', 'activity'], 'ix_mrd_public_active');

            $table->comment('Маркет: документы возврата (PDF/URL/фото/акты/чеки), tenant-safe, OZON-подход');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_refund_documents');
    }
};
