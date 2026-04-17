<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_ticket_attachments
 * Вложения тикета через Spatie Media Library:
 * - сам файл хранится в таблице media
 * - здесь tenant-safe привязка media к ticket (+ опционально к message)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_attachments', function (Blueprint $table) {
            $table->id()->comment('ID вложения тикета (ссылка на media)');

            /* =========================================================
             * TENANT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mta_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TICKET (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('ticket_id')
                ->comment('Тикет (market_tickets.id), tenant-safe');

            $table->foreign(['company_id', 'storefront_id', 'ticket_id'], 'fk_mta_ticket_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_tickets')
                ->cascadeOnDelete();

            /* =========================================================
             * MESSAGE (optional)
             * ✅ Нельзя делать nullOnDelete на композитном FK с NOT NULL company_id/storefront_id
             * Поэтому делаем FK только на message_id -> market_ticket_messages.id
             * ========================================================= */

            $table->unsignedBigInteger('message_id')
                ->nullable()
                ->comment('Сообщение тикета (market_ticket_messages.id), опционально');

            $table->foreign('message_id', 'fk_mta_message')
                ->references('id')
                ->on('market_ticket_messages')
                ->nullOnDelete();

            /* =========================================================
             * MEDIA (Spatie)
             * ========================================================= */

            $table->unsignedBigInteger('media_id')
                ->comment('Медиафайл (media.id) из Spatie Media Library');

            $table->foreign('media_id', 'fk_mta_media')
                ->references('id')
                ->on('media')
                ->cascadeOnDelete();

            /* =========================================================
             * UI / FLAGS
             * ========================================================= */

            $table->string('type', 32)->default('file')->comment('Тип: file/photo/document');
            $table->boolean('activity')->default(true)->comment('Активность вложения');

            /* =========================================================
             * WHO UPLOADED
             * ========================================================= */

            $table->foreignId('uploaded_by_user_id')
                ->nullable()
                ->comment('Кто прикрепил (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->json('meta')->nullable()->comment('Доп. метаданные (подписи/типы/поля формы)');
            $table->timestamps();

            /* =========================================================
             * CONSTRAINTS / INDEXES
             * ========================================================= */

            // Один media не должен быть привязан к одному тикету дважды
            $table->unique(['ticket_id', 'media_id'], 'uq_mta_ticket_media');

            // (опционально) если файл прикреплён к сообщению — не дублировать его в этом же сообщении
            $table->unique(['message_id', 'media_id'], 'uq_mta_message_media');

            $table->index(['ticket_id', 'activity', 'created_at'], 'ix_mta_ticket_time');
            $table->index(['company_id', 'storefront_id', 'ticket_id', 'activity'], 'ix_mta_tenant_ticket');

            $table->index(['message_id'], 'ix_mta_message');
            $table->index(['media_id'], 'ix_mta_media');
            $table->index(['uploaded_by_user_id', 'created_at'], 'ix_mta_uploader_time');

            $table->comment('Маркет: вложения тикетов через Spatie Media Library (media_id), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_attachments');
    }
};
