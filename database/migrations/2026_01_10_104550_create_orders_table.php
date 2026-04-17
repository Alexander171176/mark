<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();

            /* ============================
             * МОДУЛЬ 1 — КТО ОФОРМИЛ ЗАКАЗ
             * ============================ */
            $t->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // гость тоже может оформить заказ

            $t->string('number', 32)->unique(); // человекочитаемый номер

            /* ============================
             * МОДУЛЬ 2 — ПОКУПАТЕЛЬ (ФИЗ/ЮР)
             * ============================ */
            $t->string('buyer_name')->nullable(); // Полное имя покупателя
            $t->string('buyer_email')->nullable();// email покупателя
            $t->string('buyer_phone')->nullable();// телефон покупателя

            // Юридические данные (для счетов/договоров)
            $t->string('billing_company')->nullable();// Полное имя Компании
            $t->string('billing_tax_id')->nullable(); // БИН/ИНН/VAT
            $t->string('billing_address')->nullable();// Юр.адрес

            /* ============================
             * МОДУЛЬ 3 — АДРЕС И ДОСТАВКА
             * ============================ */
            $t->string('shipping_address')->nullable(); // Полный адрес строкой
            $t->json('shipping_address_parts')->nullable(); // улица/дом/квартира/город
            $t->integer('delivery_method_id')->nullable(); // ID способа доставки
            $t->decimal('delivery_cost', 12, 2)->nullable(); // стоимость_доставки
            $t->json('delivery_options')->nullable(); // lift, door_to_door, etc.
            $t->string('delivery_interval')->nullable(); // 12:00–16:00
            $t->string('warehouse')->nullable(); // склад отгрузки
            $t->timestamp('delivery_date')->nullable(); // дата доставки (если есть)

            /* ============================
             * МОДУЛЬ 4 — ОПЛАТА
             * ============================ */
            $t->boolean('is_paid')->default(false); // флаг оплаты
            $t->timestamp('paid_at')->nullable(); // дата оплаты

            $t->integer('payment_method_id')->nullable(); // ID способа оплаты
            $t->string('payment_method', 32)->nullable(); // card|cash|bank_transfer
            $t->string('payment_provider', 32)->nullable(); // stripe|paybox|kaspi
            $t->string('payment_reference', 128)->nullable(); // ID транзакции
            $t->string('confirmation_code')->nullable(); // email/phone подтверждение
            $t->string('confirmation_status')->nullable(); // pending|confirmed|failed
            $t->string('failure_reason')->nullable(); // от платежного шлюза

            /* ============================
             * МОДУЛЬ 5 — МУЛЬТИВАЛЮТНОСТЬ
             * ============================ */
            $t->char('currency', 3)->default('USD'); // ISO 4217
            $t->decimal('subtotal', 12, 2)->default(0); // Сумма всех товаров в корзине без скидок, налогов и доставки
            $t->decimal('discount_total', 12, 2)->default(0); // общая сумма скидок
            $t->decimal('tax_total', 12, 2)->default(0); // сумма налогов (НДС / VAT)
            $t->decimal('total', 12, 2)->default(0); // окончательная сумма заказа
            $t->decimal('total_shop_currency', 12, 2)->nullable(); // итоговая сумма в валюте магазина
            $t->decimal('delivery_shop_currency', 12, 2)->nullable(); // стоимость доставки в валюте магазина

            /* ============================
             * МОДУЛЬ 6 — СТАТУСЫ
             * ============================ */
            // статус выполнения
            // new|processing|delivered|cancelled|refunded|completed
            $t->string('status', 32)->default('new');

            // статус оплаты (отдельно от статуса заказа)
            // pending|paid|failed|refunded|partial
            $t->string('payment_status', 32)->default('pending');

            /* ============================
             * МОДУЛЬ 7 — КОНТЕНТ ЗАКАЗА
             * ============================ */
            $t->json('items')->nullable(); // товары/курсы/услуги/комплекты
            $t->json('meta')->nullable();  // гибкие поля для разных типов

            /* ====================================
             * МОДУЛЬ 8 — КОММЕНТАРИИ
             * ===================================*/
            $t->text('user_comment')->nullable();   // от покупателя
            $t->text('manager_comment')->nullable();// от менеджера/админа

            /* ============================
             * МОДУЛЬ 9 — ИНТЕГРАЦИИ
             * ============================ */
            $t->string('external_id')->nullable(); // ID во внешних системах
            $t->timestamp('exported_at')->nullable(); // Дата последней выгрузки

            /* ============================
             * МОДУЛЬ 10 — ТЕХНИЧЕСКИЕ
             * ============================ */
            $t->string('client_ip', 45)->nullable(); // ip клиента
            $t->text('user_agent')->nullable(); // user agent устройства клиента
            $t->string('public_hash', 64)->nullable(); // для публичных ссылок на заказ

            $t->timestamps();
            $t->softDeletes();

            // Индексы
            $t->index(['user_id', 'status']);
            $t->index(['payment_status']);
            $t->index(['buyer_email']);
            $t->index(['external_id']);
            $t->index(['public_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
