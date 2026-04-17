<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_category_has_properties', function (Blueprint $table) {

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * ENTITIES
             * ========================================================= */

            $table->unsignedBigInteger('category_id')
                ->comment('Категория (market_categories.id)');

            $table->unsignedBigInteger('property_id')
                ->comment('Характеристика (market_properties.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->unsignedInteger('sort')->default(0)
                ->comment('Сортировка характеристики в категории');

            $table->boolean('activity')->default(false)
                ->comment('Активность характеристики в категории');

            $table->boolean('is_required')->default(false)
                ->comment('Обязательная характеристика для категории');

            $table->boolean('is_filterable')->default(false)
                ->comment('Показывать в фильтрах (в рамках категории)');

            $table->boolean('is_visible')->default(true)
                ->comment('Показывать в карточке товара (в рамках категории)');

            $table->timestamps();

            /* =========================================================
             * KEYS / INDEXES (сначала индексы/ключи)
             * ========================================================= */

            // ✅ Единый подход: PK включает tenant, чтобы не было дублей между витринами
            $table->primary(
                ['company_id', 'storefront_id', 'category_id', 'property_id'],
                'pk_mchprop_tenant_category_property'
            );

            // Быстрые выборки
            $table->index(['company_id', 'storefront_id'], 'ix_mchprop_tenant');
            $table->index(['company_id', 'storefront_id', 'category_id', 'activity', 'sort'], 'ix_mchprop_category_list');
            $table->index(['company_id', 'storefront_id', 'property_id'], 'ix_mchprop_property');

            // Доп. индексы под админку/фильтры
            $table->index(['storefront_id', 'category_id'], 'ix_mchprop_storefront_category');
            $table->index(['storefront_id', 'property_id'], 'ix_mchprop_storefront_property');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mchprop_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // ✅ Категория обязана быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'category_id'], 'fk_mchprop_category_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_categories')
                ->cascadeOnDelete();

            // ✅ Характеристика обязана быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'property_id'], 'fk_mchprop_property_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_properties')
                ->cascadeOnDelete();

            $table->comment('Маркет: свойства категории (M:N), tenant-safe, единый подход cascadeOnDelete, без soft deletes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_category_has_properties');
    }
};
