<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Сообщения внутри Q&A тем (поддержка ответов и ветвления)
    public function up(): void
    {
        Schema::create('qa_messages', function (Blueprint $t) {
            $t->id();

            // К какой теме относится сообщение
            $t->foreignId('thread_id')
                ->constrained('qa_threads')
                ->cascadeOnDelete(); // при удалении темы — удалить сообщения

            // Автор сообщения
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Родительское сообщение (для ветвления/ответов)
            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('qa_messages')
                ->nullOnDelete(); // если родителя удалили — оставить как корневое

            // Текст сообщения
            $t->text('body');

            // Флаги/атрибуты
            $t->boolean('is_private')->default(false)  // видно только автору/преподавателю/админам (логикой)
            ->comment('приватное сообщение');
            $t->boolean('is_pinned')->default(false)   // закреплено в теме
            ->comment('закреплено модератором/автором темы');

            // Метаданные
            $t->unsignedInteger('replies_count')->default(0); // быстрый счётчик ответов
            $t->timestamp('edited_at')->nullable();           // когда редактировалось
            $t->json('meta')->nullable();                     // произвольные данные (вложения и т.п.)

            $t->timestamps();
            $t->softDeletes();

            // Индексы для частых выборок
            $t->index(['thread_id', 'parent_id', 'created_at'], 'idx_thread_parent_created');
            $t->index(['user_id', 'created_at'], 'idx_user_created');
            $t->index(['is_private', 'is_pinned'], 'idx_flags');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_messages');
    }
};
