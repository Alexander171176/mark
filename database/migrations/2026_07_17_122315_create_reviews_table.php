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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Полиморфная сущность отзыва.
             *
             * Примеры reviewable_type:
             * - market_product
             * - market_bundle
             * - school_course
             *
             * Значения типов позднее зарегистрируем через morphMap.
             */
            $table->string('reviewable_type', 100)
                ->index('reviews_reviewable_type_idx')
                ->comment('Тип сущности, к которой относится отзыв');

            $table->unsignedBigInteger('reviewable_id')
                ->index('reviews_reviewable_id_idx')
                ->comment('ID сущности, к которой относится отзыв');

            /**
             * Автор отзыва.
             */
            $table->unsignedBigInteger('user_id')
                ->index('reviews_user_id_idx')
                ->comment('Автор отзыва (users.id)');

            $table->foreign(
                'user_id',
                'reviews_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Оценка.
             */
            $table->unsignedTinyInteger('rating')
                ->default(5)
                ->index('reviews_rating_idx')
                ->comment('Оценка сущности от 1 до 5');

            /**
             * Содержимое отзыва.
             */
            $table->text('advantages')
                ->nullable()
                ->comment('Преимущества или достоинства');

            $table->text('disadvantages')
                ->nullable()
                ->comment('Недостатки');

            $table->text('comment')
                ->nullable()
                ->comment('Основной текст отзыва');

            /**
             * Подтверждённый опыт взаимодействия.
             *
             * Для товара — подтверждённая покупка.
             * Для курса — подтверждённое обучение.
             * Для комплекта — подтверждённый заказ.
             */
            $table->boolean('verified')
                ->default(false)
                ->index('reviews_verified_idx')
                ->comment('Подтверждённый опыт пользователя');

            /**
             * Ответ владельца сущности.
             *
             * Это может быть:
             * - продавец товара;
             * - владелец магазина;
             * - преподаватель курса;
             * - администратор.
             */
            $table->text('reply')
                ->nullable()
                ->comment('Ответ владельца сущности или администратора');

            $table->unsignedBigInteger('replied_by')
                ->nullable()
                ->index('reviews_replied_by_idx')
                ->comment('Пользователь, оставивший ответ (users.id)');

            $table->foreign(
                'replied_by',
                'reviews_replied_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('replied_at')
                ->nullable()
                ->index('reviews_replied_at_idx')
                ->comment('Дата и время ответа');

            /**
             * Модерация.
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('reviews_moderation_status_idx')
                ->comment('Статус модерации: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('reviews_moderated_by_idx')
                ->comment('Модератор отзыва (users.id)');

            $table->foreign(
                'moderated_by',
                'reviews_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('reviews_moderated_at_idx')
                ->comment('Дата и время модерации');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий или причина решения модератора');

            /**
             * Полезность отзыва.
             */
            $table->unsignedInteger('likes')
                ->default(0)
                ->index('reviews_likes_idx')
                ->comment('Количество отметок полезности отзыва');

            /**
             * Активность.
             */
            $table->boolean('activity')
                ->default(true)
                ->index('reviews_activity_idx')
                ->comment('Активность отзыва');

            $table->timestamps();

            /**
             * Один пользователь может оставить только один отзыв
             * на одну конкретную сущность.
             *
             * Пользователь сможет отдельно оставить отзывы:
             * - товару;
             * - комплекту;
             * - курсу.
             */
            $table->unique(
                [
                    'reviewable_type',
                    'reviewable_id',
                    'user_id',
                ],
                'reviews_reviewable_user_unique'
            );

            /**
             * Быстрая загрузка всех отзывов сущности.
             */
            $table->index(
                [
                    'reviewable_type',
                    'reviewable_id',
                ],
                'reviews_reviewable_idx'
            );

            /**
             * Публичные отзывы сущности.
             */
            $table->index(
                [
                    'reviewable_type',
                    'reviewable_id',
                    'activity',
                    'moderation_status',
                ],
                'reviews_public_idx'
            );

            /**
             * Выборка отзывов сущности по рейтингу.
             */
            $table->index(
                [
                    'reviewable_type',
                    'reviewable_id',
                    'rating',
                ],
                'reviews_rating_filter_idx'
            );

            /**
             * Отзывы пользователя.
             */
            $table->index(
                [
                    'user_id',
                    'created_at',
                ],
                'reviews_user_created_at_idx'
            );

            /**
             * Очередь модерации.
             */
            $table->index(
                [
                    'moderation_status',
                    'created_at',
                ],
                'reviews_moderation_created_at_idx'
            );

            $table->comment(
                'Универсальные полиморфные отзывы пользователей для товаров, комплектов, курсов и других сущностей.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
