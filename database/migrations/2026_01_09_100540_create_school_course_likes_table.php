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
        Schema::create('school_course_likes', function (Blueprint $t) {
            $t->id()->comment('PK лайка');

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь, который поставил лайк');

            // Курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс, который лайкнули');

            $t->timestamps();

            $t->unique(['user_id', 'school_course_id'], 'uq_school_course_like_user_course');

            $t->index('user_id', 'idx_school_course_likes_user');
            $t->index('school_course_id', 'idx_school_course_likes_course');
            $t->index(['school_course_id', 'created_at'], 'idx_school_course_likes_course_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_likes');
    }
};
