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
        Schema::create('school_module_likes', function (Blueprint $t) {
            $t->id()->comment('PK лайка');

            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            $t->foreignId('school_module_id')
                ->constrained('school_modules')
                ->cascadeOnDelete()
                ->comment('Модуль');

            $t->timestamps();

            $t->unique(['user_id', 'school_module_id'], 'uq_school_module_like_user_module');

            $t->index('user_id', 'idx_school_module_likes_user');
            $t->index('school_module_id', 'idx_school_module_likes_module');
            $t->index(['school_module_id', 'created_at'], 'idx_school_module_likes_module_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_module_likes');
    }
};
