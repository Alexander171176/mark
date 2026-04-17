<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_ticket_histories
 * Аудит/история тикета: смена статуса, назначения, приоритета, флагов и т.п.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_histories', function (Blueprint $table) {
            $table->id()->comment('ID записи истории тикета');

            /* =========================================================
             * TENANT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mth_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TICKET (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('ticket_id')
                ->comment('Тикет (market_tickets.id), tenant-safe');

            $table->foreign(['company_id', 'storefront_id', 'ticket_id'], 'fk_mth_ticket_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_tickets')
                ->cascadeOnDelete();

            /* =========================================================
             * EVENT DATA
             * ========================================================= */

            $table->string('event', 64)
                ->comment('Событие: status_changed/assigned/priority_changed/message_added/tag_added/...');

            $table->string('actor_type', 16)
                ->default('system')
                ->comment('Кто сделал: customer/support/system');

            $table->foreignId('actor_user_id')
                ->nullable()
                ->comment('Пользователь-инициатор (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('from_value', 191)->nullable()->comment('Было (универсально)');
            $table->string('to_value', 191)->nullable()->comment('Стало (универсально)');

            /* =========================================================
             * MESSAGE (optional)
             * ✅ Нельзя nullOnDelete на композитном FK с NOT NULL company_id/storefront_id
             * Поэтому FK только на message_id -> market_ticket_messages.id
             * ========================================================= */

            $table->unsignedBigInteger('message_id')
                ->nullable()
                ->comment('Сообщение тикета (market_ticket_messages.id), если связано');

            $table->foreign('message_id', 'fk_mth_message')
                ->references('id')
                ->on('market_ticket_messages')
                ->nullOnDelete();

            /* =========================================================
             * META
             * ========================================================= */

            $table->string('note', 255)->nullable()->comment('Комментарий к событию');
            $table->json('meta')->nullable()->comment('Доп. данные события (payload, поля, старые/новые значения)');
            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['ticket_id', 'created_at'], 'ix_mth_ticket_time');
            $table->index(['company_id', 'storefront_id', 'ticket_id', 'created_at'], 'ix_mth_tenant_ticket_time');
            $table->index(['company_id', 'storefront_id', 'event', 'created_at'], 'ix_mth_tenant_event_time');
            $table->index(['actor_user_id', 'created_at'], 'ix_mth_actor_time');
            $table->index(['message_id'], 'ix_mth_message');

            $table->comment('Маркет: история/аудит тикетов (смена статуса, назначения, приоритета, теги и т.д.), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_histories');
    }
};
