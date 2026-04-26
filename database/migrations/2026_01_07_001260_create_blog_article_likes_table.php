<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            /**
             * Кто поставил лайк
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_article_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            /**
             * Какую статью лайкнули
             */
            $table->unsignedBigInteger('article_id')
                ->index('blog_article_likes_article_id_idx')
                ->comment('Какую статью лайкнули (blog_articles.id)');

            $table->timestamps(); // когда лайк поставлен

            /**
             * Внешние ключи
             */
            $table->foreign('user_id', 'blog_article_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('article_id', 'blog_article_likes_article_id_fk')
                ->references('id')
                ->on('blog_articles')
                ->cascadeOnDelete();

            /**
             * Запрещаем повторный лайк одной статьи одним пользователем
             */
            $table->unique(
                ['user_id', 'article_id'],
                'blog_article_likes_user_article_unique'
            );

            /**
             * Индекс для выборок лайков статьи
             */
            $table->index(
                ['article_id', 'created_at'],
                'blog_article_likes_article_created_idx'
            );

            $table->comment('Лайки статей блога пользователями (один пользователь — один лайк).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_likes');
    }
};
