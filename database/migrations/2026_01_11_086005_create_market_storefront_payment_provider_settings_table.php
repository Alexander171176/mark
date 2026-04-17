<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefront_payment_provider_settings', function (Blueprint $table) {
            $table->id()->comment('ID настройки подключения платёжного провайдера к витрине');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_sfpays_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * PROVIDER (global)
             * ========================================================= */

            $table->foreignId('provider_id')
                ->comment('Провайдер (market_payment_providers.id)')
                ->constrained('market_payment_providers')
                ->restrictOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность подключения');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');

            /* =========================================================
             * SETTINGS
             * ========================================================= */

            $table->unsignedSmallInteger('settings_version')->default(1)->comment('Версия структуры settings JSON');
            $table->json('settings')->nullable()->comment('Настройки (ключи API, мерчант, endpoints, callback URLs и т.п.)');

            $table->json('public_meta')->nullable()->comment('Публичные данные для UI (логотип, подсказки, инструкции)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ tenant-safe ключ для композитных FK из других таблиц (если понадобится)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_sfpays_tenant_id');

            // 1 провайдер = 1 подключение на витрину
            $table->unique(['storefront_id', 'provider_id'], 'uq_market_sfpays_storefront_provider');

            $table->index(['company_id', 'storefront_id'], 'ix_market_sfpays_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_market_sfpays_storefront_list');
            $table->index(['storefront_id', 'provider_id', 'activity'], 'ix_market_sfpays_storefront_provider_active');

            $table->comment('Маркет: подключения платёжных провайдеров к витрине (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_payment_provider_settings');
    }
};
