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
        Schema::create('market_product_translations', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Связь с товаром
             */
            $table->unsignedBigInteger('market_product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_translations_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Локаль
             */
            $table->string('locale', 10)
                ->index('market_product_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название товара');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок товара');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание товара');

            $table->longText('description')
                ->nullable()
                ->comment('Полное описание товара');

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
             * Один товар = один перевод на одну локаль
             */
            $table->unique(
                ['market_product_id', 'locale'],
                'market_product_translations_product_locale_unique'
            );

            /**
             * Быстрый поиск
             */
            $table->index(
                ['locale', 'title'],
                'market_product_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы товаров по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_translations');
    }
};
