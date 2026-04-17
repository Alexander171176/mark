<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица фиксации прогресса пользователя по учебным сущностям
    public function up(): void
    {
        Schema::create('progress_records', function (Blueprint $t) {
            $t->id();

            // FK на пользователя (обязателен)
            $t->foreignId('user_id')
                ->comment('Пользователь')
                ->constrained('users')
                ->cascadeOnDelete();

            // Опциональная привязка к зачислению (если ведёте групповые потоки/планы)
            $t->foreignId('enrollment_id')
                ->nullable()
                ->comment('Зачисление пользователя на курс/поток')
                ->constrained('enrollments')
                ->nullOnDelete();

            // Агрегирующие привязки (для быстрых выборок и индексации)
            $t->foreignId('course_id')
                ->nullable()
                ->comment('Курс')
                ->constrained('courses')
                ->nullOnDelete();

            $t->foreignId('module_id')
                ->nullable()
                ->comment('Модуль курса')
                ->constrained('modules')
                ->nullOnDelete();

            $t->foreignId('lesson_id')
                ->nullable()
                ->comment('Конкретный урок')
                ->constrained('lessons')
                ->nullOnDelete();

            // Статус прохождения
            $t->string('status', 24)
                ->default('in_progress')
                ->comment('in_progress|completed|skipped|locked');

            // Процент прогресса 0..100
            $t->unsignedTinyInteger('progress_percent')
                ->default(0)
                ->comment('Процент прохождения (0..100)');

            // Накопленное время
            $t->unsignedInteger('time_spent_seconds')
                ->default(0)
                ->comment('Секунды, затраченные на изучение');

            // Метки времени активности
            $t->timestamp('last_viewed_at')->nullable()
                ->comment('Последняя активность');
            $t->timestamp('completed_at')->nullable()
                ->comment('Время завершения (если завершён)');

            $t->timestamps();

            // Индексы для быстрых запросов
            $t->index(['user_id', 'course_id'], 'idx_progress_user_course');
            $t->index(['user_id', 'module_id'], 'idx_progress_user_module');
            $t->index(['user_id', 'status'], 'idx_progress_user_status');

            // Бизнес-правило: уникальность прогресса по уроку для пользователя
            $t->unique(['user_id', 'lesson_id'], 'uq_progress_user_lesson');

            // При необходимости можно включить и это:
            // $t->unique(['user_id', 'module_id'], 'uq_progress_user_module');
            // $t->unique(['user_id', 'course_id'], 'uq_progress_user_course');

            // Небольшая защита на уровне БД (опционально, если MySQL 8.0+)
            // CHECK (progress_percent BETWEEN 0 AND 100) — в MySQL можно через raw:
            // $t->check('progress_percent BETWEEN 0 AND 100');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_records');
    }
};
