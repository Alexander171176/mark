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
        Schema::create('school_subscriptions', function (Blueprint $t) {
            $t->id();

            // Владелец подписки
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Тарифный план
            $t->foreignId('school_subscription_plan_id')
                ->constrained('school_subscription_plans')
                ->cascadeOnDelete()
                ->comment('Тарифный план');

            // Связь с заказом
            $t->foreignId('school_order_id')
                ->nullable()
                ->constrained('school_orders')
                ->nullOnDelete()
                ->comment('Заказ');

            // Сохранённый способ оплаты
            $t->foreignId('school_user_payment_method_id')
                ->nullable()
                ->constrained('school_user_payment_methods')
                ->nullOnDelete()
                ->comment('Способ оплаты пользователя');

            // Снапшот биллинга
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('price', 18, 2)->default(0)->comment('Цена за период');

            $t->enum('billing_period', ['day', 'week', 'month', 'year'])
                ->default('month')
                ->comment('Период биллинга');

            $t->unsignedSmallInteger('interval')
                ->default(1)
                ->comment('Интервал периода');

            // Триал
            $t->unsignedSmallInteger('trial_days')->default(0)->comment('Дней пробного периода');
            $t->timestamp('trial_ends_at')->nullable()->comment('Окончание пробного периода');

            // Текущий период
            $t->timestamp('current_period_start')->nullable()->comment('Начало текущего периода');
            $t->timestamp('current_period_end')->nullable()->comment('Конец текущего периода');

            // Жизненный цикл
            $t->timestamp('started_at')->nullable()->comment('Дата начала подписки');
            $t->timestamp('ends_at')->nullable()->comment('Дата окончания подписки');
            $t->timestamp('cancelled_at')->nullable()->comment('Дата отмены');

            $t->boolean('cancel_at_period_end')
                ->default(false)
                ->comment('Отменить в конце периода');

            // Статус
            $t->string('status', 20)
                ->default('active')
                ->comment('trialing|active|past_due|paused|cancelled|expired|incomplete|pending');

            // Провайдер
            $t->string('provider', 64)->nullable()->comment('Провайдер оплаты');
            $t->string('provider_subscription_id', 191)->nullable()->comment('ID подписки у провайдера');

            $t->timestamp('last_paid_at')->nullable()->comment('Дата последней оплаты');
            $t->timestamp('next_billing_at')->nullable()->comment('Дата следующего списания');

            $t->unsignedSmallInteger('renewal_attempts')
                ->default(0)
                ->comment('Количество попыток продления');

            // Снапшоты условий тарифа
            $t->json('features')->nullable()->comment('Снапшот возможностей тарифа');
            $t->json('limits')->nullable()->comment('Снапшот лимитов тарифа');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Индексы
            $t->index(['user_id', 'status'], 'idx_school_sub_user_status');
            $t->index(['status', 'next_billing_at'], 'idx_school_sub_status_nextbill');
            $t->index(['provider', 'provider_subscription_id'], 'idx_school_sub_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscriptions');
    }
};
