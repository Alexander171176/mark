<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_zone_has_addresses', function (Blueprint $table) {

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

            $table->unsignedBigInteger('delivery_zone_id')
                ->comment('Зона доставки (market_delivery_zones.id)');

            // Глобальный справочник адресов
            $table->unsignedBigInteger('address_id')
                ->comment('Адрес из справочника (market_address_dictionary.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность привязки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри зоны');

            $table->boolean('is_excluded')->default(false)->comment('Исключить адрес из зоны');

            $table->timestamps();

            /* =========================================================
             * PRIMARY / INDEXES
             * ========================================================= */

            // ✅ tenant-safe PK (полная изоляция ключа)
            $table->primary(
                ['company_id', 'storefront_id', 'delivery_zone_id', 'address_id'],
                'pk_market_zone_has_addresses'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mdzha_tenant');

            // список адресов зоны
            $table->index(['delivery_zone_id', 'activity', 'sort'], 'ix_mdzha_zone_list');

            // список адресов зоны (tenant-safe join)
            $table->index(
                ['company_id', 'storefront_id', 'delivery_zone_id', 'activity', 'sort'],
                'ix_mdzha_tenant_zone_list'
            );

            // “в каких зонах состоит адрес” в пределах витрины/компании
            $table->index(['company_id', 'storefront_id', 'address_id', 'activity'], 'ix_mdzha_tenant_address_active');

            $table->index(['address_id'], 'ix_mdzha_address');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mdzha_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mdzha_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe: зона обязана быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'delivery_zone_id'], 'fk_mdzha_zone_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_delivery_zones')
                ->cascadeOnDelete();

            // глобальный справочник (безопаснее restrict)
            $table->foreign('address_id', 'fk_mdzha_address')
                ->references('id')
                ->on('market_address_dictionary')
                ->restrictOnDelete();

            $table->comment('Маркет: адреса, входящие в зону доставки (M:N), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_zone_has_addresses');
    }
};
