<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_shop_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedBigInteger('market_shop_id')
                ->comment('Магазин (market_shops.id)');

            $table->foreign('market_shop_id', 'market_shop_translations_shop_id_fk')
                ->references('id')
                ->on('market_shops')
                ->cascadeOnDelete();

            $table->string('locale', 10)
                ->index('market_shop_translations_locale_idx')
                ->comment('Локаль перевода');

            $table->string('title', 255)
                ->comment('Название магазина');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок или слоган магазина');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание магазина');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание магазина');

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

            $table->unique(
                ['market_shop_id', 'locale'],
                'market_shop_translations_shop_locale_unique'
            );

            $table->index(
                ['locale', 'title'],
                'market_shop_translations_locale_title_idx'
            );

            $table->comment('Маркетплейс: переводы магазинов.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_shop_translations');
    }
};
