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
        Schema::create('school_assignment_submissions', function (Blueprint $t) {
            $t->id();

            // Задание
            $t->foreignId('school_assignment_id')
                ->constrained('school_assignments')
                ->cascadeOnDelete()
                ->comment('Задание');

            // Урок (опционально)
            $t->foreignId('school_lesson_id')
                ->nullable()
                ->constrained('school_lessons')
                ->nullOnDelete()
                ->comment('Урок');

            // Студент
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Контент сдачи
            $t->text('content')->nullable()->comment('Текстовый ответ');
            $t->json('attachments')->nullable()->comment('Файлы/ссылки');

            // Статус
            $t->string('status', 24)
                ->default('submitted')
                ->comment('submitted|under_review|graded|needs_changes');

            // Оценка
            $t->decimal('score', 5, 2)
                ->nullable()
                ->comment('Баллы');

            $t->text('review_comment')
                ->nullable()
                ->comment('Комментарий проверяющего');

            // Кто проверил
            $t->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Проверяющий');

            // Даты
            $t->timestamp('submitted_at')->nullable()->comment('Дата сдачи');
            $t->timestamp('graded_at')->nullable()->comment('Дата проверки');

            $t->timestamps();

            // Ограничение: 1 пользователь = 1 сдача на задание
            $t->unique(
                ['school_assignment_id', 'user_id'],
                'uq_school_assignment_user'
            );

            // Индексы
            $t->index(['status', 'graded_at'], 'idx_school_submission_status_graded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_assignment_submissions');
    }
};
