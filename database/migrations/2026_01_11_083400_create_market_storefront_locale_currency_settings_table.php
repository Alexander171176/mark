<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_storefront_locale_currency_settings', function (Blueprint $table) {
            $table->id()->comment('ID локальной настройки валюты витрины');

            // Tenant
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            // Витрина (market_storefronts.id)
            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mslcs_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            // Валюта по умолчанию для локали (обязана быть включена на витрине)
            // Важно: НЕ делаем отдельный FK на currencies, чтобы не дублировать ограничения.
            $table->unsignedBigInteger('currency_id')
                ->comment('Валюта по умолчанию (currencies.id), должна быть включена на витрине');

            $table->boolean('activity')->default(true)->comment('Активность настройки для локали');
            $table->timestamps();

            /**
             * Гарантия: выбранная currency_id должна быть включена на витрине
             * (с учётом tenant-изоляции)
             */
            $table->foreign(['company_id', 'storefront_id', 'currency_id'], 'fk_mslcs_to_storefront_currencies')
                ->references(['company_id', 'storefront_id', 'currency_id'])
                ->on('market_storefront_has_currencies')
                ->cascadeOnDelete();

            // 1 запись на локаль в рамках конкретной витрины (tenant-safe)
            $table->unique(['company_id', 'storefront_id', 'locale'], 'uq_mslcs_tenant_storefront_locale');

            $table->index(['company_id', 'storefront_id'], 'ix_mslcs_tenant');
            $table->index(['storefront_id', 'activity'], 'ix_mslcs_storefront_active');
            $table->index(['storefront_id', 'locale', 'activity'], 'ix_mslcs_lookup');

            $table->comment('Маркет: валюта по умолчанию для витрины в разрезе локалей');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_locale_currency_settings');
    }
};
