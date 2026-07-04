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
        Schema::create('market_attributables', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Характеристика
             */
            $table->unsignedBigInteger('market_attribute_id')
                ->index('market_attributables_attribute_id_idx')
                ->comment('Характеристика (market_attributes.id)');

            $table->foreign(
                'market_attribute_id',
                'market_attributables_attribute_id_fk'
            )
                ->references('id')
                ->on('market_attributes')
                ->cascadeOnDelete();

            /**
             * Значение из справочника
             */
            $table->unsignedBigInteger('market_attribute_value_id')
                ->nullable()
                ->index('market_attributables_attribute_value_id_idx')
                ->comment('Значение характеристики (market_attribute_values.id)');

            $table->foreign(
                'market_attribute_value_id',
                'market_attributables_attribute_value_id_fk'
            )
                ->references('id')
                ->on('market_attribute_values')
                ->nullOnDelete();

            /**
             * Собственное значение
             */
            $table->text('value')
                ->nullable()
                ->comment('Свободное значение характеристики');

            /**
             * Полиморфная связь
             */
            $table->string('attributable_type', 255)
                ->comment('Тип сущности');

            $table->unsignedBigInteger('attributable_id')
                ->comment('ID сущности');

            /**
             * Локаль значения
             */
            $table->string('locale', 10)
                ->nullable()
                ->index('market_attributables_locale_idx')
                ->comment('Локаль значения, если характеристика зависит от языка');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_attributables_sort_idx')
                ->comment('Сортировка');

            $table->timestamps();

            /**
             * Индексы
             */
            $table->index(
                ['attributable_type', 'attributable_id'],
                'market_attributables_entity_idx'
            );

            $table->index(
                [
                    'market_attribute_id',
                    'attributable_type',
                    'attributable_id',
                ],
                'market_attributables_attribute_entity_idx'
            );

            $table->index(
                [
                    'market_attribute_id',
                    'market_attribute_value_id',
                    'attributable_type',
                    'attributable_id',
                    'locale',
                ],
                'market_attributables_full_lookup_idx'
            );

            $table->comment(
                'Маркетплейс: универсальная связь характеристик с любыми сущностями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attributables');
    }
};
