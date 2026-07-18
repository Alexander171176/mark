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
        Schema::create('review_has_images', function (Blueprint $table) {
            /**
             * Отзыв.
             */
            $table->unsignedBigInteger('review_id')
                ->comment('Отзыв (reviews.id)');

            $table->foreign(
                'review_id',
                'review_has_images_review_id_fk'
            )
                ->references('id')
                ->on('reviews')
                ->cascadeOnDelete();

            /**
             * Изображение отзыва.
             */
            $table->unsignedBigInteger('review_image_id')
                ->comment('Изображение отзыва (review_images.id)');

            $table->foreign(
                'review_image_id',
                'review_has_images_image_id_fk'
            )
                ->references('id')
                ->on('review_images')
                ->cascadeOnDelete();

            /**
             * Порядок изображения внутри отзыва.
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('review_has_images_order_idx')
                ->comment('Порядок отображения изображения в отзыве');

            /**
             * Один отзыв не может содержать одно изображение дважды.
             */
            $table->primary(
                [
                    'review_id',
                    'review_image_id',
                ],
                'review_has_images_pk'
            );

            /**
             * Быстрая загрузка изображений отзыва
             * в установленном порядке.
             */
            $table->index(
                [
                    'review_id',
                    'order',
                ],
                'review_has_images_review_order_idx'
            );

            $table->comment(
                'Связь универсальных отзывов с изображениями.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_has_images');
    }
};
