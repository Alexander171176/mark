<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_has_rubric', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->comment('FK на articles.id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('rubric_id')
                ->comment('FK на rubrics.id')
                ->constrained('rubrics')
                ->cascadeOnDelete();

            $table->primary(['article_id', 'rubric_id']);

            // Для быстрых выборок:
            $table->index('rubric_id', 'ahr_rubric_id_idx');
            $table->index('article_id', 'ahr_article_id_idx');

            $table->comment('Связь статей с рубриками (many-to-many).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_has_rubric');
    }
};
