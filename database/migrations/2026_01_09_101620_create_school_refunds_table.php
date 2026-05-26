<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Возврат средств по заказу (полный или частичный)
    public function up(): void
    {
        Schema::create('school_refunds', function (Blueprint $t) {
            $t->id();

            // Заказ онлайн-школы
            $t->foreignId('school_order_id')
                ->constrained('school_orders')
                ->cascadeOnDelete()
                ->comment('Заказ онлайн-школы');

            // Платёж (опционально)
            $t->foreignId('school_payment_id')
                ->nullable()
                ->constrained('school_payments')
                ->nullOnDelete()
                ->comment('Платёж, по которому возврат');

            // Провайдер
            $t->string('provider', 64)->nullable()->comment('Провайдер оплаты');
            $t->string('provider_refund_id', 191)->nullable()->comment('ID возврата у провайдера');

            // Статус
            $t->string('status', 32)
                ->default('processing')
                ->comment('requested|processing|succeeded|failed|canceled');

            // Сумма
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('amount', 18, 2)->comment('Сумма возврата');

            // Причина
            $t->string('reason', 191)->nullable()->comment('Причина возврата');
            $t->text('notes')->nullable()->comment('Заметки');
            $t->json('meta')->nullable()->comment('Доп. данные');

            // Даты
            $t->timestamp('requested_at')->nullable()->comment('Дата запроса возврата');
            $t->timestamp('processed_at')->nullable()->comment('Дата обработки');

            $t->timestamps();

            // Индексы
            $t->index(['school_order_id', 'status'], 'idx_school_refund_order_status');
            $t->index(['school_payment_id', 'status'], 'idx_school_refund_payment_status');
            $t->index(['provider', 'provider_refund_id'], 'idx_school_refund_provider_rid');

            $t->unique(
                ['provider', 'provider_refund_id'],
                'uq_school_provider_refund_id'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_refunds');
    }
};
