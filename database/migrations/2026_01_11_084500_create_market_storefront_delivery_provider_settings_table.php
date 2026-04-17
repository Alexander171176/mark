<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefront_delivery_provider_settings', function (Blueprint $table) {
            $table->id()->comment('ID настройки провайдера доставки на витрине');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS
             * ========================================================= */

            $table->unsignedBigInteger('provider_id')
                ->comment('Провайдер доставки (market_delivery_providers.id)');

            /**
             * Опционально: сервис/тариф по умолчанию для данного провайдера на витрине.
             * НЕ используем как “ключ разбиения” на несколько строк, иначе ломаем FK (company,storefront,provider).
             */
            $table->unsignedBigInteger('provider_service_id')
                ->nullable()
                ->comment('Сервис провайдера по умолчанию (market_delivery_provider_services.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Провайдер включен на витрине');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка провайдеров в UI/админке');

            $table->boolean('is_default')->default(false)->comment('Провайдер по умолчанию (контроль в приложении)');
            $table->boolean('is_sandbox')->default(false)->comment('Песочница/тестовый режим');

            /* =========================================================
             * CONFIG / CREDENTIALS
             * ========================================================= */

            $table->json('credentials')->nullable()->comment('Секреты/ключи/токены (хранить безопасно!)');
            $table->json('settings')->nullable()->comment('Публичные/сервисные настройки провайдера');
            $table->json('meta')->nullable()->comment('Доп. данные/маппинги/сырой конфиг');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_msdps_tenant_id');

            // ✅ КРИТИЧЕСКИ ВАЖНО: один провайдер = одна запись на витрину (иначе FK из других таблиц невозможен)
            $table->unique(['company_id', 'storefront_id', 'provider_id'], 'uq_msdps_tenant_provider');

            // Индексы под списки/выборки
            $table->index(['company_id', 'storefront_id'], 'ix_msdps_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_msdps_storefront_list');

            $table->index(['storefront_id', 'provider_id', 'activity'], 'ix_msdps_storefront_provider_active');
            $table->index(['storefront_id', 'is_default', 'activity'], 'ix_msdps_storefront_default');
            $table->index(['provider_service_id'], 'ix_msdps_provider_service');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_msdps_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_msdps_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // справочник провайдеров
            $table->foreign('provider_id', 'fk_msdps_provider')
                ->references('id')
                ->on('market_delivery_providers')
                ->restrictOnDelete();

            // сервис провайдера “по умолчанию”
            $table->foreign('provider_service_id', 'fk_msdps_provider_service')
                ->references('id')
                ->on('market_delivery_provider_services')
                ->nullOnDelete();

            $table->comment('Маркет: настройки провайдера доставки на витрине (1 провайдер = 1 запись), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_delivery_provider_settings');
    }
};
