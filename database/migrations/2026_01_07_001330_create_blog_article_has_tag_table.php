<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_has_tag', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на blog_articles.id')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->comment('FK на blog_tags.id')
                ->constrained('blog_tags')
                ->cascadeOnDelete();

            /**
             * Композитный первичный ключ
             */
            $table->primary(
                ['article_id', 'tag_id'],
                'blog_article_has_tag_primary'
            );

            /**
             * Индексы для быстрых выборок
             */
            $table->index(
                'tag_id',
                'blog_article_has_tag_tag_id_idx'
            );

            $table->index(
                'article_id',
                'blog_article_has_tag_article_id_idx'
            );

            $table->comment('Связь статей блога с тегами (many-to-many).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_has_tag');
    }
};
