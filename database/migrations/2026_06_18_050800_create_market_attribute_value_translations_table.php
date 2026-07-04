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
        Schema::create('market_attribute_value_translations', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Связь со значением характеристики
             */
            $table->unsignedBigInteger('market_attribute_value_id')
                ->comment('Значение характеристики (market_attribute_values.id)');

            $table->foreign(
                'market_attribute_value_id',
                'market_attribute_value_translations_value_id_fk'
            )
                ->references('id')
                ->on('market_attribute_values')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_attribute_value_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название значения характеристики');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок значения характеристики');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание значения характеристики');

            $table->text('description')
                ->nullable()
                ->comment('Описание значения характеристики');

            $table->timestamps();

            /**
             * Одно значение = один перевод на одну локаль
             */
            $table->unique(
                ['market_attribute_value_id', 'locale'],
                'market_attribute_value_translations_value_locale_unique'
            );

            /**
             * Быстрый поиск
             */
            $table->index(
                ['locale', 'title'],
                'market_attribute_value_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы значений характеристик по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attribute_value_translations');
    }
};
