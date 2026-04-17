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
        Schema::create('course_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            // user_id (ручной FK + явные имена)
            $table->unsignedBigInteger('user_id')
                ->index('course_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            // course_id (ручной FK + явные имена)
            $table->unsignedBigInteger('course_id')
                ->index('course_likes_course_id_idx')
                ->comment('Какую статью лайкнули (courses.id)');

            $table->timestamps(); // когда лайк поставлен

            // FK constraints (с явными именами)
            $table->foreign('user_id', 'course_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('course_id', 'course_likes_course_id_fk')
                ->references('id')
                ->on('courses')
                ->cascadeOnDelete();

            // Запрещаем повторный лайк одной статьи одним пользователем
            $table->unique(['user_id', 'course_id'], 'course_likes_user_course_unique');

            // Ускоряет выборки "все лайки статьи"
            $table->index(['course_id', 'created_at'], 'course_likes_course_created_idx');

            $table->comment('Лайки статей пользователями (один пользователь — один лайк).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_likes');
    }
};
