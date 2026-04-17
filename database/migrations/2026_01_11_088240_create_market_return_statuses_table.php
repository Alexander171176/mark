<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_return_statuses', function (Blueprint $table) {
            $table->id()->comment('ID статуса возврата товара');

            /* =========================================================
             * TENANT / STOREFRONT (tenant-safe справочник)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrs_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность статуса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('code', 64)
                ->comment('Код: created/approved/rejected/pickup_scheduled/in_transit/received/inspected/closed/cancelled');

            $table->string('title', 255)->comment('Название статуса (для UI)');

            $table->string('type', 32)->default('info')
                ->comment('Тип для UI: info/success/warning/danger');

            $table->boolean('is_final')->default(false)->comment('Финальный статус (закрыт/отменён)');

            $table->boolean('is_default')->default(false)
                ->comment('Статус по умолчанию для нового возврата (контроль уникальности в приложении)');

            $table->json('meta')->nullable()->comment('Доп. данные (правила переходов, подсказки)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mrs_tenant_id');

            // Внутри витрины + локали код не должен повторяться
            $table->unique(['company_id', 'storefront_id', 'locale', 'code'], 'uq_mrs_tenant_locale_code');

            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mrs_tenant_locale');
            $table->index(['company_id', 'storefront_id', 'locale', 'activity', 'sort'], 'ix_mrs_list');

            $table->index(['company_id', 'storefront_id', 'is_default'], 'ix_mrs_default');
            $table->index(['company_id', 'storefront_id', 'is_final'], 'ix_mrs_final');

            $table->comment('Маркет: статусы возврата товара (логистика возврата), tenant-safe, локали независимы');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_return_statuses');
    }
};
