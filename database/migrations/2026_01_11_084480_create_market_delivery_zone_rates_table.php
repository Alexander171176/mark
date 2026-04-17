<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_zone_rates', function (Blueprint $table) {
            $table->id()->comment('ID тарифа доставки по зоне');

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

            $table->unsignedBigInteger('delivery_zone_id')
                ->comment('Зона доставки (market_delivery_zones.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность тарифа');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            // Если null — можно брать base_price из метода
            $table->decimal('price', 18, 2)->nullable()->comment('Цена доставки в зоне');

            $table->foreignId('currency_id')
                ->comment('Валюта стоимости (currencies.id)');

            $table->decimal('free_from_total', 18, 2)->nullable()->comment('Бесплатно от суммы');

            $table->unsignedSmallInteger('min_days')->nullable()->comment('Мин. срок (дней)');
            $table->unsignedSmallInteger('max_days')->nullable()->comment('Макс. срок (дней)');

            $table->decimal('min_order_total', 18, 2)->nullable()->comment('Минимальная сумма');
            $table->decimal('min_weight', 18, 6)->nullable()->comment('Мин. вес');
            $table->decimal('max_weight', 18, 6)->nullable()->comment('Макс. вес');

            $table->string('note', 255)->nullable()->comment('Заметка');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ 1 тариф на пару (method, zone) в рамках витрины
            $table->unique(
                ['storefront_id', 'delivery_method_id', 'delivery_zone_id'],
                'uq_mdzr_storefront_method_zone'
            );

            // ✅ tenant-safe ключ под композитные FK (единый паттерн)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mdzr_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mdzr_tenant');

            $table->index(['delivery_method_id', 'activity', 'sort'], 'ix_mdzr_method_list');
            $table->index(['delivery_zone_id', 'activity', 'sort'], 'ix_mdzr_zone_list');

            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mdzr_storefront_list');

            // ✅ ускоряем tenant-safe join
            $table->index(['company_id', 'storefront_id', 'delivery_method_id'], 'ix_mdzr_tenant_method');
            $table->index(['company_id', 'storefront_id', 'delivery_zone_id'], 'ix_mdzr_tenant_zone');

            $table->index(['currency_id'], 'ix_mdzr_currency');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mdzr_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mdzr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe: метод обязан быть в рамках company+storefront
            $table->foreign(['company_id', 'storefront_id', 'delivery_method_id'], 'fk_mdzr_method_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_delivery_methods')
                ->cascadeOnDelete();

            // tenant-safe: зона обязана быть в рамках company+storefront
            $table->foreign(['company_id', 'storefront_id', 'delivery_zone_id'], 'fk_mdzr_zone_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_delivery_zones')
                ->cascadeOnDelete();

            $table->foreign('currency_id', 'fk_mdzr_currency')
                ->references('id')
                ->on('currencies')
                ->restrictOnDelete();

            $table->comment('Маркет: тарифы доставки по зонам (tenant-safe: метод + зона + цена + сроки)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_zone_rates');
    }
};
