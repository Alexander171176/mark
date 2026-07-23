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
        Schema::create('market_product_variant_translations', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Вариант товара.
             */
            $table->unsignedBigInteger('market_product_variant_id')
                ->index('market_variant_translations_variant_id_idx')
                ->comment('Вариант товара (market_product_variants.id)');

            $table->foreign(
                'market_product_variant_id',
                'market_variant_translations_variant_id_fk'
            )
                ->references('id')
                ->on('market_product_variants')
                ->cascadeOnDelete();

            /**
             * Локаль перевода.
             *
             * Примеры:
             * - ru;
             * - en;
             * - kk;
             * - zh.
             */
            $table->string('locale', 10)
                ->index('market_variant_translations_locale_idx')
                ->comment('Локаль перевода');

            /**
             * Переводимые данные варианта.
             */
            $table->string('title', 255)
                ->comment('Название варианта');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок варианта');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание варианта');

            $table->longText('description')
                ->nullable()
                ->comment('Полное описание варианта');

            /**
             * SEO.
             */
            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO-заголовок варианта');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO-ключевые слова варианта');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO-описание варианта');

            $table->timestamps();

            /**
             * Один перевод каждой локали для одного варианта.
             */
            $table->unique(
                [
                    'market_product_variant_id',
                    'locale',
                ],
                'market_variant_translations_variant_locale_unique'
            );

            /**
             * Поиск вариантов по переведённому названию.
             */
            $table->index(
                [
                    'locale',
                    'title',
                ],
                'market_variant_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: мультиязычные данные вариантов товаров.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_variant_translations');
    }
};
