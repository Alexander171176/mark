<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Попытки прохождения квизов пользователями
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $t) {
            $t->id();

            // Кто проходит
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Какой квиз
            $t->foreignId('quiz_id')
                ->constrained('quizzes')
                ->cascadeOnDelete();

            // Контекст прохождения (в рамках зачисления/курса/модуля/урока)
            $t->foreignId('enrollment_id')->nullable()
                ->constrained('enrollments')
                ->nullOnDelete();

            $t->foreignId('course_id')->nullable()
                ->constrained('courses')->nullOnDelete();
            $t->foreignId('module_id')->nullable()
                ->constrained('modules')->nullOnDelete();
            $t->foreignId('lesson_id')->nullable()
                ->constrained('lessons')->nullOnDelete();

            // Номер попытки для данной пары (user, quiz)
            $t->unsignedSmallInteger('attempt_number')->default(1)
                ->comment('Порядковый номер попытки для (user, quiz)');

            // Баллы и статус
            $t->unsignedSmallInteger('score')->default(0)
                ->comment('Набранные баллы');
            $t->unsignedSmallInteger('max_score')->default(0)
                ->comment('Максимально возможные баллы');
            $t->unsignedTinyInteger('percent')->default(0)
                ->comment('Процент (0..100) на момент завершения');

            $t->enum('status', ['in_progress', 'completed', 'graded'])
                ->default('in_progress')
                ->comment('in_progress|completed (авто) | graded (после ручной проверки)');

            // Время прохождения
            $t->timestamp('started_at')->nullable()
                ->comment('Когда пользователь начал попытку');
            $t->timestamp('finished_at')->nullable()
                ->comment('Когда пользователь завершил попытку');
            $t->unsignedInteger('duration_seconds')->default(0)
                ->comment('Затраченное время в секундах');

            // Технические поля (для аналитики/безопасности)
            $t->string('ip_address', 45)->nullable()
                ->comment('IPv4/IPv6');
            $t->string('user_agent', 512)->nullable()
                ->comment('Браузер/клиент');

            $t->timestamps();

            // Индексы
            $t->unique(['user_id', 'quiz_id', 'attempt_number'], 'uq_attempt_user_quiz_n'); // уникальный номер попытки в рамках (user, quiz)
            $t->index(['quiz_id', 'status'], 'idx_attempt_quiz_status');
            $t->index(['enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
