<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            // user_id (ручной FK + явные имена)
            $table->unsignedBigInteger('user_id')
                ->index('article_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            // article_id (ручной FK + явные имена)
            $table->unsignedBigInteger('article_id')
                ->index('article_likes_article_id_idx')
                ->comment('Какую статью лайкнули (articles.id)');

            $table->timestamps(); // когда лайк поставлен

            // FK constraints (с явными именами)
            $table->foreign('user_id', 'article_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('article_id', 'article_likes_article_id_fk')
                ->references('id')
                ->on('articles')
                ->cascadeOnDelete();

            // Запрещаем повторный лайк одной статьи одним пользователем
            $table->unique(['user_id', 'article_id'], 'article_likes_user_article_unique');

            // Ускоряет выборки "все лайки статьи"
            $table->index(['article_id', 'created_at'], 'article_likes_article_created_idx');

            $table->comment('Лайки статей пользователями (один пользователь — один лайк).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_likes');
    }
};
