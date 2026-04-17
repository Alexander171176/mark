<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_zones', function (Blueprint $table) {

            /* =========================================================
             * BASE
             * ========================================================= */

            $table->id()->comment('ID зоны доставки');

            /* =========================================================
             * TENANT / STOREFRONT (поля)
             * ========================================================= */

            $table->unsignedBigInteger('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность зоны');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en)');

            $table->string('title', 255)->comment('Название зоны (например: Алматы)');
            $table->string('slug', 191)->comment('Slug зоны');

            $table->string('type', 32)
                ->default('city')
                ->comment('Тип зоны: city/region/custom/postcode');

            /* =========================================================
             * ADDRESS FILTER (опционально)
             * ========================================================= */

            $table->string('country_code', 2)->nullable()->comment('ISO2 страны (KZ/...)');
            $table->string('region', 128)->nullable()->comment('Регион/область');
            $table->string('city', 128)->nullable()->comment('Город');

            $table->string('postcode_from', 16)->nullable()->comment('Индекс от');
            $table->string('postcode_to', 16)->nullable()->comment('Индекс до');

            $table->text('note')->nullable()->comment('Заметка/описание зоны');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // slug уникален в рамках витрины и локали
            $table->unique(
                ['storefront_id', 'locale', 'slug'],
                'uq_mdz_storefront_locale_slug'
            );

            /**
             * ✅ Критично для tenant-safe FK из других таблиц:
             * (company_id, storefront_id, delivery_zone_id) -> (company_id, storefront_id, id)
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mdz_tenant_id');

            /**
             * ✅ Опционально (полезно, если где-то FK только по витрине):
             * (storefront_id, delivery_zone_id) -> (storefront_id, id)
             */
            $table->unique(['storefront_id', 'id'], 'uq_mdz_storefront_id');

            // Списки/вывод
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mdz_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mdz_list');

            // Быстрые проверки/поиск подходящей зоны
            $table->index(['storefront_id', 'locale', 'type', 'activity'], 'ix_mdz_type_active');
            $table->index(['storefront_id', 'locale', 'country_code', 'city'], 'ix_mdz_city_lookup');
            $table->index(['storefront_id', 'locale', 'postcode_from', 'postcode_to'], 'ix_mdz_postcode_range');

            $table->index('locale', 'ix_mdz_locale');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mdz_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // tenant-safe: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mdz_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment('Маркет: зоны доставки (город/регион/кастом/индексы), локали независимы');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_zones');
    }
};
