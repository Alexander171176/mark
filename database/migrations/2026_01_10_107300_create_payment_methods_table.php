<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Справочник способов оплаты (карта, ЮKassa, PayPal, счёт и т.п.)
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $t) {
            $t->id();

            $t->string('code', 64)->unique();     // Системный код (например: 'card', 'stripe', 'yookassa', 'paypal')
            $t->string('name');                   // Название для админки/витрины
            $t->string('provider', 64)->nullable();// Провайдер/шлюз (stripe|yookassa|paypal|manual)
            $t->string('type', 32)->nullable();   // Тип: card|bank_transfer|ewallet|cash|invoice|other

            // Возможности способа оплаты
            $t->boolean('supports_refund')->default(true);     // Поддержка возвратов
            $t->boolean('supports_recurring')->default(true);  // Подписки/рекуррентные списания

            // Управление показом
            $t->boolean('activity')->default(true); // Включён/выключен
            $t->unsignedInteger('sort')->default(0); // Сортировка в списке

            $t->json('meta')->nullable();          // Произвольные настройки (ключи, режимы, вебхуки и т.п.)

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры
            $t->index(['activity', 'sort'], 'idx_pm_active_pos');
            $t->index(['provider', 'type'], 'idx_pm_provider_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
