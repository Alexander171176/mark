<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Позиции выплаты (расшифровка по заказам/товарам)
    public function up(): void
    {
        Schema::create('payout_items', function (Blueprint $t) {
            $t->id();

            // К какой выплате относится позиция
            $t->foreignId('payout_id')
                ->constrained('payouts')
                ->cascadeOnDelete();

            // Источник дохода (всё опционально — фиксируем то, что известно)
            $t->foreignId('order_id')->nullable()
                ->constrained('orders')->nullOnDelete();          // исходный заказ
            $t->foreignId('order_item_id')->nullable()
                ->constrained('order_items')->nullOnDelete();     // конкретная позиция заказа

            // Нормализованные ссылки на продаваемые сущности
            $t->foreignId('course_id')->nullable()
                ->constrained('courses')->nullOnDelete();
            $t->foreignId('bundle_id')->nullable()
                ->constrained('bundles')->nullOnDelete();
            $t->foreignId('subscription_id')->nullable()
                ->constrained('subscriptions')->nullOnDelete();

            // Денежные поля (в валюте выплаты или источника)
            $t->char('currency', 3)->default('USD');             // ISO 4217
            $t->decimal('amount_gross', 18, 2)->default(0)       // начислено по позиции (до удержаний)
            ->comment('Начислено брутто за позицию');
            $t->decimal('fee_total', 18, 2)->default(0)          // комиссии провайдера/платёжки
            ->comment('Комиссии');
            $t->decimal('tax_total', 18, 2)->default(0)          // удержанные налоги
            ->comment('Налоги/удержания');
            $t->decimal('amount_net', 18, 2)->default(0)         // к выплате по позиции
            ->comment('Нетто (к выплате)');

            // Когда доход был “заработан” (момент продажи/завершения периода)
            $t->timestamp('earned_at')->nullable();

            // Свободные поля
            $t->string('title')->nullable()->comment('Подпись позиции для актов/отчётов');
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();

            // Частые фильтры/поиски
            $t->index(['payout_id', 'order_id'], 'idx_payout_order');
            $t->index(['course_id', 'bundle_id', 'subscription_id'], 'idx_item_objects');
            $t->index(['earned_at'], 'idx_earned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_items');
    }
};
