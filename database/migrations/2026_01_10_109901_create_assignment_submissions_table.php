<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Работы студентов по заданиям
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $t) {
            $t->id();

            // К какому заданию относится работа
            $t->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            // На какой урок подана (может пригодиться для фильтрации; nullable на случай общих заданий)
            $t->foreignId('lesson_id')
                ->nullable()
                ->constrained('lessons')
                ->nullOnDelete();

            // Кто сдал
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Контент сдачи
            $t->text('content')->nullable();          // текстовый ответ
            $t->json('attachments')->nullable();      // массив файлов/ссылок (если не используем медиалиблиотеку напрямую)

            // Статус проверки
            $t->string('status', 24)
                ->default('submitted')
                ->comment('submitted|under_review|graded|needs_changes');

            // Оценка и комментарии проверяющего
            $t->decimal('score', 5, 2)->nullable();   // балл (например 0..100 или 0..5)
            $t->text('review_comment')->nullable();   // комментарий проверяющего

            // Кто проверил (если требуется фиксация проверяющего)
            $t->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Тайминги
            $t->timestamp('submitted_at')->nullable(); // когда студент отправил
            $t->timestamp('graded_at')->nullable();    // когда проверено

            $t->timestamps();
            $t->softDeletes();

            // Ограничение: один пользователь — одна активная сдача на задание (без учёта удалённых)
            $t->unique(['assignment_id', 'user_id'], 'uniq_assignment_user');

            // Частые индексы
            $t->index(['status', 'graded_at'], 'idx_status_graded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
