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
        Schema::create('market_product_variant_images', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Общий порядок изображения.
             *
             * Используется как базовая сортировка.
             * Внутри конкретного варианта порядок может
             * дополнительно переопределяться pivot-таблицей.
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_variant_images_order_idx')
                ->comment('Порядок сортировки изображения');

            /**
             * SEO и описание изображения.
             */
            $table->string('alt', 255)
                ->nullable()
                ->comment('Alt-текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment(
                'Маркетплейс: изображения вариантов товаров. Файлы хранятся через Spatie MediaLibrary.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_variant_images');
    }
};
