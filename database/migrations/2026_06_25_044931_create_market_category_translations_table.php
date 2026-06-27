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
        Schema::create('market_category_translations', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Связь с категорией
             */
            $table->unsignedBigInteger('market_category_id')
                ->comment('Категория маркетплейса (market_categories.id)');

            $table->foreign(
                'market_category_id',
                'market_category_translations_category_id_fk'
            )
                ->references('id')
                ->on('market_categories')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_category_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название категории');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок категории');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание категории');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание категории');

            /**
             * SEO
             */
            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO: meta title');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO: meta keywords');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO: meta description');

            $table->timestamps();

            /**
             * Одна категория = один перевод на одну локаль
             */
            $table->unique(
                ['market_category_id', 'locale'],
                'market_category_translations_category_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'market_category_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы общих категорий товаров по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_category_translations');
    }
};
