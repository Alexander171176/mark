<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_product_variant_has_property_values', function (Blueprint $table) {
            $table->id()->comment('ID значения характеристики у варианта');

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
             * LINKS
             * ========================================================= */

            $table->unsignedBigInteger('product_variant_id')
                ->comment('Вариант товара (market_product_variants.id)');

            $table->unsignedBigInteger('property_id')
                ->comment('Характеристика (market_properties.id)');

            $table->unsignedBigInteger('property_value_id')
                ->nullable()
                ->comment('Значение (market_property_values.id), если используется справочник значений');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->unsignedInteger('sort')->default(0)->comment('Сортировка значения');
            $table->boolean('activity')->default(true)->comment('Активность значения у варианта');

            // значение (одно из полей, по value_type свойства)
            $table->text('value_text')->nullable()->comment('Текстовое значение (value_type=text|string)');
            $table->decimal('value_number', 18, 6)->nullable()->comment('Числовое значение (value_type=int|decimal)');
            $table->boolean('value_bool')->nullable()->comment('Булево значение (value_type=bool)');
            $table->date('value_date')->nullable()->comment('Дата (value_type=date)');

            $table->timestamps();

            /* =========================================================
             * KEYS / INDEXES (сначала ключи, потом FK)
             * ========================================================= */

            // tenant-safe уникальность: 1 значение на 1 свойство в рамках варианта (в пределах витрины)
            $table->unique(
                ['company_id', 'storefront_id', 'product_variant_id', 'property_id'],
                'uq_mvpv_tenant_variant_property'
            );

            $table->index(['company_id', 'storefront_id'], 'ix_mvpv_tenant');
            $table->index(['company_id', 'storefront_id', 'product_variant_id', 'activity'], 'ix_mvpv_variant_active');

            // фильтрация
            $table->index(['company_id', 'storefront_id', 'property_id'], 'ix_mvpv_property');
            $table->index(['company_id', 'storefront_id', 'property_id', 'property_value_id'], 'ix_mvpv_property_value');

            $table->index(['property_value_id'], 'ix_mvpv_property_value_id');
            $table->index(['property_id', 'value_number'], 'ix_mvpv_property_number');
            $table->index(['property_id', 'value_bool'], 'ix_mvpv_property_bool');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mvpv_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // ✅ variant обязан быть из этого company+storefront
            $table->foreign(['company_id', 'storefront_id', 'product_variant_id'], 'fk_mvpv_variant_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_product_variants')
                ->cascadeOnDelete();

            // ✅ property обязан быть из этого company+storefront
            $table->foreign(['company_id', 'storefront_id', 'property_id'], 'fk_mvpv_property_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_properties')
                ->cascadeOnDelete();

            // ✅ property_value: если справочник удалили — удаляем связку (без "мертвых" ссылок)
            // Поле nullable только потому что может быть режим без справочника (value_* поля)
            $table->foreign('property_value_id', 'fk_mvpv_prop_value')
                ->references('id')
                ->on('market_property_values')
                ->cascadeOnDelete();

            $table->comment('Маркет: значения характеристик у варианта товара (tenant-safe, единый подход cascade)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_product_variant_has_property_values');
    }
};
