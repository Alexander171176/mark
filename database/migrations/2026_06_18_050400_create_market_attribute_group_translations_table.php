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
        Schema::create('market_attribute_group_translations', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Связь с группой характеристик
             */
            $table->unsignedBigInteger('market_attribute_group_id')
                ->comment('Группа характеристик (market_attribute_groups.id)');

            $table->foreign(
                'market_attribute_group_id',
                'market_attribute_group_translations_group_id_fk'
            )
                ->references('id')
                ->on('market_attribute_groups')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_attribute_group_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название группы характеристик');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок группы характеристик');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание группы характеристик');

            $table->timestamps();

            /**
             * Одна группа = один перевод на одну локаль
             */
            $table->unique(
                ['market_attribute_group_id', 'locale'],
                'market_attribute_group_translations_group_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'market_attribute_group_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы групп характеристик по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attribute_group_translations');
    }
};
