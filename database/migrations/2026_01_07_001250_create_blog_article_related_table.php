<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_related', function (Blueprint $table) {

            $table->foreignId('article_id')
                ->comment('Статья-источник (blog_articles.id)')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->foreignId('related_article_id')
                ->comment('Рекомендуемая статья (blog_articles.id)')
                ->constrained('blog_articles')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка рекомендаций внутри статьи (draggable)');

            /**
             * Композитный PK
             */
            $table->primary(
                ['article_id', 'related_article_id'],
                'blog_article_related_primary'
            );

            /**
             * Индексы для быстрых выборок
             */
            $table->index(
                ['article_id', 'sort'],
                'blog_article_related_article_sort_idx'
            );

            $table->index(
                'related_article_id',
                'blog_article_related_related_article_id_idx'
            );

            $table->comment('Связь рекомендованных статей (self many-to-many) с сортировкой.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_related');
    }
};
