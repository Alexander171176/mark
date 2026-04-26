<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_video_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            /**
             * Кто поставил лайк
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_video_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            /**
             * Какое видео лайкнули
             */
            $table->unsignedBigInteger('video_id')
                ->index('blog_video_likes_video_id_idx')
                ->comment('Какое видео лайкнули (blog_videos.id)');

            $table->timestamps(); // когда поставлен лайк

            /**
             * Один пользователь может лайкнуть видео только один раз
             */
            $table->unique(
                ['user_id', 'video_id'],
                'blog_video_likes_user_video_unique'
            );

            /**
             * Быстрые выборки лайков конкретного видео
             */
            $table->index(
                ['video_id', 'created_at'],
                'blog_video_likes_video_created_idx'
            );

            /**
             * FK
             */
            $table->foreign('user_id', 'blog_video_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('video_id', 'blog_video_likes_video_id_fk')
                ->references('id')
                ->on('blog_videos')
                ->cascadeOnDelete();

            $table->comment('Лайки видео блога пользователями (один пользователь — один лайк).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_video_likes');
    }
};
