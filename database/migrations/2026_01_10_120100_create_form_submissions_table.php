<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Заявки, отправленные через любые формы на сайте
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $t) {
            $t->id();

            // Кто отправил (если пользователь авторизован)
            $t->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            // Ключ/идентификатор формы (например: contact, demo_request, newsletter)
            $t->string('form_key', 64)->index();

            // Базовые поля (зависят от формы — делаем nullable)
            $t->string('name', 255)->nullable();      // Имя отправителя
            $t->string('email', 255)->nullable();     // Email отправителя
            $t->string('phone', 64)->nullable();      // Телефон
            $t->text('message')->nullable();          // Сообщение/комментарий

            // Произвольные данные формы (всё, что не в стандартных полях)
            $t->json('data')->nullable();

            // Техническая информация
            $t->string('ip', 45)->nullable();         // IPv4/IPv6
            $t->text('user_agent')->nullable();       // Браузер/клиент
            $t->text('referrer')->nullable();         // Откуда пришёл
            $t->text('page_url')->nullable();         // На какой странице отправлено
            $t->string('locale', 10)->nullable();     // Локаль сайта

            // Состояние
            $t->boolean('is_spam')->default(false);   // Помечено как спам
            $t->boolean('is_read')->default(false);   // Прочитано менеджером
            $t->timestamp('processed_at')->nullable();// Обработано (перезвон/ответ)

            // Служебные
            $t->text('notes')->nullable();            // Заметки менеджера
            $t->json('meta')->nullable();             // Любая дополнительная мета

            $t->timestamps();
            $t->softDeletes();

            // Частые фильтры
            $t->index(['form_key', 'created_at'], 'idx_form_key_created');
            $t->index(['is_spam', 'is_read'], 'idx_spam_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
