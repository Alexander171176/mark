<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_method_has_pickup_points', function (Blueprint $table) {

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

            $table->unsignedBigInteger('pickup_point_id')
                ->comment('Пункт выдачи (market_pickup_points.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка в рамках метода');

            // Опционально: переопределение стоимости/сроков для конкретного ПВЗ
            $table->decimal('price_override', 18, 2)->nullable()->comment('Цена доставки до ПВЗ');
            $table->unsignedSmallInteger('min_days')->nullable()->comment('Мин. срок');
            $table->unsignedSmallInteger('max_days')->nullable()->comment('Макс. срок');

            $table->timestamps();

            /* =========================================================
             * PRIMARY / UNIQUE / INDEXES
             * ========================================================= */

            // ✅ Единый подход: PK включает storefront_id (tenant-safe)
            $table->primary(
                ['storefront_id', 'delivery_method_id', 'pickup_point_id'],
                'pk_mdmhpp_storefront_method_pickup'
            );

            // (доп. защита, если когда-то захочешь PK поменять — можно оставить)
            $table->unique(
                ['storefront_id', 'delivery_method_id', 'pickup_point_id'],
                'uq_mdmhpp_storefront_method_pickup'
            );

            // Tenant индексы
            $table->index(['company_id', 'storefront_id'], 'ix_mdmhpp_tenant');

            // Быстрый список ПВЗ метода
            $table->index(['delivery_method_id', 'activity', 'sort'], 'ix_mdmhpp_method_list');

            // Быстрый список методов для ПВЗ
            $table->index(['pickup_point_id', 'activity'], 'ix_mdmhpp_pickup_methods');

            // Ускорение tenant-join'ов
            $table->index(['company_id', 'storefront_id', 'delivery_method_id'], 'ix_mdmhpp_tenant_method');
            $table->index(['company_id', 'storefront_id', 'pickup_point_id'], 'ix_mdmhpp_tenant_pickup');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mdmhpp_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mdmhpp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // ✅ tenant-safe: метод доставки обязан быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'delivery_method_id'], 'fk_mdmhpp_method_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_delivery_methods')
                ->cascadeOnDelete();

            // ✅ tenant-safe: ПВЗ обязан быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'pickup_point_id'], 'fk_mdmhpp_pickup_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_pickup_points')
                ->cascadeOnDelete();

            $table->comment('Маркет: доступные пункты выдачи для методов доставки (M:N), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_method_has_pickup_points');
    }
};
