<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Закладки пользователя (полиморфно: курс, модуль, урок, пост и т.п.)
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $t) {
            $t->id();

            // Кто поставил закладку
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // На что именно стоит закладка (полиморфная связь)
            $t->morphs('bookmarkable'); // bookmarkable_type, bookmarkable_id

            // Доп. атрибуты
            $t->boolean('is_favorite')->default(false); // избранное
            $t->string('folder', 64)->nullable();       // папка/категория пользователя (опционально)
            $t->unsignedInteger('position')->default(0);// сортировка внутри папки/списка
            $t->text('note')->nullable();               // заметка к закладке
            $t->json('meta')->nullable();               // произвольные метаданные

            $t->timestamps();
            $t->softDeletes();

            // Один и тот же объект нельзя закладить дважды одним пользователем
            $t->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id'], 'uniq_user_target');

            // Частые выборки
            $t->index(['user_id', 'is_favorite', 'created_at'], 'idx_user_flags_created');
            $t->index(['folder', 'position'], 'idx_folder_position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
