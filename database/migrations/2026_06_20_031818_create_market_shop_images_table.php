<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_shop_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_shop_images_order_idx')
                ->comment('Порядок сортировки изображения');

            $table->string('alt', 255)
                ->nullable()
                ->comment('Alt текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment('Маркетплейс: изображения магазинов. Файлы хранятся через Spatie MediaLibrary.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_shop_images');
    }
};
