<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_job_application_message_media', function (Blueprint $table) {
            $table->id()->comment('ID привязки медиа к сообщению отклика');

            // TENANT
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mjammed_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // MESSAGE tenant-safe
            $table->unsignedBigInteger('message_id')
                ->comment('Сообщение (market_job_application_messages.id)');

            $table->foreign(['company_id', 'storefront_id', 'message_id'], 'fk_mjammed_tenant_message')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_application_messages')
                ->cascadeOnDelete();

            // SPATIE MEDIA
            $table->unsignedBigInteger('media_id')->comment('Файл Spatie (media.id)');

            $table->foreign('media_id', 'fk_mjammed_media')
                ->references('id')
                ->on('media')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность прикрепления');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка медиа внутри сообщения');

            $table->string('collection', 64)->default('attachments')->comment('attachments/images/docs/...');

            $table->string('note', 255)->nullable()->comment('Заметка');
            $table->timestamps();

            // 1 файл не должен дважды прикрепиться к одному сообщению
            $table->unique(['message_id', 'media_id'], 'uq_mjammed_message_media');

            $table->index(['company_id', 'storefront_id'], 'ix_mjammed_tenant');
            $table->index(['storefront_id', 'message_id', 'activity', 'sort'], 'ix_mjammed_message_list');
            $table->index(['media_id'], 'ix_mjammed_media');

            $table->comment('Маркет: медиа (Spatie) для сообщений чата по отклику, tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_job_application_message_media');
    }
};
