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
        Schema::create('market_brand_translations', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Связь с брендом
             */
            $table->unsignedBigInteger('market_brand_id')
                ->comment('Бренд (market_brands.id)');

            $table->foreign(
                'market_brand_id',
                'market_brand_translations_brand_id_fk'
            )
                ->references('id')
                ->on('market_brands')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_brand_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название бренда');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок или слоган бренда');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание бренда');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание бренда');

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
             * Один бренд = один перевод на одну локаль
             */
            $table->unique(
                ['market_brand_id', 'locale'],
                'market_brand_translations_brand_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'market_brand_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы брендов товаров по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_brand_translations');
    }
};
