<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('blog_article_images_order_idx')
                ->comment('Порядок/сортировка изображения (общий, если используется)');

            $table->string('alt', 255)
                ->nullable()
                ->comment('ALT текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment('Изображения статей блога (метаданные). Файл хранится через Spatie Media Library.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_images');
    }
};
