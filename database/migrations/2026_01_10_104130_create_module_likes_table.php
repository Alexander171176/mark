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
        Schema::create('module_likes', function (Blueprint $table) {
            $table->id()->comment('PK лайка');

            // user_id (ручной FK + явные имена)
            $table->unsignedBigInteger('user_id')
                ->index('module_likes_user_id_idx')
                ->comment('Кто поставил лайк (users.id)');

            // module_id (ручной FK + явные имена)
            $table->unsignedBigInteger('module_id')
                ->index('module_likes_module_id_idx')
                ->comment('Какой модуль лайкнули (modules.id)');

            $table->timestamps(); // когда лайк поставлен

            // FK constraints (с явными именами)
            $table->foreign('user_id', 'module_likes_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('module_id', 'module_likes_module_id_fk')
                ->references('id')
                ->on('modules')
                ->cascadeOnDelete();

            // Запрещаем повторный лайк одного модуля одним пользователем
            $table->unique(['user_id', 'module_id'], 'module_likes_user_module_unique');

            // Ускоряет выборки "все лайки модулей"
            $table->index(['module_id', 'created_at'], 'module_likes_module_created_idx');

            $table->comment('Лайки модулей пользователями (один пользователь — один лайк).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_likes');
    }
};
