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
        Schema::create('school_orders', function (Blueprint $t) {
            $t->id();

            // Кто оформил заказ
            $t->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Пользователь, оформивший заказ');

            // Связь с курсом
            $t->foreignId('school_course_id')
                ->nullable()
                ->constrained('school_courses')
                ->nullOnDelete()
                ->comment('Курс');

            // Связь с потоком курса
            $t->foreignId('school_course_schedule_id')
                ->nullable()
                ->constrained('school_course_schedules')
                ->nullOnDelete()
                ->comment('Поток курса');

            // Номер заказа
            $t->string('number', 32)->unique()->comment('Человекочитаемый номер заказа');

            // Покупатель
            $t->string('buyer_name')->nullable()->comment('Имя покупателя');
            $t->string('buyer_email')->nullable()->comment('Email покупателя');
            $t->string('buyer_phone')->nullable()->comment('Телефон покупателя');

            // Юридические данные
            $t->string('billing_company')->nullable()->comment('Компания');
            $t->string('billing_tax_id')->nullable()->comment('БИН/ИИН/ИНН/VAT');
            $t->string('billing_address')->nullable()->comment('Юридический адрес');

            // Оплата
            $t->boolean('is_paid')->default(false)->comment('Оплачен ли заказ');
            $t->timestamp('paid_at')->nullable()->comment('Дата оплаты');

            $t->integer('payment_method_id')->nullable()->comment('ID способа оплаты');
            $t->string('payment_method', 32)->nullable()->comment('Метод оплаты');
            $t->string('payment_provider', 32)->nullable()->comment('Платёжный провайдер');
            $t->string('payment_reference', 128)->nullable()->comment('ID транзакции');
            $t->string('confirmation_code')->nullable()->comment('Код подтверждения');
            $t->string('confirmation_status')->nullable()->comment('Статус подтверждения');
            $t->string('failure_reason')->nullable()->comment('Причина ошибки оплаты');

            // Валюта и суммы
            $t->char('currency', 3)->default('USD')->comment('Валюта ISO 4217');
            $t->decimal('subtotal', 12, 2)->default(0)->comment('Сумма без скидок и налогов');
            $t->decimal('discount_total', 12, 2)->default(0)->comment('Сумма скидки');
            $t->decimal('tax_total', 12, 2)->default(0)->comment('Сумма налога');
            $t->decimal('total', 12, 2)->default(0)->comment('Итоговая сумма');

            // Статусы
            $t->string('status', 32)->default('new')->comment('Статус заказа');
            $t->string('payment_status', 32)->default('pending')->comment('Статус оплаты');

            // Содержимое заказа
            $t->json('items')->nullable()->comment('Курсы/потоки/услуги в заказе');
            $t->json('meta')->nullable()->comment('Дополнительные данные заказа');

            // Комментарии
            $t->text('user_comment')->nullable()->comment('Комментарий покупателя');
            $t->text('manager_comment')->nullable()->comment('Комментарий менеджера');

            // Интеграции
            $t->string('external_id')->nullable()->comment('ID во внешней системе');
            $t->timestamp('exported_at')->nullable()->comment('Дата выгрузки');

            // Технические поля
            $t->string('client_ip', 45)->nullable()->comment('IP клиента');
            $t->text('user_agent')->nullable()->comment('User Agent');
            $t->string('public_hash', 64)->nullable()->comment('Публичный хеш заказа');

            $t->timestamps();

            // Индексы
            $t->index(['user_id', 'status'], 'idx_school_order_user_status');
            $t->index('school_course_id', 'idx_school_order_course');
            $t->index('school_course_schedule_id', 'idx_school_order_schedule');
            $t->index('payment_status', 'idx_school_order_payment_status');
            $t->index('buyer_email', 'idx_school_order_buyer_email');
            $t->index('external_id', 'idx_school_order_external');
            $t->index('public_hash', 'idx_school_order_public_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_orders');
    }
};
