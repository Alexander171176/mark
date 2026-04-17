<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_submission_events
 * История событий по отправке: пометка спам, ответ, смена статуса, экспорт и т.п.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_submission_events', function (Blueprint $table) {
            $table->id()->comment('ID события отправки формы');

            $table->foreignId('submission_id')
                ->comment('Отправка формы (market_feedback_submissions.id)')
                ->constrained('market_feedback_submissions')
                ->cascadeOnDelete();

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfse_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // Защита: событие в рамках витрины отправки
            $table->foreign(['storefront_id', 'submission_id'], 'fk_mfse_storefront_submission')
                ->references(['storefront_id', 'id'])
                ->on('market_feedback_submissions')
                ->cascadeOnDelete();

            $table->string('event', 64)->comment('Событие: created/status_changed/marked_spam/replied/closed/exported/...');

            $table->string('actor_type', 16)->default('system')->comment('Кто сделал: customer/support/system');
            $table->foreignId('actor_user_id')
                ->nullable()
                ->comment('Пользователь-инициатор (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('from_value', 191)->nullable()->comment('Было (универсально)');
            $table->string('to_value', 191)->nullable()->comment('Стало (универсально)');

            $table->string('note', 255)->nullable()->comment('Комментарий к событию');
            $table->json('meta')->nullable()->comment('Доп. данные события');

            $table->timestamps();

            // ✅ критично для tenant-safe ссылок в будущем
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mfse_tenant_id');

            $table->index(['submission_id', 'created_at'], 'ix_mfse_submission_time');
            $table->index(['storefront_id', 'submission_id', 'created_at'], 'ix_mfse_storefront_submission_time');
            $table->index(['storefront_id', 'event', 'created_at'], 'ix_mfse_storefront_event_time');
            $table->index(['actor_user_id', 'created_at'], 'ix_mfse_actor_time');

            $table->comment('Маркет: события/история по отправкам формы (модерация/аудит), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_submission_events');
    }
};
