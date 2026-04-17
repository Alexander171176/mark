<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_returns', function (Blueprint $table) {
            $table->id()->comment('ID возврата товара (логистика)');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_mret_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * ORDER LINK (tenant-safe)
             * Требование: в market_orders есть uq на (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->comment('Заказ (market_orders.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mret_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            // Покупатель (если у заказа есть user_id — удобно дублировать)
            $table->foreignId('customer_user_id')
                ->nullable()
                ->comment('Покупатель (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * RETURN STATUS (tenant-safe)
             * Требование: в market_return_statuses есть unique на (company_id, storefront_id, id)
             * или хотя бы unique/index покрывающий (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('status_id')
                ->nullable()
                ->comment('Статус возврата (market_return_statuses.id), tenant-safe');

            $table->foreign(['company_id', 'storefront_id', 'status_id'], 'fk_mret_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_return_statuses')
                ->restrictOnDelete(); // ✅

            // Денормализация (быстрые фильтры без join)
            $table->string('status_code', 64)->default('created')
                ->comment('Статус-код (копия code из status)');

            $table->timestamp('status_updated_at')->nullable()
                ->comment('Когда обновился статус');

            /* =========================================================
             * METHOD / ROUTING (как маркетплейсы: курьер/ПВЗ/почта)
             * ========================================================= */

            $table->string('return_method', 32)->default('pickup')
                ->comment('Способ возврата: pickup/pickup_point/post/courier');

            $table->foreignId('pickup_point_id')
                ->nullable()
                ->comment('ПВЗ для возврата (market_pickup_points.id), опционально')
                ->constrained('market_pickup_points')
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->comment('Склад приёмки возврата (market_warehouses.id), опционально')
                ->constrained('market_warehouses')
                ->nullOnDelete();

            /* =========================================================
             * DELIVERY / TRACKING
             * ========================================================= */

            $table->foreignId('delivery_provider_id')
                ->nullable()
                ->comment('Провайдер доставки (market_delivery_providers.id), опционально')
                ->constrained('market_delivery_providers')
                ->nullOnDelete();

            $table->string('tracking_number', 128)->nullable()->comment('Трек-номер');
            $table->string('tracking_url', 2048)->nullable()->comment('Ссылка на трекинг');

            /* =========================================================
             * REASON / COMMENTS
             * ========================================================= */

            $table->string('reason_code', 64)->nullable()
                ->comment('Причина: defect/not_like/wrong_item/delivery_issue/...');

            $table->string('reason_text', 255)->nullable()
                ->comment('Комментарий причины');

            $table->text('customer_comment')->nullable()->comment('Комментарий покупателя');
            $table->string('admin_note', 255)->nullable()->comment('Заметка админа');

            /* =========================================================
             * AMOUNTS (витрина ожиданий; деньги живут в market_refunds)
             * ========================================================= */

            $table->decimal('estimated_refund_amount', 18, 2)->nullable()
                ->comment('Ожидаемая сумма возврата (оценка)');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта оценки (currencies.id)')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * LINK TO MONEY REFUND (tenant-safe, optional)
             * Один return может породить несколько refunds — тут ссылка опционально на "основной/первый"
             * ========================================================= */

            $table->unsignedBigInteger('refund_id')
                ->nullable()
                ->comment('Денежный возврат (market_refunds.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'refund_id'], 'fk_mret_refund_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_refunds')
                ->restrictOnDelete();

            /* =========================================================
             * DATES
             * ========================================================= */

            $table->timestamp('approved_at')->nullable()->comment('Когда одобрен');
            $table->timestamp('rejected_at')->nullable()->comment('Когда отклонён');
            $table->timestamp('received_at')->nullable()->comment('Когда получен складом/ПВЗ');
            $table->timestamp('closed_at')->nullable()->comment('Когда закрыт');

            /* =========================================================
             * ACTORS
             * ========================================================= */

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('processed_by_user_id')
                ->nullable()
                ->comment('Кто обработал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * FLAGS / META
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность заявки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->json('meta')->nullable()->comment('Доп. данные (адрес забора, фото, внутренние флаги)');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mrs_tenant_id');

            // Если тебе реально нужен tenant-safe FK на returns из других таблиц — оставь.
            // Если нет — можно удалить (id и так уникален).
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_returns_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mret_tenant');

            $table->index(['company_id', 'storefront_id', 'order_id', 'created_at'], 'ix_mret_order_time');

            $table->index(['company_id', 'storefront_id', 'status_code', 'created_at'], 'ix_mret_status_time');
            $table->index(['company_id', 'storefront_id', 'status_id', 'created_at'], 'ix_mret_status_id_time');

            $table->index(['tracking_number'], 'ix_mret_tracking');
            $table->index(['pickup_point_id'], 'ix_mret_pickup_point');
            $table->index(['warehouse_id'], 'ix_mret_warehouse');

            $table->index(['refund_id'], 'ix_mret_refund');

            $table->comment('Маркет: возвраты товаров (логистика) — отдельно от возврата денег (refund), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_returns');
    }
};
