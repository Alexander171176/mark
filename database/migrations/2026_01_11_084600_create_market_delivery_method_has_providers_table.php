<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_method_has_providers', function (Blueprint $table) {

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

            $table->unsignedBigInteger('delivery_method_id')
                ->comment('Метод доставки (market_delivery_methods.id)');

            $table->unsignedBigInteger('provider_id')
                ->comment('Провайдер доставки (market_delivery_providers.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность связки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('service_code', 64)->nullable()->comment('Код сервиса/тарифа провайдера (например: CDEK_PVZ)');
            $table->string('service_name', 255)->nullable()->comment('Читаемое название сервиса (опционально)');

            $table->json('settings')->nullable()->comment('Доп. настройки для метода+провайдера (override)');

            $table->timestamps();

            /* =========================================================
             * PRIMARY / INDEXES
             * ========================================================= */

            // ✅ tenant-safe PK (единый подход)
            $table->primary(
                ['company_id', 'storefront_id', 'delivery_method_id', 'provider_id'],
                'pk_mdhp_tenant_method_provider'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mdhp_tenant');

            $table->index(
                ['company_id', 'storefront_id', 'delivery_method_id', 'activity', 'sort'],
                'ix_mdhp_method_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'provider_id', 'activity', 'sort'],
                'ix_mdhp_provider_list'
            );

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // company
            $table->foreign('company_id', 'fk_mdhp_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mdhp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // ✅ метод доставки обязан быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'delivery_method_id'], 'fk_mdhp_method_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_delivery_methods')
                ->cascadeOnDelete();

            /**
             * ✅ провайдер обязан быть подключён к этой же company+storefront
             * (ссылаемся на settings по tenant-safe UNIQUE(company_id, storefront_id, provider_id))
             */
            $table->foreign(['company_id', 'storefront_id', 'provider_id'], 'fk_mdhp_provider_enabled_tenant')
                ->references(['company_id', 'storefront_id', 'provider_id'])
                ->on('market_storefront_delivery_provider_settings')
                ->cascadeOnDelete();

            /**
             * ❗️Базовый FK на справочник провайдеров НЕ нужен и часто мешает:
             * provider_id уже “проверен” через settings, плюс двойные FK усложняют удаление.
             * Если принципиально нужен — ставь RESTRICT, а не CASCADE.
             */
            // $table->foreign('provider_id', 'fk_mdhp_provider')
            //     ->references('id')
            //     ->on('market_delivery_providers')
            //     ->restrictOnDelete();

            $table->comment('Маркет: провайдеры, обслуживающие методы доставки (M:N), tenant-safe по company+storefront');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_method_has_providers');
    }
};
