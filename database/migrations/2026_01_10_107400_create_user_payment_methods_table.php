<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Сохранённые способы оплаты конкретных пользователей (токены провайдера и т.п.)
    public function up(): void
    {
        Schema::create('user_payment_methods', function (Blueprint $t) {
            $t->id();

            // Владелец способа оплаты
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Ссылка на справочник способов оплаты
            $t->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            // Идентификаторы на стороне платёжного провайдера
            $t->string('provider', 64)->nullable();                 // stripe|yookassa|paypal|...
            $t->string('provider_customer_id', 191)->nullable();     // customer id в провайдере
            $t->string('provider_payment_method_id', 191)->nullable(); // payment method/token id

            // Карточные реквизиты (если применимо)
            $t->string('brand', 64)->nullable();    // Visa/Mastercard и т.п.
            $t->string('last4', 4)->nullable();     // последние 4 цифры
            $t->unsignedTinyInteger('exp_month')->nullable(); // 1..12
            $t->unsignedSmallInteger('exp_year')->nullable(); // 4-значный год
            $t->string('country', 2)->nullable();   // ISO 3166-1 alpha-2, если от провайдера

            // Биллинг-данные (опционально)
            $t->string('billing_name')->nullable();
            $t->string('billing_email')->nullable();
            $t->string('billing_phone', 32)->nullable();
            $t->json('billing_address')->nullable(); // {line1,line2,city,region,zip,country}

            // Управление
            $t->boolean('is_default')->default(false); // метод по умолчанию для пользователя
            $t->boolean('activity')->default(true);   // выключение без удаления
            $t->json('meta')->nullable();              // любые доп.поля от провайдера

            $t->timestamps();
            $t->softDeletes();

            // Индексы/уникальности
            $t->index(['user_id', 'is_default'], 'idx_upm_user_default');
            $t->index(['payment_method_id', 'activity'], 'idx_upm_method_active');
            $t->index(['provider', 'provider_customer_id'], 'idx_upm_provider_customer');
            $t->unique(['provider', 'provider_payment_method_id'], 'uniq_upm_provider_pm'); // защита от дублей токена
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
