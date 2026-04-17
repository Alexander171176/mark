<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            // user_id (ручной FK + явные имена)
            $table->unsignedBigInteger('user_id')
                ->index('lesson_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            // lesson_id (ручной FK + явные имена)
            $table->unsignedBigInteger('lesson_id')
                ->index('lesson_likes_lesson_id_idx')
                ->comment('Какой урок лайкнули (lessons.id)');

            $table->timestamps(); // когда лайк поставлен

            // FK constraints (с явными именами)
            $table->foreign('user_id', 'lesson_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('lesson_id', 'lesson_likes_lesson_id_fk')
                ->references('id')
                ->on('lessons')
                ->cascadeOnDelete();

            // Запрещаем повторный лайк одного урока одним пользователем
            $table->unique(['user_id', 'lesson_id'], 'lesson_likes_user_lesson_unique');

            // Ускоряет выборки "все лайки уроков"
            $table->index(['lesson_id', 'created_at'], 'lesson_likes_lesson_created_idx');

            $table->comment('Лайки уроков пользователями (один пользователь — один лайк).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_likes');
    }
};
