<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Экземпляры подписок пользователей
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $t) {
            $t->id();

            // Владелец
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Тарифный план
            $t->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            // Связки с комерцией
            $t->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete(); // заказ могут удалить/архивировать

            $t->foreignId('user_payment_method_id')
                ->nullable()
                ->constrained('user_payment_methods')
                ->nullOnDelete(); // пользователь мог отвязать способ оплаты

            // Снапшот биллинга (фиксируем стоимость на момент старта)
            $t->char('currency', 3)->default('USD');             // ISO 4217
            $t->decimal('price', 18, 2)->default(0);             // цена за период
            $t->enum('billing_period', ['day','week','month','year'])->default('month');
            $t->unsignedSmallInteger('interval')->default(1);    // шаг периода (1,3,12...)

            // Триал
            $t->unsignedSmallInteger('trial_days')->default(0);
            $t->timestamp('trial_ends_at')->nullable();

            // Текущий период
            $t->timestamp('current_period_start')->nullable();
            $t->timestamp('current_period_end')->nullable();

            // Даты ЖЦ
            $t->timestamp('started_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->boolean('cancel_at_period_end')->default(false);

            // Статус
            $t->string('status', 20)->default('active')
                ->comment('trialing|active|past_due|paused|cancelled|expired|incomplete|pending');

            // Интеграция с провайдером
            $t->string('provider', 64)->nullable();                  // stripe|paypal|yookassa|...
            $t->string('provider_subscription_id', 191)->nullable(); // ID у провайдера
            $t->timestamp('last_paid_at')->nullable();
            $t->timestamp('next_billing_at')->nullable();
            $t->unsignedSmallInteger('renewal_attempts')->default(0);

            // Доп. данные
            $t->json('features')->nullable(); // снапшот фич плана
            $t->json('limits')->nullable();   // снапшот лимитов
            $t->json('meta')->nullable();     // произвольные данные

            $t->timestamps();
            $t->softDeletes();

            // Индексы под частые выборки
            $t->index(['user_id', 'status'], 'idx_sub_user_status');
            $t->index(['status', 'next_billing_at'], 'idx_sub_status_nextbill');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
