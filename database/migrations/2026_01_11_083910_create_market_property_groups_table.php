<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_property_groups', function (Blueprint $table) {
            $table->id()->comment('ID группы характеристик');

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
             * FLAGS / SORT
             * ========================================================= */

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Порядок сортировки');

            $table->boolean('activity')
                ->default(true)
                ->comment('Активность группы');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)
                ->comment('Локаль (ru/kk/en)');

            $table->string('title')
                ->comment('Название группы характеристик');

            $table->string('slug', 191)
                ->comment('Slug группы');

            $table->text('description')
                ->nullable()
                ->comment('Описание / пояснение группы');

            $table->string('note', 255)
                ->nullable()
                ->comment('Заметка администратора');

            /* =========================================================
             * UI / FILTERS
             * ========================================================= */

            $table->boolean('is_filterable')
                ->default(false)
                ->comment('Использовать группу в фильтрах');

            $table->boolean('is_collapsible')
                ->default(true)
                ->comment('Сворачиваемая группа в UI');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // slug уникален в рамках витрины + локали
            $table->unique(
                ['storefront_id', 'locale', 'slug'],
                'uq_market_property_groups_storefront_locale_slug'
            );

            /**
             * ✅ КРИТИЧНО для tenant-safe FK
             * (company_id, storefront_id, property_group_id)
             */
            $table->unique(
                ['company_id', 'storefront_id', 'id'],
                'uq_market_property_groups_tenant_id'
            );

            $table->index(
                ['company_id', 'storefront_id', 'locale'],
                'ix_market_property_groups_tenant_locale'
            );

            $table->index(
                ['storefront_id', 'locale', 'activity', 'sort'],
                'ix_market_property_groups_list'
            );

            $table->index(
                ['storefront_id', 'locale', 'activity', 'is_filterable', 'sort'],
                'ix_market_property_groups_filterable_list'
            );

            $table->index(
                ['locale'],
                'ix_market_property_groups_locale'
            );

            /* =========================================================
             * FOREIGN KEYS (В КОНЦЕ)
             * ========================================================= */

            // tenant-safe storefront
            $table->foreign(
                ['company_id', 'storefront_id'],
                'fk_market_property_groups_company_storefront'
            )
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment(
                'Маркет: группы характеристик товаров, локали независимы, tenant-safe (company + storefront)'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_property_groups');
    }
};
