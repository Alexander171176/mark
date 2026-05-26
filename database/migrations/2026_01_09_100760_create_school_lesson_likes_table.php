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
        Schema::create('school_lesson_likes', function (Blueprint $t) {
            $t->id()->comment('PK лайка');

            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            $t->foreignId('school_lesson_id')
                ->constrained('school_lessons')
                ->cascadeOnDelete()
                ->comment('Урок');

            $t->timestamps();

            $t->unique(['user_id', 'school_lesson_id'], 'uq_school_lesson_like_user');

            $t->index('user_id', 'idx_school_lesson_likes_user');
            $t->index('school_lesson_id', 'idx_school_lesson_likes_lesson');
            $t->index(['school_lesson_id', 'created_at'], 'idx_school_lesson_likes_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_lesson_likes');
    }
};
