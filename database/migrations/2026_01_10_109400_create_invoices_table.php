<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Инвойсы (счета) по заказам
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $t) {
            $t->id();

            // Привязка к заказу
            $t->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Номер инвойса (для писем/печати), уникален
            $t->string('number', 50)->unique();

            // Статус инвойса
            $t->string('status', 20)
                ->default('draft')
                ->comment('draft|issued|paid|void|refunded');

            // Валюта и суммы (фиксируем на момент выставления)
            $t->char('currency', 3)->default('USD');        // ISO 4217
            $t->decimal('subtotal', 18, 2)->default(0);     // сумма позиций
            $t->decimal('discount_total', 18, 2)->default(0);// скидки
            $t->decimal('tax_total', 18, 2)->default(0);    // налоги
            $t->decimal('total', 18, 2)->default(0);        // к оплате

            // Даты жизненного цикла
            $t->timestamp('issued_at')->nullable();         // когда выставлен
            $t->timestamp('due_at')->nullable();            // срок оплаты
            $t->timestamp('paid_at')->nullable();           // когда оплачен

            // Реквизиты плательщика (снимок на момент выставления)
            $t->string('bill_to_name')->nullable();         // имя/компания
            $t->string('bill_to_tax_id', 64)->nullable();   // ИНН/НДС и т.п.
            $t->string('bill_to_email')->nullable();
            $t->string('bill_to_address1')->nullable();
            $t->string('bill_to_address2')->nullable();
            $t->string('bill_to_city')->nullable();
            $t->string('bill_to_region')->nullable();       // область/штат
            $t->string('bill_to_postcode', 32)->nullable();
            $t->string('bill_to_country', 2)->nullable();   // ISO 3166-1 alpha-2

            // Примечания и метаданные
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры
            $t->index(['order_id', 'status'], 'idx_invoices_order_status');
            $t->index(['status', 'issued_at'], 'idx_invoices_status_issued');
            $t->index('due_at', 'idx_invoices_due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
