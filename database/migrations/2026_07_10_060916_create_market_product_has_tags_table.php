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
        Schema::create('market_product_has_tags', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_has_tags_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Тег
             */
            $table->unsignedBigInteger('market_tag_id')
                ->comment('Тег товара (market_tags.id)');

            $table->foreign(
                'market_tag_id',
                'market_product_has_tags_tag_id_fk'
            )
                ->references('id')
                ->on('market_tags')
                ->cascadeOnDelete();

            /**
             * Порядок отображения тегов
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_has_tags_order_idx')
                ->comment('Порядок отображения тега');

            $table->timestamps();

            /**
             * Один товар не может иметь одинаковый тег дважды
             */
            $table->unique(
                ['market_product_id', 'market_tag_id'],
                'market_product_has_tags_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'order'],
                'market_product_has_tags_product_order_idx'
            );

            $table->index(
                ['market_tag_id', 'order'],
                'market_product_has_tags_tag_order_idx'
            );

            $table->comment(
                'Маркетплейс: связь товаров с тегами.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_has_tags');
    }
};
