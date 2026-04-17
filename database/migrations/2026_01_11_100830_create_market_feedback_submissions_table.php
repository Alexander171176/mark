<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_submissions
 * Отправки формы (лиды/сообщения). Храним снапшоты + антиспам поля.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_submissions', function (Blueprint $table) {
            $table->id()->comment('ID отправки формы');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfs_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FORM (tenant-safe)
             * Требование: в market_feedback_forms есть unique(company_id, storefront_id, id)
             * ========================================================= */
            $table->unsignedBigInteger('form_id')
                ->comment('Форма (market_feedback_forms.id)');

            $table->foreign(['company_id', 'storefront_id', 'form_id'], 'fk_mfs_form_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_feedback_forms')
                ->cascadeOnDelete();

            /* =========================================================
             * AUTHOR
             * ========================================================= */
            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('locale', 10)->nullable()->comment('Локаль отправки');

            /* =========================================================
             * STATUS / SNAPSHOTS
             * ========================================================= */
            $table->string('status', 32)->default('new')
                ->comment('Статус: new/processing/answered/closed/spam');

            $table->boolean('is_spam')->default(false)->comment('Пометка как спам (быстрый флаг)');
            $table->unsignedTinyInteger('spam_score')->default(0)->comment('Скор спама 0..100 (если считаешь)');

            $table->string('name', 255)->nullable()->comment('Имя отправителя (если есть в форме)');
            $table->string('email', 255)->nullable()->comment('Email отправителя');
            $table->string('phone', 50)->nullable()->comment('Телефон отправителя');

            $table->string('messenger_type', 32)->nullable()->comment('Мессенджер: whatsapp/telegram/viber/...');
            $table->string('messenger_contact', 255)->nullable()->comment('Контакт мессенджера');

            $table->string('subject', 255)->nullable()->comment('Тема (если есть)');
            $table->longText('message')->nullable()->comment('Сообщение (если есть)');

            /* =========================================================
             * ANTI-SPAM / TECH
             * ========================================================= */
            $table->string('ip', 64)->nullable()->comment('IP отправителя');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent');
            $table->string('fingerprint', 64)->nullable()->comment('Fingerprint клиента (хеш от параметров)');
            $table->string('form_token', 64)->nullable()->comment('Токен формы (одноразовый/короткий)');
            $table->unsignedSmallInteger('fill_seconds')->nullable()->comment('Сколько секунд заполняли форму');
            $table->string('honeypot_value', 191)->nullable()->comment('Значение honeypot поля (если бот заполнил)');

            $table->json('payload')->nullable()->comment('Полная отправка (key => value)');

            /* =========================================================
             * HANDLING
             * ========================================================= */
            $table->foreignId('handled_by_user_id')
                ->nullable()
                ->comment('Кто обработал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('handled_at')->nullable()->comment('Когда обработали');
            $table->string('note', 255)->nullable()->comment('Заметка оператора');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // критично для будущих tenant-safe FK на submissions
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mfs_tenant_id');

            // если хочешь оставлять как у тебя — можно, не мешает
            $table->unique(['storefront_id','id'], 'uq_mfs_storefront_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mfs_tenant');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_mfs_storefront_status_time');
            $table->index(['storefront_id', 'is_spam', 'created_at'], 'ix_mfs_storefront_spam_time');
            $table->index(['form_id', 'created_at'], 'ix_mfs_form_time');

            $table->index(['ip', 'created_at'], 'ix_mfs_ip_time');
            $table->index(['fingerprint', 'created_at'], 'ix_mfs_fingerprint_time');
            $table->index(['email', 'created_at'], 'ix_mfs_email_time');

            $table->index(['handled_by_user_id', 'handled_at'], 'ix_mfs_handler_time');

            // дедуп токена в пределах витрины (NULL в MySQL не конфликтует)
            $table->unique(['storefront_id', 'form_token'], 'uq_mfs_storefront_form_token');

            $table->comment('Маркет: отправки форм обратной связи (лиды), tenant-safe + антиспам поля.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_submissions');
    }
};
