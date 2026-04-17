<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_return_documents', function (Blueprint $table) {
            $table->id()->comment('ID документа возврата товара (логистика)');

            /* =========================================================
             * TENANT / STOREFRONT (tenant-safe)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mretd_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            // Основная связь — возврат товара (обычно обязателен)
            $table->unsignedBigInteger('return_id')
                ->comment('Возврат товара (market_returns.id)');

            $table->foreign(['company_id', 'storefront_id', 'return_id'], 'fk_mretd_return_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_returns')
                ->cascadeOnDelete();

            // Для удобства вывода рядом с заказом (опционально)
            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), опционально');

            // ❗ НЕЛЬЗЯ nullOnDelete на композитном FK где company_id/storefront_id NOT NULL
            // Выбираем безопасный вариант: RESTRICT (документы — аудит)
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mretd_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            // Основной/первичный денежный возврат (опционально)
            $table->unsignedBigInteger('refund_id')
                ->nullable()
                ->comment('Денежный возврат (market_refunds.id), опционально');

            // Аналогично: только RESTRICT или CASCADE
            $table->foreign(['company_id', 'storefront_id', 'refund_id'], 'fk_mretd_refund_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_refunds')
                ->restrictOnDelete();

            // Фискальный чек (sale/refund) — если документы завязаны на фискализацию
            $table->foreignId('fiscal_receipt_id')
                ->nullable()
                ->comment('Фискальный чек (market_fiscal_receipts.id), опционально')
                ->constrained('market_fiscal_receipts')
                ->nullOnDelete(); // тут FK ОДНОКОЛОНОЧНЫЙ -> nullOnDelete можно

            /* =========================================================
             * FLAGS / VISIBILITY
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность документа');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');
            $table->boolean('is_public')->default(true)->comment('Показывать покупателю в ЛК');

            /* =========================================================
             * TYPE / STATUS
             * ========================================================= */

            $table->string('type', 32)->comment(
                'Тип: return_statement/return_act/label/waybill/photo/video/inspection_report/other'
            );

            $table->string('status', 32)->default('ready')
                ->comment('Статус: ready/processing/failed/archived');

            /* =========================================================
             * UI / IDENTIFIERS
             * ========================================================= */

            $table->string('title', 255)->comment('Название документа для UI');
            $table->string('number', 64)->nullable()->comment('Номер документа (если есть)');
            $table->string('locale', 10)->nullable()->comment('Локаль документа (ru/kk/en)');

            /* =========================================================
             * FILE / STORAGE
             * ========================================================= */

            $table->string('file_url', 2048)->nullable()->comment('Прямая ссылка на документ (если внешнее хранение)');
            $table->string('storage_disk', 64)->nullable()->comment('Диск хранения (public/s3/...)');
            $table->string('storage_path', 1024)->nullable()->comment('Путь к файлу в хранилище');

            $table->string('mime', 128)->nullable()->comment('MIME тип файла');
            $table->unsignedBigInteger('size_bytes')->nullable()->comment('Размер файла (байты)');
            $table->string('sha256', 64)->nullable()->comment('Хеш файла (sha256)');

            /* =========================================================
             * META / NOTES
             * ========================================================= */

            $table->string('note', 255)->nullable()->comment('Комментарий/заметка');
            $table->json('meta')->nullable()->comment('Доп. данные (qr, ссылки проверки, провайдеры, ошибки, теги)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id', 'created_at'], 'ix_mretd_tenant_time');
            $table->index(['company_id', 'storefront_id', 'return_id', 'activity', 'sort'], 'ix_mretd_return_list');

            $table->index(['order_id'], 'ix_mretd_order');
            $table->index(['refund_id'], 'ix_mretd_refund');
            $table->index(['fiscal_receipt_id'], 'ix_mretd_fiscal_receipt');

            $table->index(['type', 'status'], 'ix_mretd_type_status');
            $table->index(['is_public', 'activity'], 'ix_mretd_public_active');

            $table->comment('Маркет: документы возврата товара (логистика) — PDF/URL/фото/акты/ярлыки, tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_return_documents');
    }
};
