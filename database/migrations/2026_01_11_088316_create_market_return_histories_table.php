<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_return_histories', function (Blueprint $table) {
            $table->id()->comment('ID записи истории возврата товара');

            /* =========================================================
             * TENANT / STOREFRONT (tenant-safe)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mrh_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * RETURN LINK (tenant-safe)
             * Требование: в market_returns есть uq (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('return_id')
                ->comment('Возврат товара (market_returns.id)');

            $table->foreign(['company_id', 'storefront_id', 'return_id'], 'fk_mrh_return_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_returns')
                ->cascadeOnDelete();

            /* =========================================================
             * STATUS CHANGE (tenant-safe, optional)
             * ВАЖНО: нельзя SET NULL на композитном FK, т.к. company_id/storefront_id NOT NULL
             * Поэтому: RESTRICT (или CASCADE если хочешь удалять историю вместе со статусом)
             * ========================================================= */

            $table->unsignedBigInteger('from_status_id')
                ->nullable()
                ->comment('Статус ДО (market_return_statuses.id), tenant-safe');

            $table->unsignedBigInteger('to_status_id')
                ->nullable()
                ->comment('Статус ПОСЛЕ (market_return_statuses.id), tenant-safe');

            // ✅ tenant-safe через (company_id, storefront_id, id)
            $table->foreign(['company_id', 'storefront_id', 'from_status_id'], 'fk_mrh_from_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_return_statuses')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'to_status_id'], 'fk_mrh_to_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_return_statuses')
                ->restrictOnDelete();

            // Денормализация для UI/быстрых фильтров
            $table->string('from_status_code', 64)->nullable()->comment('Код статуса ДО (snapshot)');
            $table->string('to_status_code', 64)->nullable()->comment('Код статуса ПОСЛЕ (snapshot)');

            /* =========================================================
             * EVENT
             * ========================================================= */

            $table->string('event', 64)->default('status_change')
                ->comment('Событие: created/status_change/comment/provider_webhook/system');

            $table->string('source', 32)->default('system')
                ->comment('Источник: customer/admin/system/provider');

            $table->boolean('is_public')->default(false)
                ->comment('Показывать событие покупателю в ЛК');

            $table->unsignedInteger('sort')->default(0)
                ->comment('Сортировка (если нужно вручную упорядочивать)');

            /* =========================================================
             * MESSAGE / PAYLOAD
             * ========================================================= */

            $table->string('title', 255)->nullable()->comment('Заголовок события для UI');
            $table->text('message')->nullable()->comment('Сообщение/комментарий');
            $table->json('payload')->nullable()->comment('Данные события (webhook/ответы/параметры)');

            /* =========================================================
             * ACTOR
             * ========================================================= */

            $table->string('actor_type', 32)->default('system')
                ->comment('Кто инициатор: system/user/admin/provider');

            $table->foreignId('actor_user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если событие от человека')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('actor_name', 255)->nullable()->comment('Имя инициатора (если надо показать)');

            /* =========================================================
             * TECH
             * ========================================================= */

            $table->string('ip', 64)->nullable()->comment('IP инициатора (опционально)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent (опционально)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mrh_tenant');

            $table->index(['company_id', 'storefront_id', 'return_id', 'created_at'], 'ix_mrh_return_timeline');
            $table->index(['company_id', 'storefront_id', 'event', 'created_at'], 'ix_mrh_event_timeline');
            $table->index(['company_id', 'storefront_id', 'return_id', 'is_public', 'created_at'], 'ix_mrh_return_public_timeline');

            // ✅ индексы под tenant-safe статусы
            $table->index(['company_id', 'storefront_id', 'from_status_id'], 'ix_mrh_from_status');
            $table->index(['company_id', 'storefront_id', 'to_status_id'], 'ix_mrh_to_status');

            $table->comment('Маркет: история возврата товара (audit log, события/смена статусов), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_return_histories');
    }
};
