<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_recently_viewed_products', function (Blueprint $table) {
            $table->id();

            /** Пользователь, который просмотрел товар */
            $table->foreignId('user_id')
                ->comment('ID пользователя')
                ->constrained('users')
                ->cascadeOnDelete();

            /** Просмотренный товар */
            $table->foreignId('market_product_id')
                ->comment('ID просмотренного товара')
                ->constrained('market_products')
                ->cascadeOnDelete();

            /** Дата и время последнего просмотра товара */
            $table->timestamp('viewed_at')
                ->useCurrent()
                ->comment('Дата и время последнего просмотра товара');

            $table->timestamps();

            /** Один товар хранится в истории пользователя только один раз */
            $table->unique(
                ['user_id', 'market_product_id'],
                'market_recently_viewed_user_product_unique'
            );

            /** Быстрая выборка последних просмотренных товаров пользователя */
            $table->index(
                ['user_id', 'viewed_at'],
                'market_recently_viewed_user_date_index'
            );

            /** Индекс для выборок истории конкретного товара */
            $table->index(
                ['market_product_id', 'viewed_at'],
                'market_recently_viewed_product_date_index'
            );

            $table->comment(
                'История недавно просмотренных товаров авторизованных пользователей маркетплейса'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_recently_viewed_products');
    }
};
