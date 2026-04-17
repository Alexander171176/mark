<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Платёж по заказу (может быть несколько: ретраи, доплаты и т.п.)
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();

            // Заказ, к которому относится платёж
            $t->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Категория / тип способа оплаты (справочник)
            $t->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            // (Опционально) конкретный сохранённый метод пользователя
            $t->foreignId('user_payment_method_id')
                ->nullable()
                ->constrained('user_payment_methods')
                ->nullOnDelete();

            // Данные провайдера (шлюза)
            $t->string('provider', 64)->nullable();             // stripe|yookassa|paypal|...
            $t->string('provider_payment_id', 191)->nullable();  // id платежа на стороне провайдера
            $t->string('idempotency_key', 191)->nullable();      // ключ идемпотентности для безопасных ретраев

            // Статус платежа
            $t->string('status', 32)
                ->default('pending')
                ->comment('pending|processing|succeeded|failed|canceled|refunded|partially_refunded');

            // Сумма и валюта
            $t->char('currency', 3)->default('USD');            // ISO 4217
            $t->decimal('amount', 18, 2);                       // сумма платежа (в валюте)

            // Служебные отметки и детали результата
            $t->timestamp('captured_at')->nullable();           // когда захвачен/подтверждён
            $t->timestamp('refunded_at')->nullable();           // если полностью возвращён
            $t->decimal('refunded_amount', 18, 2)->nullable();  // сумма возврата (для частичных)
            $t->string('error_code', 64)->nullable();           // код ошибки от провайдера
            $t->text('error_message')->nullable();              // описание ошибки
            $t->json('meta')->nullable();                       // произвольные поля провайдера

            $t->timestamps();
            $t->softDeletes();

            // Индексы
            $t->index(['order_id', 'status'], 'idx_payments_order_status');
            $t->index(['provider', 'provider_payment_id'], 'idx_payments_provider_pid');
            $t->unique(['provider', 'provider_payment_id'], 'uniq_provider_payment_id'); // защита от дублей
            $t->unique('idempotency_key'); // по желанию, для безопасных повт. запросов
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
