<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_quotes_cache', function (Blueprint $table) {
            $table->id()->comment('ID записи кеша расчёта доставки');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS (nullable — допускаем кеш без привязки к методу/провайдеру)
             * ========================================================= */

            $table->unsignedBigInteger('delivery_method_id')
                ->nullable()
                ->comment('Метод доставки (market_delivery_methods.id), nullable');

            $table->unsignedBigInteger('provider_id')
                ->nullable()
                ->comment('Провайдер (market_delivery_providers.id), nullable');

            $table->string('provider_service_code', 64)
                ->nullable()
                ->comment('Код сервиса провайдера (например: CDEK_PVZ)');

            /* =========================================================
             * CONTEXT
             * ========================================================= */

            $table->string('locale', 10)->nullable()->comment('Локаль (ru/kk/en)');

            // Адрес назначения
            $table->string('country_code', 2)->nullable()->comment('ISO2 страны (KZ/...)');
            $table->string('region', 128)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('district', 128)->nullable();
            $table->string('postcode', 16)->nullable();

            // Координаты
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Параметры посылки
            $table->unsignedInteger('items_count')->default(0)->comment('Кол-во позиций');
            $table->decimal('weight', 18, 6)->default(0)->comment('Вес (общий)');
            $table->decimal('length', 18, 3)->nullable();
            $table->decimal('width', 18, 3)->nullable();
            $table->decimal('height', 18, 3)->nullable();

            // Контекст суммы заказа
            $table->decimal('order_total', 18, 2)->nullable()->comment('Сумма заказа');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта суммы/результата (currencies.id), nullable');

            /* =========================================================
             * RESULT
             * ========================================================= */

            $table->decimal('price', 18, 2)->nullable()->comment('Итоговая стоимость доставки');
            $table->unsignedSmallInteger('min_days')->nullable();
            $table->unsignedSmallInteger('max_days')->nullable();

            $table->string('status', 32)->default('ok')->comment('ok|error|unavailable');
            $table->string('error_message', 255)->nullable()->comment('Ошибка провайдера/валидации');

            /* =========================================================
             * CACHE PAYLOAD
             * ========================================================= */

            $table->string('cache_key', 64)->comment('Хеш ключа кеша');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            $table->timestamp('expires_at')->nullable()->comment('Когда запись считается устаревшей');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Дедупликация кеша в пределах витрины
            $table->unique(['storefront_id', 'cache_key'], 'uq_market_quotes_storefront_cache_key');

            // Tenant индексы
            $table->index(['company_id', 'storefront_id'], 'ix_market_quotes_tenant');
            $table->index(['storefront_id', 'locale'], 'ix_market_quotes_storefront_locale');

            // “актуальный кеш если не протух”
            $table->index(['storefront_id', 'status', 'expires_at'], 'ix_market_quotes_storefront_status_expires');

            // По провайдеру/сервису
            $table->index(['provider_id', 'provider_service_code'], 'ix_market_quotes_provider_service');

            // По методу (инвалидации/отладка)
            $table->index(['delivery_method_id', 'expires_at'], 'ix_market_quotes_method_expires');

            $table->index(['expires_at'], 'ix_market_quotes_expires_at');

            // полезно для tenant-safe join'ов
            $table->index(['company_id', 'storefront_id', 'provider_id'], 'ix_market_quotes_tenant_provider');
            $table->index(['company_id', 'storefront_id', 'delivery_method_id'], 'ix_market_quotes_tenant_method');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // company
            $table->foreign('company_id', 'fk_market_quotes_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_quotes_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // delivery_method_id nullable -> set null
            $table->foreign('delivery_method_id', 'fk_market_quotes_method')
                ->references('id')
                ->on('market_delivery_methods')
                ->nullOnDelete();

            // provider_id nullable -> set null
            $table->foreign('provider_id', 'fk_market_quotes_provider')
                ->references('id')
                ->on('market_delivery_providers')
                ->nullOnDelete();

            // Было (ломалось из-за SET NULL на company_id/storefront_id):
            // ->nullOnDelete();
            $table->foreign(['company_id', 'storefront_id', 'provider_id'], 'fk_market_quotes_provider_enabled_tenant')
                ->references(['company_id', 'storefront_id', 'provider_id'])
                ->on('market_storefront_delivery_provider_settings')
                ->cascadeOnDelete();

            // currency nullable -> set null
            $table->foreign('currency_id', 'fk_market_quotes_currency')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            $table->comment('Маркет: кеш расчётов стоимости/сроков доставки (delivery quotes cache), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_quotes_cache');
    }
};
