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
        Schema::create('school_track_likes', function (Blueprint $t) {
            $t->id()->comment('PK лайка');

            // Пользователь, который поставил лайк
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Категория обучения, которую лайкнули
            $t->foreignId('school_track_id')
                ->constrained('school_tracks')
                ->cascadeOnDelete()
                ->comment('Категория обучения');

            $t->timestamps();

            // Один пользователь — один лайк на одну категорию обучения
            $t->unique(['user_id', 'school_track_id'], 'uq_school_track_like_user_track');

            // Индексы
            $t->index('user_id', 'idx_school_track_likes_user');
            $t->index('school_track_id', 'idx_school_track_likes_track');
            $t->index(['school_track_id', 'created_at'], 'idx_school_track_likes_track_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_track_likes');
    }
};
