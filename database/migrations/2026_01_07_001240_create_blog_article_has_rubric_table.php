<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_has_rubric', function (Blueprint $table) {

            $table->foreignId('article_id')
                ->comment('FK на blog_articles.id')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->foreignId('rubric_id')
                ->comment('FK на blog_rubrics.id')
                ->constrained('blog_rubrics')
                ->cascadeOnDelete();

            /**
             * Композитный PK
             */
            $table->primary(
                ['article_id', 'rubric_id'],
                'blog_article_has_rubric_primary'
            );

            /**
             * Индексы для быстрых выборок
             */
            $table->index(
                'rubric_id',
                'blog_article_has_rubric_rubric_id_idx'
            );

            $table->index(
                'article_id',
                'blog_article_has_rubric_article_id_idx'
            );

            $table->comment('Связь статей блога с рубриками (many-to-many).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_has_rubric');
    }
};
