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
        Schema::create('market_product_likes', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Пользователь
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_product_likes_user_id_idx')
                ->comment('Пользователь (users.id)');

            $table->foreign(
                'user_id',
                'market_product_likes_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->index('market_product_likes_product_id_idx')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_likes_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            $table->timestamps();

            /**
             * Один пользователь может поставить лайк товару только один раз
             */
            $table->unique(
                ['user_id', 'market_product_id'],
                'market_product_likes_user_product_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'created_at'],
                'market_product_likes_product_created_idx'
            );

            $table->index(
                ['user_id', 'created_at'],
                'market_product_likes_user_created_idx'
            );

            $table->comment(
                'Маркетплейс: лайки товаров пользователями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_likes');
    }
};
