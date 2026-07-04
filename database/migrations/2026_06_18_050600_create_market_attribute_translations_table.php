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
        Schema::create('market_attribute_translations', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Связь с характеристикой
             */
            $table->unsignedBigInteger('market_attribute_id')
                ->comment('Характеристика (market_attributes.id)');

            $table->foreign(
                'market_attribute_id',
                'market_attribute_translations_attribute_id_fk'
            )
                ->references('id')
                ->on('market_attributes')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_attribute_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название характеристики');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок характеристики');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание характеристики');

            $table->text('description')
                ->nullable()
                ->comment('Описание характеристики');

            $table->timestamps();

            /**
             * Одна характеристика = один перевод на одну локаль
             */
            $table->unique(
                ['market_attribute_id', 'locale'],
                'market_attribute_translations_attribute_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'market_attribute_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы характеристик по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attribute_translations');
    }
};
