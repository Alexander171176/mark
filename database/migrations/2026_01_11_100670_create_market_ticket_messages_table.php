<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_messages', function (Blueprint $table) {
            $table->id()->comment('ID сообщения тикета');

            /* TENANT (денормализация для быстрых выборок) */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mtm_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* TICKET (tenant-safe, без constrained на ticket_id) */
            $table->unsignedBigInteger('ticket_id')
                ->comment('Тикет (market_tickets.id), tenant-safe');

            $table->foreign(['company_id', 'storefront_id', 'ticket_id'], 'fk_mtm_ticket_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_tickets')
                ->cascadeOnDelete();

            /* AUTHOR */
            $table->foreignId('author_user_id')
                ->nullable()
                ->comment('Автор (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('author_type', 16)->default('customer')
                ->comment('Тип автора: customer/support/system');

            $table->boolean('is_internal')->default(false)
                ->comment('Внутренняя заметка (видит только поддержка)');

            $table->longText('message')->nullable()->comment('Текст сообщения');
            $table->json('meta')->nullable()->comment('Доп. данные (вебхуки/шаблоны/канал)');

            $table->timestamps();

            /* INDEXES */
            $table->unique(['company_id','storefront_id','id'], 'uq_mtm_tenant_id');

            $table->index(['ticket_id', 'created_at'], 'ix_mtm_ticket_time');
            $table->index(['company_id', 'storefront_id', 'ticket_id', 'created_at'], 'ix_mtm_tenant_ticket_time');
            $table->index(['storefront_id', 'author_type', 'created_at'], 'ix_mtm_author_type_time');

            $table->comment('Маркет: сообщения тикетов (переписка клиент/поддержка), tenant-safe, поддержка внутренних заметок.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_messages');
    }
};
