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
        Schema::create('market_tag_translations', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Связь с тегом
             */
            $table->unsignedBigInteger('market_tag_id')
                ->comment('Тег маркетплейса (market_tags.id)');

            $table->foreign(
                'market_tag_id',
                'market_tag_translations_tag_id_fk'
            )
                ->references('id')
                ->on('market_tags')
                ->cascadeOnDelete();

            /**
             * Локаль перевода
             */
            $table->string('locale', 10)
                ->index('market_tag_translations_locale_idx')
                ->comment('Локаль перевода: ru, kk, en');

            /**
             * Локализуемые поля
             */
            $table->string('title', 255)
                ->comment('Название тега');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок тега');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание тега');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание тега');

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
             * Один тег = один перевод на одну локаль
             */
            $table->unique(
                ['market_tag_id', 'locale'],
                'market_tag_translations_tag_locale_unique'
            );

            /**
             * Быстрый поиск по локали и названию
             */
            $table->index(
                ['locale', 'title'],
                'market_tag_translations_locale_title_idx'
            );

            $table->comment(
                'Маркетплейс: переводы тегов товаров по локалям.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_tag_translations');
    }
};
