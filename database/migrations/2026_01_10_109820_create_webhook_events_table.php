<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Входящие события от платёжных/внешних провайдеров
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $t) {
            $t->id();

            // Идентификация источника/типа события
            $t->string('provider', 64);             // Stripe/PayPal/ЮKassa/CloudPayments/и т.п.
            $t->string('event_type', 128);          // напр. 'invoice.payment_succeeded'

            // Вспомогательные поля для идемпотентности/подписи
            $t->string('external_id', 191)->nullable();    // id события у провайдера (если есть)
            $t->string('idempotency_key', 191)->nullable();// для защиты от повторной обработки
            $t->string('signature', 255)->nullable();      // подпись провайдера, если используется

            // Связи на наши сущности (могут быть неизвестны на момент приёма)
            $t->foreignId('order_id')->nullable()
                ->constrained('orders')->nullOnDelete();
            $t->foreignId('payment_id')->nullable()
                ->constrained('payments')->nullOnDelete();
            $t->foreignId('subscription_id')->nullable()
                ->constrained('subscriptions')->nullOnDelete();

            // Сырые данные и заголовки — для отладки/повторной обработки
            $t->json('payload');                    // тело вебхука (обязательно)
            $t->json('headers')->nullable();        // HTTP‑заголовки

            // Статус жизненного цикла обработки
            $t->string('status', 32)->default('received')
                ->comment('received|processing|processed|failed|skipped');

            // Служебные поля
            $t->unsignedSmallInteger('attempts')->default(0);     // попытки обработки
            $t->text('error_message')->nullable();                // последняя ошибка (если была)
            $t->timestamp('delivered_at')->nullable();            // когда пришло от провайдера (по серверу)
            $t->timestamp('processed_at')->nullable();            // когда успешно обработали

            $t->timestamps();
            $t->softDeletes();

            // Индексы для быстрых выборок/повторов
            $t->index(['provider', 'event_type'], 'idx_provider_type');
            $t->index(['status', 'created_at'], 'idx_status_created');
            $t->index('external_id', 'idx_external_id');
            $t->unique(['provider', 'external_id'], 'uniq_provider_external')  // защитимся от дублей
            ->nullable(); // внешние ID бывают пустыми
            $t->unique('idempotency_key'); // если используем идемпотентность
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
