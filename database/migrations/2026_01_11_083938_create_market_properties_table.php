<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_properties', function (Blueprint $table) {
            $table->id()->comment('ID характеристики (свойства)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * GROUP (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('property_group_id')
                ->comment('Группа характеристик (market_property_groups.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность характеристики');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en)');

            $table->string('title')->comment('Название характеристики');
            $table->string('slug', 191)->comment('Slug характеристики');

            /* =========================================================
             * TYPES
             * ========================================================= */

            $table->string('value_type', 20)
                ->default('string')
                ->comment('string|text|int|decimal|bool|date');

            $table->string('filter_type', 50)
                ->default('checkbox')
                ->comment('checkbox|select|text|number|range|bool');

            /* =========================================================
             * UI FLAGS
             * ========================================================= */

            $table->boolean('is_filterable')->default(false)->comment('Использовать как фильтр в каталоге');
            $table->boolean('is_variant_axis')->default(false)->comment('Формирует варианты (цвет/размер)');

            $table->boolean('is_required')->default(false)->comment('Обязательное для заполнения');
            $table->boolean('is_visible')->default(true)->comment('Показывать в карточке товара');
            $table->boolean('is_searchable')->default(false)->comment('Участвует в поиске');

            $table->string('unit', 30)->nullable()->comment('Единица измерения (кг/мм/Вт)');

            /* =========================================================
             * RANGE META (optional)
             * ========================================================= */

            $table->decimal('min_value', 20, 6)->nullable()->comment('Мин. значение (для range/number)');
            $table->decimal('max_value', 20, 6)->nullable()->comment('Макс. значение (для range/number)');
            $table->decimal('step', 20, 6)->nullable()->comment('Шаг (для range/number)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique(
                ['storefront_id', 'locale', 'slug'],
                'uq_market_properties_storefront_locale_slug'
            );

            /**
             * ✅ Критично для будущих tenant-safe pivot'ов
             * (company_id, storefront_id, property_id) -> (company_id, storefront_id, id)
             */
            $table->unique(
                ['company_id', 'storefront_id', 'id'],
                'uq_market_properties_tenant_id'
            );

            $table->index(
                ['property_group_id', 'activity', 'sort'],
                'ix_market_properties_group_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'locale'],
                'ix_market_properties_tenant_locale'
            );

            $table->index(
                ['storefront_id', 'locale', 'activity', 'sort'],
                'ix_market_properties_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'property_group_id'],
                'ix_market_properties_tenant_group'
            );

            $table->index(
                ['storefront_id', 'locale', 'activity', 'is_filterable', 'sort'],
                'ix_market_properties_filterable_list'
            );

            $table->index(['locale'], 'ix_market_properties_locale');

            /* =========================================================
             * FOREIGN KEYS (В КОНЦЕ)
             * ========================================================= */

            $table->foreign('company_id', 'fk_market_properties_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_properties_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * tenant-safe: группа обязана быть в рамках того же company/storefront
             * Требование: в market_property_groups есть UNIQUE(company_id, storefront_id, id)
             */
            $table->foreign(['company_id', 'storefront_id', 'property_group_id'], 'fk_market_properties_tenant_group')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_property_groups')
                ->cascadeOnDelete();

            $table->comment('Маркет: характеристики (свойства), локали независимы, tenant-safe (company + storefront)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_properties');
    }
};
