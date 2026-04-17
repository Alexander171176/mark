<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Темы Q&A к курсам/модулям/урокам/бандлам и т.д. (polymorphic)
    public function up(): void
    {
        Schema::create('qa_threads', function (Blueprint $t) {
            $t->id();

            // Автор темы (кто создал вопрос/тему)
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Целевая сущность темы (Course/Module/Lesson/Bundle/...)
            $t->morphs('threadable'); // threadable_type, threadable_id (+индексы)

            // Контент и атрибуты темы
            $t->string('title');                  // заголовок темы/вопроса
            $t->text('body')->nullable();         // стартовое сообщение (опционально)
            $t->string('status', 16)              // статус модерации/жизни
            ->default('open')
                ->comment('open|closed|archived');
            $t->boolean('is_locked')->default(false); // запрет новых сообщений
            $t->boolean('is_pinned')->default(false); // закрепить вверху

            // Служебные поля/метрики
            $t->unsignedInteger('replies_count')->default(0); // количество сообщений (без стартового)
            $t->timestamp('last_reply_at')->nullable();       // когда был последний ответ
            $t->timestamp('last_activity_at')->nullable();    // последняя активность (созд./ответ/изменение)
            $t->json('meta')->nullable();                     // любые дополнительные данные

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['status', 'is_locked', 'is_pinned'], 'idx_threads_flags');
            $t->index(['last_activity_at'], 'idx_threads_last_activity');
            // (morphs уже добавляет индекс по (threadable_type, threadable_id))
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_threads');
    }
};
