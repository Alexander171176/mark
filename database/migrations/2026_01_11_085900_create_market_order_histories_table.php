<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_order_histories', function (Blueprint $table) {
            $table->id()->comment('ID записи истории заказа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_moh_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * ORDER LINK (tenant-safe)
             * Требование: в market_orders должен быть UNIQUE/INDEX (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->comment('Заказ (market_orders.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_moh_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->cascadeOnDelete();

            /* =========================================================
             * EVENT
             * ========================================================= */

            $table->string('event', 64)
                ->comment('Тип события: created/status_changed/paid/cancelled/comment/delivery_updated/...');

            $table->boolean('is_public')->default(false)
                ->comment('Показывать событие покупателю в ЛК (true/false)');

            $table->unsignedInteger('sort')->default(0)
                ->comment('Сортировка (если нужно вручную упорядочивать отдельные события)');

            /* =========================================================
             * STATUS CHANGE (tenant-safe optional)
             * Требование: в market_order_statuses должен быть UNIQUE/INDEX (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('from_status_id')->nullable()
                ->comment('Статус ДО (market_order_statuses.id)');

            $table->unsignedBigInteger('to_status_id')->nullable()
                ->comment('Статус ПОСЛЕ (market_order_statuses.id)');

            $table->foreign(['company_id', 'storefront_id', 'from_status_id'], 'fk_moh_from_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_statuses')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'to_status_id'], 'fk_moh_to_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_statuses')
                ->restrictOnDelete();

            /* =========================================================
             * ACTOR
             * ========================================================= */

            $table->string('actor_type', 32)->default('system')
                ->comment('Кто инициатор: system/user/admin/guest/provider');

            $table->foreignId('actor_user_id')
                ->nullable()
                ->comment('Пользователь-инициатор (users.id), если actor_type=user/admin')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('actor_name', 255)->nullable()
                ->comment('Имя инициатора (если нужно показать в логе)');

            $table->string('source', 32)->nullable()
                ->comment('Источник: api/admin/webhook/cron/frontend');

            /* =========================================================
             * MESSAGE / PAYLOAD
             * ========================================================= */

            $table->string('title', 255)->nullable()
                ->comment('Короткий заголовок события (для UI)');

            $table->text('message')->nullable()
                ->comment('Текст события (для UI/уведомлений)');

            $table->json('payload')->nullable()
                ->comment('JSON-данные события (что изменилось, параметры, ответы провайдера и т.п.)');

            /* =========================================================
             * TECH
             * ========================================================= */

            $table->string('ip', 64)->nullable()->comment('IP инициатора (опционально)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent (опционально)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_moh_tenant');

            // Быстрый вывод истории заказа
            $table->index(['company_id', 'storefront_id', 'order_id', 'created_at'], 'ix_moh_order_timeline');

            // Поиск событий по типу
            $table->index(['company_id', 'storefront_id', 'event', 'created_at'], 'ix_moh_event_timeline');

            // Фильтр для ЛК
            $table->index(['company_id', 'storefront_id', 'order_id', 'is_public', 'created_at'], 'ix_moh_order_public_timeline');

            // Для аналитики статусов
            $table->index(['company_id', 'storefront_id', 'to_status_id', 'created_at'], 'ix_moh_to_status_timeline');

            $table->comment('Маркет: история/события заказа (event-log), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_histories');
    }
};
