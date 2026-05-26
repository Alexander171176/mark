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
        Schema::create('school_payout_items', function (Blueprint $t) {
            $t->id();

            // Выплата
            $t->foreignId('school_payout_id')
                ->constrained('school_payouts')
                ->cascadeOnDelete()
                ->comment('Выплата');

            // Источник дохода
            $t->foreignId('school_order_id')
                ->nullable()
                ->constrained('school_orders')
                ->nullOnDelete()
                ->comment('Заказ');

            $t->foreignId('school_order_item_id')
                ->nullable()
                ->constrained('school_order_items')
                ->nullOnDelete()
                ->comment('Позиция заказа');

            // Объекты продажи
            $t->foreignId('school_course_id')
                ->nullable()
                ->constrained('school_courses')
                ->nullOnDelete()
                ->comment('Курс');

            $t->foreignId('school_bundle_id')
                ->nullable()
                ->constrained('school_bundles')
                ->nullOnDelete()
                ->comment('Набор курсов');

            $t->foreignId('school_subscription_id')
                ->nullable()
                ->constrained('school_subscriptions')
                ->nullOnDelete()
                ->comment('Подписка');

            // Суммы
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('amount_gross', 18, 2)->default(0)->comment('Начислено брутто');
            $t->decimal('fee_total', 18, 2)->default(0)->comment('Комиссии');
            $t->decimal('tax_total', 18, 2)->default(0)->comment('Налоги/удержания');
            $t->decimal('amount_net', 18, 2)->default(0)->comment('К выплате нетто');

            // Дата заработка
            $t->timestamp('earned_at')->nullable()->comment('Дата начисления дохода');

            // Описание
            $t->string('title')->nullable()->comment('Подпись позиции');
            $t->text('notes')->nullable()->comment('Заметки');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Индексы
            $t->index(['school_payout_id', 'school_order_id'], 'idx_school_payout_order');

            $t->index(
                ['school_course_id', 'school_bundle_id', 'school_subscription_id'],
                'idx_school_payout_item_objects'
            );

            $t->index('earned_at', 'idx_school_payout_item_earned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_payout_items');
    }
};
