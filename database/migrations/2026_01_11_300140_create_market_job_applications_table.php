<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_job_applications', function (Blueprint $table) {
            $table->id()->comment('ID отклика на вакансию');

            // TENANT / STOREFRONT
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mja_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // JOB (tenant-safe)
            $table->unsignedBigInteger('job_id')
                ->comment('Вакансия (market_jobs.id)');

            // Требует UNIQUE(company_id, storefront_id, id) в market_jobs
            $table->foreign(['company_id', 'storefront_id', 'job_id'], 'fk_mja_tenant_job')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_jobs')
                ->cascadeOnDelete();

            // USER
            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            // SNAPSHOT CONTACTS
            $table->string('candidate_first_name', 255)->nullable()->comment('Имя кандидата (снапшот)');
            $table->string('candidate_last_name', 255)->nullable()->comment('Фамилия кандидата (снапшот)');
            $table->string('candidate_email', 255)->nullable()->comment('Email кандидата (снапшот)');
            $table->string('candidate_phone', 50)->nullable()->comment('Телефон кандидата (снапшот)');

            $table->string('candidate_messenger_type', 32)->nullable()->comment('whatsapp/telegram/...');
            $table->string('candidate_messenger_contact', 255)->nullable()->comment('номер/username/ссылка');

            // CONTENT
            $table->string('subject', 255)->nullable()->comment('Тема');
            $table->longText('message')->nullable()->comment('Сопроводительное письмо');

            $table->string('source', 32)->default('storefront')->comment('storefront/admin/import/api');
            $table->string('utm_source', 100)->nullable()->comment('UTM source');
            $table->string('utm_medium', 100)->nullable()->comment('UTM medium');
            $table->string('utm_campaign', 100)->nullable()->comment('UTM campaign');

            // HR WORKFLOW
            $table->string('status', 32)->default('new')
                ->comment('new/in_review/interview/offer/rejected/hired/archived');

            $table->boolean('is_read')->default(false)->comment('Прочитан ли отклик');
            $table->timestamp('read_at')->nullable()->comment('Когда прочитан');
            $table->timestamp('responded_at')->nullable()->comment('Когда HR ответил/начал обработку');

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->comment('Ответственный (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал запись (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            // AUDIT / ANTISPAM
            $table->string('ip', 64)->nullable()->comment('IP отправителя');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent');

            $table->string('anti_spam_provider', 32)->nullable()->comment('recaptcha/hcaptcha/turnstile');
            $table->boolean('anti_spam_passed')->default(true)->comment('Прошёл антиспам');
            $table->json('anti_spam_meta')->nullable()->comment('score/action/...');

            $table->string('note', 255)->nullable()->comment('Заметка HR/админа');
            $table->timestamps();

            // UNIQUE / INDEXES
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mja_tenant_id');
            $table->unique(['storefront_id', 'id'], 'uq_mja_storefront_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mja_tenant');
            $table->index(['storefront_id', 'job_id', 'created_at'], 'ix_mja_job_time');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_mja_status_time');
            $table->index(['storefront_id', 'is_read', 'created_at'], 'ix_mja_read_time');

            $table->index(['user_id', 'created_at'], 'ix_mja_user_time');
            $table->index(['assigned_to_user_id', 'status'], 'ix_mja_assigned_status');

            $table->index(['candidate_email'], 'ix_mja_candidate_email');
            $table->index(['candidate_phone'], 'ix_mja_candidate_phone');
            $table->index(['ip', 'created_at'], 'ix_mja_ip_time');

            $table->comment('Маркет: отклики на вакансии, tenant-safe. Контакты + сообщение + статус обработки.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_job_applications');
    }
};
