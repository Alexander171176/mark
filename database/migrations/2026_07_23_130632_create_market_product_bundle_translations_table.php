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
        Schema::create('market_product_bundle_translations', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Связь с комплектом товаров
             */
            $table->unsignedBigInteger('market_product_bundle_id')
                ->index('market_product_bundle_translations_bundle_id_idx')
                ->comment('Комплект товаров (market_product_bundles.id)');

            $table->foreign(
                'market_product_bundle_id',
                'market_product_bundle_translations_bundle_id_fk'
            )
                ->references('id')
                ->on('market_product_bundles')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             *
             * Примеры:
             * - ru;
             * - kk;
             * - en;
             * - zh.
             */
            $table->string('locale', 10)
                ->index('market_product_bundle_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en, zh');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название комплекта товаров');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок комплекта товаров');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание комплекта товаров');

            $table->longText('description')
                ->nullable()
                ->comment('Полное описание комплекта товаров');

            /**
             * SEO
             */
            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO-заголовок комплекта товаров');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO-ключевые слова комплекта товаров');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO-описание комплекта товаров');

            $table->timestamps();

            /**
             * Один комплект может иметь
             * только один перевод на одну локаль.
             */
            $table->unique(
                [
                    'market_product_bundle_id',
                    'locale',
                ],
                'market_product_bundle_translations_bundle_locale_unique'
            );

            /**
             * Быстрый поиск комплектов
             * по локали и переведённому названию.
             */
            $table->index(
                [
                    'locale',
                    'title',
                ],
                'market_product_bundle_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: мультиязычные данные комплектов товаров.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_bundle_translations');
    }
};
