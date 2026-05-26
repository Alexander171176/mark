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
        Schema::create('school_subscription_plans', function (Blueprint $t) {
            $t->id();

            // Управление
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(false)->comment('Активность тарифа');

            // Непереводимые поля
            $t->string('slug')->unique()->comment('Уникальный ЧПУ тарифа');

            // Публикация / доступность
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');
            $t->timestamp('available_from')->nullable()->comment('Доступен с');
            $t->timestamp('available_until')->nullable()->comment('Доступен до');

            // Биллинг
            $t->enum('billing_period', ['day', 'week', 'month', 'year'])
                ->default('month')
                ->comment('Период биллинга');

            $t->unsignedSmallInteger('interval')
                ->default(1)
                ->comment('Интервал периода');

            $t->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete()
                ->comment('Валюта');

            $t->decimal('price', 18, 2)
                ->default(0)
                ->comment('Цена тарифа');

            $t->unsignedSmallInteger('trial_days')
                ->default(0)
                ->comment('Пробный период в днях');

            $t->boolean('auto_renew')
                ->default(true)
                ->comment('Автопродление');

            // Провайдер оплаты
            $t->string('provider', 70)->nullable()->comment('Провайдер оплаты');
            $t->string('provider_ref')->nullable()->comment('ID тарифа у провайдера');
            $t->json('provider_payload')->nullable()->comment('Данные провайдера');

            // Конфиг тарифа
            $t->json('config')->nullable()->comment('Конфиг тарифа');

            $t->timestamps();

            // Индексы
            $t->index(['activity', 'sort'], 'idx_school_plan_active_sort');
            $t->index(['published_at', 'available_from', 'available_until'], 'idx_school_plan_publish_window');
            $t->index(['billing_period', 'interval'], 'idx_school_plan_period_interval');
            $t->index(['provider', 'provider_ref'], 'idx_school_plan_provider_ref');
            $t->index('currency_id', 'idx_school_plan_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscription_plans');
    }
};
