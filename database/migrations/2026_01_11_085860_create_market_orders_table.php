<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_orders', function (Blueprint $table) {
            $table->id()->comment('ID заказа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_orders_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * OWNER
             * ========================================================= */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('locale', 10)->nullable()->comment('Локаль заказа (ru/kk/en/...)');

            /* =========================================================
             * IDENTIFIERS
             * ========================================================= */

            $table->string('order_number', 191)->comment('Номер заказа (уникален в рамках витрины)');
            $table->string('public_token', 64)->nullable()->comment('Публичный токен для гостевого доступа/ссылок');

            /* =========================================================
             * ORDER LIFECYCLE
             * ========================================================= */

            $table->timestamp('submitted_at')->nullable()->comment('Когда заказ оформлен/отправлен');
            $table->timestamp('paid_at')->nullable()->comment('Когда заказ оплачен');
            $table->timestamp('completed_at')->nullable()->comment('Когда заказ завершён');
            $table->timestamp('cancelled_at')->nullable()->comment('Когда заказ отменён');

            /* =========================================================
             * BUSINESS STATUS (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('order_status_id')
                ->nullable()
                ->comment('Статус заказа (market_order_statuses.id)');

            /**
             * ✅ tenant-safe:
             * (company_id, storefront_id, order_status_id) -> market_order_statuses(company_id, storefront_id, id)
             * Требование выполнено, потому что в market_order_statuses есть uq_mos_tenant_id.
             */
            $table->foreign(['company_id', 'storefront_id', 'order_status_id'], 'fk_market_orders_status_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_statuses')
                ->restrictOnDelete();

            /* =========================================================
             * CUSTOMER SNAPSHOT
             * ========================================================= */

            $table->string('customer_type', 16)->default('individual')->comment('individual / legal');

            $table->string('customer_phone', 50)->nullable()->comment('Телефон покупателя (снапшот)');
            $table->string('customer_email', 255)->nullable()->comment('Email покупателя (снапшот)');

            $table->string('customer_messenger_type', 32)->nullable()->comment('whatsapp/telegram/... (снапшот)');
            $table->string('customer_messenger_contact', 255)->nullable()->comment('Контакт мессенджера (снапшот)');

            $table->string('customer_first_name', 255)->nullable()->comment('Имя (снапшот)');
            $table->string('customer_last_name', 255)->nullable()->comment('Фамилия (снапшот)');

            $table->string('legal_name', 255)->nullable()->comment('Юр. лицо: наименование (снапшот)');
            $table->string('legal_bin', 12)->nullable()->comment('Юр. лицо: БИН (KZ)');
            $table->string('legal_contact_name', 255)->nullable()->comment('Юр. лицо: контактное лицо');

            $table->string('legal_country_code', 2)->nullable()->comment('Юр. адрес: ISO2');
            $table->string('legal_region', 128)->nullable()->comment('Юр. адрес: регион');
            $table->string('legal_city', 128)->nullable()->comment('Юр. адрес: город');
            $table->string('legal_district', 128)->nullable()->comment('Юр. адрес: район');
            $table->string('legal_postcode', 16)->nullable()->comment('Юр. адрес: индекс');
            $table->text('legal_address')->nullable()->comment('Юр. адрес: строкой');

            $table->string('legal_iik', 34)->nullable()->comment('ИИК/IBAN (опционально)');
            $table->string('legal_bank_name', 255)->nullable()->comment('Банк (опционально)');
            $table->string('legal_bik', 11)->nullable()->comment('БИК (опционально)');

            /* =========================================================
             * DELIVERY SNAPSHOT
             * ========================================================= */

            $table->string('delivery_type', 32)->default('courier')->comment('courier/pickup/post/digital');

            $table->foreignId('delivery_method_id')
                ->nullable()
                ->comment('Способ доставки (market_delivery_methods.id)')
                ->constrained('market_delivery_methods')
                ->nullOnDelete();

            $table->foreignId('delivery_zone_id')
                ->nullable()
                ->comment('Зона доставки (market_delivery_zones.id)')
                ->constrained('market_delivery_zones')
                ->nullOnDelete();

            $table->foreignId('pickup_point_id')
                ->nullable()
                ->comment('Пункт выдачи (market_pickup_points.id)')
                ->constrained('market_pickup_points')
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->comment('Склад отгрузки (market_warehouses.id)')
                ->constrained('market_warehouses')
                ->nullOnDelete();

            $table->string('ship_country_code', 2)->nullable()->comment('Доставка: ISO2');
            $table->string('ship_region', 128)->nullable()->comment('Доставка: регион');
            $table->string('ship_city', 128)->nullable()->comment('Доставка: город');
            $table->string('ship_district', 128)->nullable()->comment('Доставка: район');
            $table->string('ship_postcode', 16)->nullable()->comment('Доставка: индекс');
            $table->text('ship_address')->nullable()->comment('Доставка: адрес строкой');
            $table->string('ship_address_note', 255)->nullable()->comment('Доставка: примечание');

            $table->decimal('ship_lat', 10, 7)->nullable()->comment('Доставка: широта');
            $table->decimal('ship_lng', 10, 7)->nullable()->comment('Доставка: долгота');

            $table->text('customer_comment')->nullable()->comment('Комментарий покупателя');

            /* =========================================================
             * TOTALS / CURRENCY
             * ========================================================= */

            $table->foreignId('currency_id')
                ->comment('Валюта заказа (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->decimal('items_subtotal', 18, 2)->default(0)->comment('Сумма товаров');
            $table->decimal('discount_total', 18, 2)->default(0)->comment('Скидки');
            $table->decimal('delivery_total', 18, 2)->default(0)->comment('Доставка');
            $table->decimal('tax_total', 18, 2)->default(0)->comment('Налоги/сборы');
            $table->decimal('grand_total', 18, 2)->default(0)->comment('Итого');

            /* =========================================================
             * BONUSES SNAPSHOT
             * ========================================================= */

            $table->decimal('bonus_spent', 18, 2)->default(0)->comment('Списано бонусов (итог)');
            $table->decimal('bonus_earned', 18, 2)->default(0)->comment('Начислим бонусов (итог)');
            $table->decimal('bonus_hold', 18, 2)->default(0)->comment('Hold бонусов');

            $table->foreignId('bonus_currency_id')->nullable()
                ->comment('Валюта бонусов (currencies.id), если бонусы денежные')
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('bonus_program_id')->nullable()
                ->comment('Бонусная программа (market_bonus_programs.id)')
                ->constrained('market_bonus_programs')
                ->nullOnDelete();

            $table->foreignId('bonus_spend_operation_id')->nullable()
                ->comment('Операция списания (market_bonus_operations.id)')
                ->constrained('market_bonus_operations')
                ->nullOnDelete();

            $table->foreignId('bonus_earn_operation_id')->nullable()
                ->comment('Операция начисления (market_bonus_operations.id)')
                ->constrained('market_bonus_operations')
                ->nullOnDelete();

            $table->foreignId('bonus_hold_operation_id')->nullable()
                ->comment('Операция hold (market_bonus_operations.id)')
                ->constrained('market_bonus_operations')
                ->nullOnDelete();

            /* =========================================================
             * TECH / ANALYTICS
             * ========================================================= */

            $table->string('status', 32)->default('draft')
                ->comment('Тех. статус: draft/submitted/locked/cancelled (не путать с order_status_id)');

            $table->string('ip', 64)->nullable()->comment('IP при оформлении');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ tenant-safe ключ для композитных ссылок на заказ из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_orders_tenant_id');

            $table->unique(['storefront_id', 'order_number'], 'uq_market_orders_storefront_number');

            $table->index(['storefront_id', 'public_token'], 'ix_market_orders_storefront_public_token');

            $table->index(['company_id', 'storefront_id'], 'ix_market_orders_tenant');

            $table->index(['storefront_id', 'created_at'], 'ix_market_orders_storefront_created');
            $table->index(['storefront_id', 'paid_at'], 'ix_market_orders_storefront_paid_at');

            $table->index(['storefront_id', 'order_status_id', 'created_at'], 'ix_market_orders_storefront_status_created');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_market_orders_storefront_tech_status_created');

            $table->index(['storefront_id', 'customer_type', 'created_at'], 'ix_market_orders_storefront_customer_type_created');
            $table->index(['storefront_id', 'legal_bin'], 'ix_market_orders_storefront_legal_bin');

            $table->index(['storefront_id', 'delivery_type'], 'ix_market_orders_storefront_delivery_type');
            $table->index(['delivery_method_id'], 'ix_market_orders_delivery_method');
            $table->index(['pickup_point_id'], 'ix_market_orders_pickup_point');
            $table->index(['warehouse_id'], 'ix_market_orders_warehouse');

            $table->index(['storefront_id', 'grand_total'], 'ix_market_orders_storefront_total');

            $table->index(['bonus_program_id'], 'ix_market_orders_bonus_program');
            $table->index(['storefront_id', 'bonus_spent'], 'ix_market_orders_storefront_bonus_spent');
            $table->index(['storefront_id', 'bonus_earned'], 'ix_market_orders_storefront_bonus_earned');

            $table->comment('Маркет: заказы (tenant-safe), снапшоты покупателя/доставки/сумм на момент оформления');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_orders');
    }
};
