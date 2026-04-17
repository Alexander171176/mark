<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_ticket_has_tags
 * Связь тикетов и тегов (M:N), tenant-safe по storefront.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_has_tags', function (Blueprint $table) {
            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mttht_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // Тикет (tenant-safe)
            $table->unsignedBigInteger('ticket_id')
                ->comment('Тикет (market_tickets.id)');

            $table->foreign(['company_id', 'storefront_id', 'ticket_id'], 'fk_mttht_ticket_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_tickets')
                ->cascadeOnDelete();

            // Тег (tenant-safe)
            $table->unsignedBigInteger('tag_id')
                ->comment('Тег (market_ticket_tags.id)');

            $table->foreign(['company_id', 'storefront_id', 'tag_id'], 'fk_mttht_tag_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_ticket_tags')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка (если нужно)');

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто добавил тег (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->primary(['ticket_id', 'tag_id'], 'pk_market_ticket_has_tags');

            $table->index(['ticket_id', 'activity', 'sort'], 'ix_mttht_ticket_list');
            $table->index(['tag_id', 'activity'], 'ix_mttht_tag_active');
            $table->index(['company_id', 'storefront_id', 'ticket_id'], 'ix_mttht_tenant_ticket');

            $table->comment('Маркет: связь тикетов и тегов (M:N), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_has_tags');
    }
};
