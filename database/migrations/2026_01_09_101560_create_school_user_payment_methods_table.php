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
        Schema::create('school_user_payment_methods', function (Blueprint $t) {
            $t->id();

            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            $t->foreignId('school_payment_method_id')
                ->nullable()
                ->constrained('school_payment_methods')
                ->nullOnDelete()
                ->comment('Способ оплаты');

            $t->string('provider', 64)->nullable()->comment('Провайдер');
            $t->string('provider_customer_id', 191)->nullable()->comment('ID клиента у провайдера');
            $t->string('provider_payment_method_id', 191)->nullable()->comment('ID метода оплаты у провайдера');

            $t->string('brand', 64)->nullable()->comment('Бренд карты');
            $t->string('last4', 4)->nullable()->comment('Последние 4 цифры');
            $t->unsignedTinyInteger('exp_month')->nullable()->comment('Месяц окончания');
            $t->unsignedSmallInteger('exp_year')->nullable()->comment('Год окончания');
            $t->string('country', 2)->nullable()->comment('Страна карты');

            $t->string('billing_name')->nullable()->comment('Имя плательщика');
            $t->string('billing_email')->nullable()->comment('Email плательщика');
            $t->string('billing_phone', 32)->nullable()->comment('Телефон плательщика');
            $t->json('billing_address')->nullable()->comment('Платёжный адрес');

            $t->boolean('is_default')->default(false)->comment('Метод по умолчанию');
            $t->boolean('activity')->default(true)->comment('Активность');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            $t->index(['user_id', 'is_default'], 'idx_school_upm_user_default');
            $t->index(['school_payment_method_id', 'activity'], 'idx_school_upm_method_active');
            $t->index(['provider', 'provider_customer_id'], 'idx_school_upm_provider_customer');
            $t->unique(['provider', 'provider_payment_method_id'], 'uq_school_upm_provider_pm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_user_payment_methods');
    }
};
