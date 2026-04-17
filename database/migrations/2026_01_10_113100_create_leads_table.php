<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Лиды/заявки с форм сайта (контакты, заявки на курс и др.)
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $t) {
            $t->id();

            // Базовые контактные данные
            $t->string('name')->nullable();            // Имя отправителя
            $t->string('email')->nullable();           // Email
            $t->string('phone', 32)->nullable();       // Телефон в свободном формате

            // Текст обращения
            $t->text('message')->nullable();           // Сообщение/комментарий

            // Откуда пришёл лид
            $t->string('source', 64)->nullable();      // форма/лендинг/виджет и т.п.
            $t->string('page_url')->nullable();        // страница, где была отправка
            $t->string('referrer')->nullable();        // реферер

            // UTM-метки
            $t->string('utm_source', 64)->nullable();
            $t->string('utm_medium', 64)->nullable();
            $t->string('utm_campaign', 128)->nullable();
            $t->string('utm_term', 128)->nullable();
            $t->string('utm_content', 128)->nullable();

            // Техническое
            $t->string('ip', 45)->nullable();          // IPv4/IPv6
            $t->text('user_agent')->nullable();        // браузер/устройство
            $t->boolean('consent')->default(false);    // согласие с политикой (GDPR/152-ФЗ)

            // Статусы и обработка
            $t->string('status', 20)
                ->default('new')
                ->comment('new|contacted|qualified|won|lost|spam'); // воронка

            $t->timestamp('processed_at')->nullable(); // когда взяли в работу/обработали

            // Ответственный менеджер (если назначается)
            $t->foreignId('manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Примечания и произвольные данные
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Индексы для частых выборок
            $t->index(['status', 'created_at'], 'idx_leads_status_created');
            $t->index(['email', 'phone'], 'idx_leads_contacts');
            $t->index(['utm_source', 'utm_campaign'], 'idx_leads_utm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
