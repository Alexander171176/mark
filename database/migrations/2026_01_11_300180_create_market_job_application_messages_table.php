<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_job_application_messages', function (Blueprint $table) {
            $table->id()->comment('ID сообщения в чате отклика');

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mjam_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* APPLICATION tenant-safe */
            $table->unsignedBigInteger('application_id')
                ->comment('Отклик (market_job_applications.id)');

            $table->foreign(['company_id', 'storefront_id', 'application_id'], 'fk_mjam_tenant_application')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_applications')
                ->cascadeOnDelete();

            /* MESSAGE */
            $table->boolean('activity')->default(true)->comment('Активность сообщения');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок (обычно не нужен)');
            $table->string('message_type', 32)->default('text')->comment('text/system/status/file_note/...');

            $table->longText('body')->nullable()->comment('Текст сообщения');
            $table->json('meta')->nullable()->comment('Доп. данные');

            /* SENDER */
            $table->string('sender_type', 16)->default('candidate')->comment('candidate/hr/system');

            $table->foreignId('sender_user_id')
                ->nullable()
                ->comment('Отправитель (users.id), если hr/system')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('sender_name', 255)->nullable()->comment('Имя отправителя (снапшот)');

            /* DELIVERY / READ */
            $table->string('status', 32)->default('sent')->comment('draft/sent/delivered/read/failed');
            $table->timestamp('sent_at')->nullable()->comment('Когда отправлено');
            $table->timestamp('read_at')->nullable()->comment('Когда прочитано');
            $table->string('channel', 32)->nullable()->comment('web/email/telegram/...');

            /* ✅ KEYs for tenant-safe (важно объявить ДО self-FK) */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mjam_tenant_id');
            $table->unique(['storefront_id', 'id'], 'uq_mjam_storefront_id');

            /* REPLY (self) */
            $table->unsignedBigInteger('reply_to_message_id')
                ->nullable()
                ->comment('Ответ на сообщение (market_job_application_messages.id)');

            /**
             * ❗ SET NULL для композитного FK нельзя (company_id/storefront_id NOT NULL).
             * Поэтому RESTRICT: нельзя удалить сообщение, если на него есть ответы.
             */
            $table->foreign(['company_id', 'storefront_id', 'reply_to_message_id'], 'fk_mjam_reply_to_same_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_application_messages')
                ->restrictOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка');
            $table->timestamps();

            /* INDEXES */
            $table->index(['company_id', 'storefront_id'], 'ix_mjam_tenant');
            $table->index(['storefront_id', 'application_id', 'created_at'], 'ix_mjam_application_time');
            $table->index(['storefront_id', 'application_id', 'activity', 'created_at'], 'ix_mjam_application_list');

            $table->index(['sender_type', 'created_at'], 'ix_mjam_sender_type_time');
            $table->index(['sender_user_id', 'created_at'], 'ix_mjam_sender_user_time');

            $table->index(['status', 'created_at'], 'ix_mjam_status_time');
            $table->index(['read_at'], 'ix_mjam_read_at');

            $table->comment('Маркет: сообщения чата по отклику (HR ↔ кандидат), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_job_application_messages');
    }
};
