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
        Schema::create('school_webhook_events', function (Blueprint $t) {
            $t->id();

            // Источник события
            $t->string('provider', 64)->comment('Провайдер');
            $t->string('event_type', 128)->comment('Тип события');

            // Идемпотентность / подпись
            $t->string('external_id', 191)->nullable()->comment('ID события у провайдера');
            $t->string('idempotency_key', 191)->nullable()->comment('Ключ идемпотентности');
            $t->string('signature', 255)->nullable()->comment('Подпись вебхука');

            // Связи с сущностями школы
            $t->foreignId('school_order_id')
                ->nullable()
                ->constrained('school_orders')
                ->nullOnDelete()
                ->comment('Заказ');

            $t->foreignId('school_payment_id')
                ->nullable()
                ->constrained('school_payments')
                ->nullOnDelete()
                ->comment('Платёж');

            $t->foreignId('school_subscription_id')
                ->nullable()
                ->constrained('school_subscriptions')
                ->nullOnDelete()
                ->comment('Подписка');

            // Сырые данные
            $t->json('payload')->comment('Тело вебхука');
            $t->json('headers')->nullable()->comment('HTTP-заголовки');

            // Статус обработки
            $t->string('status', 32)
                ->default('received')
                ->comment('received|processing|processed|failed|skipped');

            // Попытки и ошибки
            $t->unsignedSmallInteger('attempts')->default(0)->comment('Количество попыток');
            $t->text('error_message')->nullable()->comment('Последняя ошибка');

            // Даты обработки
            $t->timestamp('delivered_at')->nullable()->comment('Дата получения вебхука');
            $t->timestamp('processed_at')->nullable()->comment('Дата обработки');

            $t->timestamps();

            // Индексы
            $t->index(['provider', 'event_type'], 'idx_school_webhook_provider_type');
            $t->index(['status', 'created_at'], 'idx_school_webhook_status_created');
            $t->index('external_id', 'idx_school_webhook_external_id');

            $t->unique(['provider', 'external_id'], 'uq_school_webhook_provider_external');
            $t->unique('idempotency_key', 'uq_school_webhook_idempotency_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_webhook_events');
    }
};
