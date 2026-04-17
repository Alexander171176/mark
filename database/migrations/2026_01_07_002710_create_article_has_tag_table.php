<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_has_tag', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на articles.id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->comment('FK на tags.id')
                ->constrained('tags')
                ->cascadeOnDelete();

            $table->primary(['article_id', 'tag_id']);

            // Для быстрых выборок (по тегу / по статье)
            $table->index('tag_id', 'aht_tag_id_idx');
            $table->index('article_id', 'aht_article_id_idx');

            $table->comment('Связь статей с тегами (many-to-many).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_has_tag');
    }
};
