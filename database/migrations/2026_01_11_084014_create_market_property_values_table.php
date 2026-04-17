<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_property_values', function (Blueprint $table) {
            $table->id()->comment('ID значения характеристики');

            /* =========================================================
             * TENANT / STOREFRONT
             * (единый подход как в остальных сущностях)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * PROPERTY (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('property_id')
                ->comment('Характеристика (market_properties.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность значения');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en)');

            $table->string('title')->comment('Название значения (например: Черный)');
            $table->string('slug', 191)->comment('Slug значения (для URL/фильтров, стабильный ключ)');

            // “Сырое” значение (может быть текст, число, код, boolean-представление и т.п.)
            $table->string('value', 255)->nullable()->comment('Сырое значение (например: black, 128, true)');

            $table->boolean('is_default')->default(false)->comment('Значение по умолчанию');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Уникальность в рамках свойства и локали
            $table->unique(
                ['property_id', 'locale', 'slug'],
                'uq_market_property_values_property_locale_slug'
            );

            /**
             * ✅ Критично для tenant-safe pivot'ов в будущем:
             * (company_id, storefront_id, value_id) -> (company_id, storefront_id, id)
             */
            $table->unique(
                ['company_id', 'storefront_id', 'id'],
                'uq_market_property_values_tenant_id'
            );

            $table->index(
                ['company_id', 'storefront_id'],
                'ix_market_property_values_tenant'
            );

            $table->index(
                ['property_id', 'locale'],
                'ix_market_property_values_property_locale'
            );

            $table->index(
                ['property_id', 'locale', 'activity', 'sort'],
                'ix_market_property_values_list'
            );

            $table->index(
                ['property_id', 'value'],
                'ix_market_property_values_property_value'
            );

            $table->index(
                ['locale'],
                'ix_market_property_values_locale'
            );

            /* =========================================================
             * FOREIGN KEYS (В КОНЦЕ)
             * ========================================================= */

            $table->foreign('company_id', 'fk_market_property_values_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_property_values_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * tenant-safe: значение обязано принадлежать свойству из этой же company+storefront
             * Требование: в market_properties есть UNIQUE(company_id, storefront_id, id)
             */
            $table->foreign(['company_id', 'storefront_id', 'property_id'], 'fk_market_property_values_tenant_property')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_properties')
                ->cascadeOnDelete();

            $table->comment('Маркет: значения характеристик (для select/checkbox), локали независимы, tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_property_values');
    }
};
