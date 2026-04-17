<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_has_video', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на articles.id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('video_id')
                ->comment('FK на videos.id')
                ->constrained('videos')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка видео внутри статьи (draggable)');

            $table->primary(['article_id', 'video_id']);

            // Основной индекс для выдачи видео по статье в нужном порядке
            $table->index(['article_id', 'sort'], 'ahv_article_sort_idx');

            // Для обратных выборок: где используется видео
            $table->index('video_id', 'ahv_video_id_idx');

            $table->comment('Связь статей с видео (many-to-many) + сортировка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_has_video');
    }
};
