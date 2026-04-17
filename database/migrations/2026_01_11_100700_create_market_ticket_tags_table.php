<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_ticket_tags
 * Теги для тикетов в рамках витрины (tenant-safe), как в HelpDesk.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_ticket_tags', function (Blueprint $table) {
            $table->id()->comment('ID тега тикетов');

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mttag_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность тега');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('title', 100)->comment('Название тега');
            $table->string('slug', 120)->comment('Slug тега (внутри витрины уникален)');

            $table->string('color', 32)->nullable()->comment('Цвет/метка для UI (опционально)');
            $table->string('type', 32)->default('label')->comment('Тип: label/system (опционально)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->timestamps();

            // slug уникален внутри витрины
            $table->unique(['storefront_id', 'slug'], 'uq_mttag_storefront_slug');

            // критично для tenant-safe FK из pivot/прочего
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mttag_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mttag_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mttag_list');

            $table->comment('Маркет: теги тикетов (витринные), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_ticket_tags');
    }
};
