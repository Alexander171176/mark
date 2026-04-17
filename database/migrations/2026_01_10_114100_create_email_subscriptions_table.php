<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Подписки на email-рассылки (новости, промо и т.п.)
    public function up(): void
    {
        Schema::create('email_subscriptions', function (Blueprint $t) {
            $t->id();

            // Опционально — связь с пользователем
            $t->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Сам email (обязательно)
            $t->string('email');                         // адрес подписчика

            // Список/канал, на который оформлена подписка (например: newsletter, promos)
            $t->string('list', 64)->default('newsletter');

            // Статус подписки
            $t->string('status', 20)
                ->default('pending')
                ->comment('pending|subscribed|unsubscribed|bounced|complained');

            // Double opt-in / подтверждение
            $t->string('confirm_token', 64)->nullable(); // токен подтверждения
            $t->timestamp('confirmed_at')->nullable();   // когда подтвердил

            // Отписка
            $t->timestamp('unsubscribed_at')->nullable(); // когда отписался
            $t->string('unsub_reason', 255)->nullable();  // причина (если указал)

            // Про источник / технические детали
            $t->string('source', 64)->nullable();       // форма, лендинг, импорт
            $t->string('locale', 16)->nullable();       // язык интерфейса подписки
            $t->string('ip', 45)->nullable();           // IPv4/IPv6
            $t->text('user_agent')->nullable();         // браузер/устройство

            // Интеграции с провайдерами email (Mailchimp, Sendgrid и т.п.)
            $t->string('provider', 64)->nullable();           // имя провайдера
            $t->string('provider_subscriber_id')->nullable(); // id подписчика у провайдера
            $t->string('last_event', 64)->nullable();         // последнее событие (delivered/open/click/spam/bounce)

            // Произвольные метаданные / теги
            $t->json('tags')->nullable();               // массив тегов подписчика
            $t->json('meta')->nullable();               // любые доп.данные

            $t->timestamps();
            $t->softDeletes();

            // Индексы/уникальность: один email в рамках списка — один раз
            $t->unique(['email', 'list'], 'uniq_email_list');

            $t->index(['list', 'status'], 'idx_list_status');
            $t->index(['provider', 'provider_subscriber_id'], 'idx_provider_pid');
            $t->index(['confirmed_at', 'unsubscribed_at'], 'idx_confirm_unsub');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_subscriptions');
    }
};
