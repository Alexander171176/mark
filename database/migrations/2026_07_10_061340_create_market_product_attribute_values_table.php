<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('market_product_attribute_values', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->index('market_product_attribute_values_product_id_idx')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_attribute_values_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Характеристика
             */
            $table->unsignedBigInteger('market_attribute_id')
                ->index('market_product_attribute_values_attribute_id_idx')
                ->comment('Характеристика (market_attributes.id)');

            $table->foreign(
                'market_attribute_id',
                'market_product_attribute_values_attribute_id_fk'
            )
                ->references('id')
                ->on('market_attributes')
                ->cascadeOnDelete();

            /**
             * Готовое значение характеристики из справочника
             */
            $table->unsignedBigInteger('market_attribute_value_id')
                ->nullable()
                ->index('market_product_attribute_values_value_id_idx')
                ->comment('Значение характеристики из справочника (market_attribute_values.id)');

            $table->foreign(
                'market_attribute_value_id',
                'market_product_attribute_values_value_id_fk'
            )
                ->references('id')
                ->on('market_attribute_values')
                ->nullOnDelete();

            /**
             * Ручные значения
             */
            $table->string('value_string', 500)
                ->nullable()
                ->index('market_product_attribute_values_string_idx')
                ->comment('Строковое значение характеристики');

            $table->decimal('value_number', 15, 4)
                ->nullable()
                ->index('market_product_attribute_values_number_idx')
                ->comment('Числовое значение характеристики');

            $table->boolean('value_boolean')
                ->nullable()
                ->index('market_product_attribute_values_boolean_idx')
                ->comment('Логическое значение характеристики');

            $table->date('value_date')
                ->nullable()
                ->index('market_product_attribute_values_date_idx')
                ->comment('Дата как значение характеристики');

            $table->json('value_json')
                ->nullable()
                ->comment('JSON значение характеристики');

            /**
             * Единица измерения / отображение
             */
            $table->string('unit', 50)
                ->nullable()
                ->index('market_product_attribute_values_unit_idx')
                ->comment('Единица измерения значения');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_attribute_values_order_idx')
                ->comment('Порядок отображения характеристики у товара');

            $table->boolean('activity')
                ->default(true)
                ->index('market_product_attribute_values_activity_idx')
                ->comment('Активность значения характеристики');

            $table->timestamps();

            /**
             * Один товар не должен иметь одну и ту же характеристику дважды
             */
            $table->unique(
                ['market_product_id', 'market_attribute_id'],
                'market_product_attribute_values_product_attribute_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'order'],
                'market_product_attribute_values_product_order_idx'
            );

            $table->index(
                ['market_attribute_id', 'market_attribute_value_id'],
                'market_product_attribute_values_attribute_value_idx'
            );

            $table->index(
                ['activity', 'market_attribute_id'],
                'market_product_attribute_values_activity_attribute_idx'
            );

            $table->comment(
                'Маркетплейс: значения характеристик товаров. Поддерживает справочные и ручные значения разных типов.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_attribute_values');
    }
};
