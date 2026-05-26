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
        Schema::create('school_qa_messages', function (Blueprint $t) {
            $t->id();

            // Тема
            $t->foreignId('thread_id')
                ->constrained('school_qa_threads')
                ->cascadeOnDelete();

            // Автор
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Родитель (для вложенности)
            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('school_qa_messages')
                ->nullOnDelete();

            // Контент
            $t->text('body')->comment('Текст сообщения');

            // Флаги
            $t->boolean('is_private')->default(false)
                ->comment('Приватное сообщение');

            $t->boolean('is_pinned')->default(false)
                ->comment('Закреплено');

            // Метрики
            $t->unsignedInteger('replies_count')->default(0);

            // Тайминги
            $t->timestamp('edited_at')->nullable();

            $t->json('meta')->nullable();

            $t->timestamps();

            // Индексы
            $t->index(['thread_id', 'parent_id', 'created_at'], 'idx_school_thread_parent');
            $t->index(['user_id', 'created_at'], 'idx_school_message_user');
            $t->index(['is_private', 'is_pinned'], 'idx_school_message_flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_qa_messages');
    }
};
