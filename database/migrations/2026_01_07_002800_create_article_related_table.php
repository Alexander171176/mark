<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_related', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('Статья-источник (articles.id)')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('related_article_id')
                ->comment('Рекомендуемая статья (articles.id)')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка рекомендаций внутри статьи (draggable)');

            $table->primary(['article_id', 'related_article_id']);

            // Индексы для быстрых выборок
            $table->index(['article_id', 'sort'], 'ar_article_sort_idx'); // основной рабочий индекс
            $table->index('related_article_id', 'ar_related_article_id_idx');

            $table->comment('Связь рекомендованных статей (self many-to-many) с сортировкой.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_related');
    }
};
