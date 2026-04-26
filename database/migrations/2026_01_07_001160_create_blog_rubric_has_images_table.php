<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_rubric_has_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedBigInteger('rubric_id')
                ->comment('Рубрика (blog_rubrics.id)');

            $table->unsignedBigInteger('image_id')
                ->comment('Изображение (blog_rubric_images.id)');

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Порядок изображения внутри рубрики');

            $table->timestamps();

            $table->foreign('rubric_id', 'blog_rubric_has_images_rubric_id_fk')
                ->references('id')
                ->on('blog_rubrics')
                ->cascadeOnDelete();

            $table->foreign('image_id', 'blog_rubric_has_images_image_id_fk')
                ->references('id')
                ->on('blog_rubric_images')
                ->cascadeOnDelete();

            $table->unique(['rubric_id', 'image_id'], 'blog_rubric_has_images_unique');
            $table->index(['rubric_id', 'order'], 'blog_rubric_has_images_rubric_order_idx');

            $table->comment('Связь рубрик блога с изображениями и порядок их отображения.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_rubric_has_images');
    }
};
