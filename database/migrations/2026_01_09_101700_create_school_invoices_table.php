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
        Schema::create('school_invoices', function (Blueprint $t) {
            $t->id();

            // Заказ онлайн-школы
            $t->foreignId('school_order_id')
                ->constrained('school_orders')
                ->cascadeOnDelete()
                ->comment('Заказ онлайн-школы');

            // Номер инвойса
            $t->string('number', 50)->unique()->comment('Номер инвойса');

            // Статус
            $t->string('status', 20)
                ->default('draft')
                ->comment('draft|issued|paid|void|refunded');

            // Валюта и суммы
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('subtotal', 18, 2)->default(0)->comment('Сумма позиций');
            $t->decimal('discount_total', 18, 2)->default(0)->comment('Сумма скидок');
            $t->decimal('tax_total', 18, 2)->default(0)->comment('Сумма налогов');
            $t->decimal('total', 18, 2)->default(0)->comment('Итого к оплате');

            // Даты жизненного цикла
            $t->timestamp('issued_at')->nullable()->comment('Дата выставления');
            $t->timestamp('due_at')->nullable()->comment('Срок оплаты');
            $t->timestamp('paid_at')->nullable()->comment('Дата оплаты');

            // Реквизиты плательщика
            $t->string('bill_to_name')->nullable()->comment('Имя или компания');
            $t->string('bill_to_tax_id', 64)->nullable()->comment('ИИН/БИН/ИНН/VAT');
            $t->string('bill_to_email')->nullable()->comment('Email плательщика');
            $t->string('bill_to_address1')->nullable()->comment('Адрес 1');
            $t->string('bill_to_address2')->nullable()->comment('Адрес 2');
            $t->string('bill_to_city')->nullable()->comment('Город');
            $t->string('bill_to_region')->nullable()->comment('Регион');
            $t->string('bill_to_postcode', 32)->nullable()->comment('Индекс');
            $t->string('bill_to_country', 2)->nullable()->comment('Страна ISO 3166-1 alpha-2');

            // Примечания
            $t->text('notes')->nullable()->comment('Примечания');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Индексы
            $t->index(['school_order_id', 'status'], 'idx_school_invoice_order_status');
            $t->index(['status', 'issued_at'], 'idx_school_invoice_status_issued');
            $t->index('due_at', 'idx_school_invoice_due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_invoices');
    }
};
