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
        Schema::create('track_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            // user_id
            $table->unsignedBigInteger('user_id')
                ->index('track_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            // learning_category_id
            $table->unsignedBigInteger('learning_category_id')
                ->index('track_likes_learning_category_id_idx')
                ->comment('Какую категорию обучения лайкнули (learning_categories.id)');

            $table->timestamps();

            // FK constraints
            $table->foreign('user_id', 'track_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('learning_category_id', 'track_likes_learning_category_id_fk')
                ->references('id')
                ->on('learning_categories')
                ->cascadeOnDelete();

            // один пользователь — один лайк на одну категорию обучения
            $table->unique(
                ['user_id', 'learning_category_id'],
                'track_likes_user_learning_category_unique'
            );

            // индекс для выборки лайков категории
            $table->index(
                ['learning_category_id', 'created_at'],
                'track_likes_learning_category_created_idx'
            );

            $table->comment('Лайки категорий обучения пользователями.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_likes');
    }
};
