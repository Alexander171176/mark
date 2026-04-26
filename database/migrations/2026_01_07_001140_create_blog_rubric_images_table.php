<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_rubric_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('blog_rubric_images_order_idx')
                ->comment('Порядок изображения');

            $table->string('alt', 255)
                ->nullable()
                ->comment('Alt текст изображения');

            $table->string('caption', 500)
                ->nullable()
                ->comment('Подпись изображения');

            $table->timestamps();

            $table->comment('Изображения рубрик блога. Файлы хранятся через Spatie Media Library.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_rubric_images');
    }
};
