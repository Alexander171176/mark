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
        Schema::create('review_images', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Порядок изображения.
             *
             * Основной порядок конкретного изображения хранится здесь,
             * а порядок изображения внутри конкретного отзыва дополнительно
             * будет храниться в pivot-таблице review_has_images.
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('review_images_order_idx')
                ->comment('Порядок сортировки изображения');

            /**
             * Описание изображения.
             */
            $table->string('alt', 255)
                ->nullable()
                ->comment('Alt-текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment(
                'Универсальные изображения отзывов. Файлы хранятся через Spatie MediaLibrary.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
