<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Возврат средств по заказу (полный или частичный)
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $t) {
            $t->id();

            // Заказ, по которому делается возврат
            $t->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Опционально: конкретный платёж, из которого делается возврат
            $t->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->nullOnDelete();

            // Данные провайдера (шлюза)
            $t->string('provider', 64)->nullable();              // stripe|yookassa|paypal|...
            $t->string('provider_refund_id', 191)->nullable();   // id возврата у провайдера

            // Статус возврата (без CHECK/ENUM — совместимо с MySQL)
            $t->string('status', 32)
                ->default('processing')
                ->comment('requested|processing|succeeded|failed|canceled');

            // Сумма возврата и валюта
            $t->char('currency', 3)->default('USD');            // ISO 4217
            $t->decimal('amount', 18, 2);                       // сумма, которую вернули/пытаемся вернуть

            // Причина и служебные поля
            $t->string('reason', 191)->nullable();              // причина (по кнопке/скрипту)
            $t->text('notes')->nullable();                      // заметки оператора/системы
            $t->json('meta')->nullable();                       // произвольные данные провайдера

            // Временные метки жизненного цикла
            $t->timestamp('requested_at')->nullable();          // когда запросили возврат
            $t->timestamp('processed_at')->nullable();          // когда завершился (успех/ошибка)

            $t->timestamps();
            $t->softDeletes();

            // Индексы/уникальности
            $t->index(['order_id', 'status'], 'idx_refunds_order_status');
            $t->index(['payment_id', 'status'], 'idx_refunds_payment_status');
            $t->index(['provider', 'provider_refund_id'], 'idx_refunds_provider_rid');
            $t->unique(['provider', 'provider_refund_id'], 'uniq_provider_refund_id'); // защита от дублей
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
