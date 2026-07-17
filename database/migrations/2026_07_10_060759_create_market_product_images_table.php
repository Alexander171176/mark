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
        Schema::create('market_product_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_images_order_idx')
                ->comment('Порядок сортировки изображения');

            $table->string('alt', 255)
                ->nullable()
                ->comment('Alt текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment('Маркетплейс: изображения товаров. Файлы хранятся через Spatie MediaLibrary.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_images');
    }
};
