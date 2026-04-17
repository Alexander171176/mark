<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            $table->unsignedBigInteger('user_id')
                ->index('video_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            $table->unsignedBigInteger('video_id')
                ->index('video_likes_video_id_idx')
                ->comment('Какое видео лайкнули (videos.id)');

            $table->timestamps(); // когда поставлен лайк

            // Один пользователь может лайкнуть видео только один раз
            $table->unique(['user_id', 'video_id'], 'video_likes_user_video_unique');

            // Быстрые выборки лайков конкретного видео
            $table->index(['video_id', 'created_at'], 'video_likes_video_created_idx');

            // FK (явные имена)
            $table->foreign('user_id', 'video_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('video_id', 'video_likes_video_id_fk')
                ->references('id')
                ->on('videos')
                ->cascadeOnDelete();

            $table->comment('Лайки видео пользователями (один пользователь — один лайк).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_likes');
    }
};
