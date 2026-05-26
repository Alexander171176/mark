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
        Schema::create('school_bookmarks', function (Blueprint $t) {
            $t->id();

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Объект закладки: курс, модуль, урок, бандл и т.д.
            $t->morphs('bookmarkable');

            // Атрибуты закладки
            $t->boolean('is_favorite')->default(false)->comment('Избранное');
            $t->string('folder', 64)->nullable()->comment('Папка пользователя');
            $t->unsignedInteger('position')->default(0)->comment('Позиция сортировки');
            $t->text('note')->nullable()->comment('Заметка');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Один пользователь не может дважды добавить один объект
            $t->unique(
                ['user_id', 'bookmarkable_type', 'bookmarkable_id'],
                'uq_school_user_bookmarkable'
            );

            // Индексы
            $t->index(['user_id', 'is_favorite', 'created_at'], 'idx_school_bookmark_user_flags');
            $t->index(['folder', 'position'], 'idx_school_bookmark_folder_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bookmarks');
    }
};
