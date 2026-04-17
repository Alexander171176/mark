<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_tickets', function (Blueprint $table) {
            $table->id()->comment('ID тикета (обращение в поддержку)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mt_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * AUTHOR
             * ========================================================= */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('locale', 10)->nullable()->comment('Локаль тикета (ru/kk/en/...)');

            /* =========================================================
             * IDENTIFIERS
             * ========================================================= */

            $table->string('ticket_number', 64)->comment('Номер тикета (уникален в рамках витрины)');
            $table->string('public_token', 64)->nullable()->comment('Публичный токен для гостевого доступа');

            /* =========================================================
             * STATUS (tenant-safe by storefront)
             * ========================================================= */

            $table->unsignedBigInteger('ticket_status_id')
                ->nullable()
                ->comment('Статус тикета (market_ticket_statuses.id), tenant-safe');

            // tenant-safe: статус должен принадлежать этой витрине
            $table->foreign(['storefront_id', 'ticket_status_id'], 'fk_mt_storefront_status')
                ->references(['storefront_id', 'id'])
                ->on('market_ticket_statuses')
                ->restrictOnDelete();

            /* =========================================================
             * TOPIC / SUBJECT
             * ========================================================= */

            $table->string('topic', 128)->nullable()->comment('Тема/категория обращения (shipping/payment/return/...)');
            $table->string('subject', 255)->comment('Тема обращения (заголовок)');

            /* =========================================================
             * CONTACT SNAPSHOT
             * ========================================================= */

            $table->string('customer_name', 255)->nullable()->comment('Имя/ФИО отправителя (снапшот)');
            $table->string('customer_phone', 50)->nullable()->comment('Телефон (снапшот)');
            $table->string('customer_email', 255)->nullable()->comment('Email (снапшот)');

            $table->string('customer_messenger_type', 32)->nullable()->comment('Мессенджер: whatsapp/telegram/...');
            $table->string('customer_messenger_contact', 255)->nullable()->comment('Контакт мессенджера');

            /* =========================================================
             * LINKS (tenant-safe, optional)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), если обращение по заказу');

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), если обращение по товару');

            /**
             * ⚠️ Нельзя nullOnDelete() на композитном FK, где company_id/storefront_id NOT NULL
             * Поэтому: RESTRICT (или CASCADE по желанию, но обычно не нужно).
             */
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mt_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'product_id'], 'fk_mt_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->restrictOnDelete();

            /* =========================================================
             * PRIORITY / FLAGS
             * ========================================================= */

            $table->string('priority', 16)->default('normal')->comment('Приоритет: low/normal/high/urgent');
            $table->boolean('is_read_by_support')->default(false)->comment('Прочитано поддержкой');
            $table->boolean('is_read_by_customer')->default(true)->comment('Прочитано клиентом');

            /* =========================================================
             * ASSIGNEE
             * ========================================================= */

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->comment('Кому назначен тикет (users.id), оператор/менеджер')
                ->constrained('users')
                ->nullOnDelete();

            $table->json('meta')->nullable()->comment('Доп. данные (канал/utm/интеграции)');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique(['storefront_id', 'ticket_number'], 'uq_mt_storefront_number');

            // ✅ критично для будущих tenant-safe FK на tickets
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mt_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mt_tenant');
            $table->index(['storefront_id', 'ticket_status_id', 'created_at'], 'ix_mt_status_time');
            $table->index(['storefront_id', 'user_id', 'created_at'], 'ix_mt_user_time');
            $table->index(['storefront_id', 'assigned_to_user_id', 'created_at'], 'ix_mt_assignee_time');
            $table->index(['company_id', 'storefront_id', 'order_id'], 'ix_mt_order');
            $table->index(['company_id', 'storefront_id', 'product_id'], 'ix_mt_product');
            $table->index(['storefront_id', 'priority', 'created_at'], 'ix_mt_priority_time');

            $table->comment('Маркет: тикеты поддержки витрины, tenant-safe, с привязкой к заказу/товару.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_tickets');
    }
};
