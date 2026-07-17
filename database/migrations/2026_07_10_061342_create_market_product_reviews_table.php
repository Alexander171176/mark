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
        Schema::create('market_product_reviews', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->index('market_product_reviews_product_id_idx')
                ->comment('Товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_reviews_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Автор отзыва
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_product_reviews_user_id_idx')
                ->comment('Автор отзыва (users.id)');

            $table->foreign(
                'user_id',
                'market_product_reviews_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Оценка
             */
            $table->unsignedTinyInteger('rating')
                ->default(5)
                ->index('market_product_reviews_rating_idx')
                ->comment('Оценка товара (1-5)');

            /**
             * Достоинства / недостатки
             */
            $table->text('advantages')
                ->nullable()
                ->comment('Достоинства товара');

            $table->text('disadvantages')
                ->nullable()
                ->comment('Недостатки товара');

            /**
             * Текст отзыва
             */
            $table->text('comment')
                ->nullable()
                ->comment('Текст отзыва');

            /**
             * Покупатель действительно купил товар
             */
            $table->boolean('verified_purchase')
                ->default(false)
                ->index('market_product_reviews_verified_purchase_idx')
                ->comment('Подтвержденная покупка');

            $table->text('seller_reply')
                ->nullable()
                ->comment('Ответ продавца на отзыв');

            $table->timestamp('seller_reply_at')
                ->nullable()
                ->comment('Дата ответа продавца');

            /**
             * Одобрение модератором
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_product_reviews_moderation_status_idx')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_product_reviews_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign(
                'moderated_by',
                'market_product_reviews_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_product_reviews_moderated_at_idx')
                ->comment('Дата модерации');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Полезность
             */
            $table->unsignedInteger('likes')
                ->default(0)
                ->index('market_product_reviews_likes_idx')
                ->comment('Количество отметок "Полезный отзыв"');

            /**
             * Активность
             */
            $table->boolean('activity')
                ->default(true)
                ->index('market_product_reviews_activity_idx')
                ->comment('Активность отзыва');

            $table->timestamps();

            /**
             * Один пользователь = один отзыв на товар
             */
            $table->unique(
                ['market_product_id', 'user_id'],
                'market_product_reviews_product_user_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'activity', 'moderation_status'],
                'market_product_reviews_public_idx'
            );

            $table->index(
                ['market_product_id', 'rating'],
                'market_product_reviews_product_rating_idx'
            );

            $table->comment(
                'Маркетплейс: отзывы пользователей о товарах.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_reviews');
    }
};
