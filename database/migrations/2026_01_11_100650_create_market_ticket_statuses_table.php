<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_statuses', function (Blueprint $table) {
            $table->id()->comment('ID статуса тикета');

            /* TENANT / STOREFRONT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mts_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* FIELDS */
            $table->boolean('activity')->default(true)->comment('Активность статуса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('code', 64)->comment('Код: open/pending/answered/closed/spam/...');
            $table->string('title', 255)->comment('Название статуса');

            $table->string('type', 32)->default('info')->comment('Тип UI: info/success/warning/danger');
            $table->boolean('is_final')->default(false)->comment('Финальный статус');

            $table->boolean('is_default')->default(false)
                ->comment('Статус по умолчанию для нового тикета (уникальность контролировать в приложении)');

            $table->json('meta')->nullable()->comment('Доп. данные/маппинг');

            $table->timestamps();

            /* UNIQUE / INDEXES */
            $table->unique(['storefront_id', 'id'], 'uq_mts_storefront_id');
            $table->unique(['storefront_id', 'locale', 'code'], 'uq_mts_storefront_locale_code');

            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mts_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mts_list');
            $table->index(['storefront_id', 'is_default'], 'ix_mts_default');
            $table->index(['storefront_id', 'is_final'], 'ix_mts_final');

            $table->comment('Маркет: статусы тикетов поддержки, tenant-safe, локали независимы');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_statuses');
    }
};
