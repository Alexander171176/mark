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
        Schema::create('market_product_has_categories', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_has_categories_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Категория
             */
            $table->unsignedBigInteger('market_category_id')
                ->comment('Категория товара (market_categories.id)');

            $table->foreign(
                'market_category_id',
                'market_product_has_categories_category_id_fk'
            )
                ->references('id')
                ->on('market_categories')
                ->cascadeOnDelete();

            /**
             * Главная категория товара
             */
            $table->boolean('main')
                ->default(false)
                ->index('market_product_has_categories_main_idx')
                ->comment('Главная категория товара');

            /**
             * Сортировка категорий у товара
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_has_categories_order_idx')
                ->comment('Порядок отображения категории');

            $table->timestamps();

            /**
             * Один товар не может иметь одну и ту же категорию дважды
             */
            $table->unique(
                ['market_product_id', 'market_category_id'],
                'market_product_has_categories_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'order'],
                'market_product_has_categories_product_order_idx'
            );

            $table->index(
                ['market_category_id', 'order'],
                'market_product_has_categories_category_order_idx'
            );

            $table->comment(
                'Маркетплейс: связь товаров с категориями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_has_categories');
    }
};
