<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Учётные записи платёжных провайдеров (Stripe, PayPal, YooKassa и т.п.)
    public function up(): void
    {
        Schema::create('provider_accounts', function (Blueprint $t) {
            $t->id();

            // Идентификация провайдера и режимов
            $t->string('provider', 64);                 // например: stripe|paypal|yookassa
            $t->string('title')->nullable();            // человеко-читаемое имя: "Stripe основной"
            $t->string('mode', 8)->default('test');     // test|live

            // Идентификаторы/ключи (что-то может быть пустым в зависимости от провайдера)
            $t->string('account_id', 191)->nullable();  // ID аккаунта у провайдера (например, Stripe acct_***)
            $t->string('public_key', 255)->nullable();  // publishable/client id
            $t->text('secret_key')->nullable();         // секретный ключ (будет шифроваться на уровне модели)
            $t->text('webhook_secret')->nullable();     // секрет для проверки вебхуков (шифруем в модели)

            // Поддерживаемые валюты/страны и произвольные настройки
            $t->json('supported_currencies')->nullable(); // ["USD","EUR","RUB"]
            $t->json('supported_countries')->nullable();  // ["US","EU","RU"]
            $t->json('config')->nullable();               // любые специфические настройки (merchant_id, scopes и т.п.)

            // Флаги
            $t->boolean('activity')->default(true);    // активна ли учётка
            $t->boolean('is_default')->default(false);  // использовать по умолчанию

            // Кто создал/обновил (опционально)
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры/уникальности
            $t->index(['provider', 'mode', 'activity'], 'idx_provider_mode_active');
            $t->unique(['provider', 'mode', 'account_id'], 'uniq_provider_mode_account'); // в рамках провайдера+режима один account_id
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_accounts');
    }
};
