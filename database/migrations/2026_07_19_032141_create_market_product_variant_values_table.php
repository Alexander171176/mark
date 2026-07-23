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
        Schema::create('market_product_variant_values', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Вариант товара.
             */
            $table->unsignedBigInteger('market_product_variant_id')
                ->index('market_variant_values_variant_id_idx')
                ->comment('Вариант товара (market_product_variants.id)');

            $table->foreign(
                'market_product_variant_id',
                'market_variant_values_variant_id_fk'
            )
                ->references('id')
                ->on('market_product_variants')
                ->cascadeOnDelete();

            /**
             * Характеристика, участвующая в формировании варианта.
             *
             * Примеры:
             * - цвет;
             * - размер;
             * - объём;
             * - память.
             */
            $table->unsignedBigInteger('market_attribute_id')
                ->index('market_variant_values_attribute_id_idx')
                ->comment('Характеристика варианта (market_attributes.id)');

            $table->foreign(
                'market_attribute_id',
                'market_variant_values_attribute_id_fk'
            )
                ->references('id')
                ->on('market_attributes')
                ->cascadeOnDelete();

            /**
             * Выбранное справочное значение характеристики.
             *
             * Примеры:
             * - чёрный;
             * - XL;
             * - 500 мл;
             * - 256 ГБ.
             *
             * Переводы значения находятся в таблице
             * market_attribute_value_translations.
             */
            $table->unsignedBigInteger('market_attribute_value_id')
                ->index('market_variant_values_attribute_value_id_idx')
                ->comment('Значение характеристики (market_attribute_values.id)');

            $table->foreign(
                'market_attribute_value_id',
                'market_variant_values_attribute_value_id_fk'
            )
                ->references('id')
                ->on('market_attribute_values')
                ->cascadeOnDelete();

            /**
             * Порядок характеристики внутри варианта.
             *
             * Например:
             * 0 — цвет;
             * 1 — размер;
             * 2 — объём памяти.
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_variant_values_sort_idx')
                ->comment('Порядок отображения значения характеристики');

            $table->timestamps();

            /**
             * Одна характеристика может быть указана
             * в конкретном варианте только один раз.
             *
             * Нельзя создать вариант:
             * - цвет: чёрный;
             * - цвет: белый.
             */
            $table->unique(
                [
                    'market_product_variant_id',
                    'market_attribute_id',
                ],
                'market_variant_values_variant_attribute_unique'
            );

            /**
             * Одно значение характеристики не должно повторяться
             * внутри одного варианта.
             */
            $table->unique(
                [
                    'market_product_variant_id',
                    'market_attribute_value_id',
                ],
                'market_variant_values_variant_value_unique'
            );

            /**
             * Быстрая загрузка значений варианта
             * в установленном порядке.
             */
            $table->index(
                [
                    'market_product_variant_id',
                    'sort',
                ],
                'market_variant_values_variant_sort_idx'
            );

            /**
             * Поиск вариантов по характеристике и её значению.
             */
            $table->index(
                [
                    'market_attribute_id',
                    'market_attribute_value_id',
                ],
                'market_variant_values_attribute_value_idx'
            );

            $table->comment(
                'Маркетплейс: значения характеристик, формирующие варианты товаров.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_variant_values');
    }
};
