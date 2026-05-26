<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_payments', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_order_id')
                ->constrained('school_orders')
                ->cascadeOnDelete()
                ->comment('Заказ онлайн-школы');

            $t->foreignId('school_payment_method_id')
                ->nullable()
                ->constrained('school_payment_methods')
                ->nullOnDelete()
                ->comment('Способ оплаты');

            $t->foreignId('school_user_payment_method_id')
                ->nullable()
                ->constrained('school_user_payment_methods')
                ->nullOnDelete()
                ->comment('Сохранённый способ оплаты пользователя');

            $t->string('provider', 64)->nullable()->comment('Провайдер');
            $t->string('provider_payment_id', 191)->nullable()->comment('ID платежа у провайдера');
            $t->string('idempotency_key', 191)->nullable()->comment('Ключ идемпотентности');

            $t->string('status', 32)
                ->default('pending')
                ->comment('pending|processing|succeeded|failed|canceled|refunded|partially_refunded');

            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('amount', 18, 2)->comment('Сумма платежа');

            $t->timestamp('captured_at')->nullable()->comment('Дата подтверждения платежа');
            $t->timestamp('refunded_at')->nullable()->comment('Дата возврата');
            $t->decimal('refunded_amount', 18, 2)->nullable()->comment('Сумма возврата');

            $t->string('error_code', 64)->nullable()->comment('Код ошибки');
            $t->text('error_message')->nullable()->comment('Описание ошибки');
            $t->json('meta')->nullable()->comment('Данные провайдера');

            $t->timestamps();

            $t->index(['school_order_id', 'status'], 'idx_school_payment_order_status');
            $t->index(['provider', 'provider_payment_id'], 'idx_school_payment_provider_pid');
            $t->unique(['provider', 'provider_payment_id'], 'uq_school_provider_payment_id');
            $t->unique('idempotency_key', 'uq_school_payment_idempotency_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_payments');
    }
};
