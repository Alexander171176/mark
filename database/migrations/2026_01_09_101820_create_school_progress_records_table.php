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
        Schema::create('school_progress_records', function (Blueprint $t) {
            $t->id();

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Зачисление
            $t->foreignId('school_enrollment_id')
                ->nullable()
                ->constrained('school_enrollments')
                ->nullOnDelete()
                ->comment('Зачисление');

            // Курс
            $t->foreignId('school_course_id')
                ->nullable()
                ->constrained('school_courses')
                ->nullOnDelete()
                ->comment('Курс');

            // Модуль
            $t->foreignId('school_module_id')
                ->nullable()
                ->constrained('school_modules')
                ->nullOnDelete()
                ->comment('Модуль');

            // Урок
            $t->foreignId('school_lesson_id')
                ->nullable()
                ->constrained('school_lessons')
                ->nullOnDelete()
                ->comment('Урок');

            // Статус
            $t->string('status', 24)
                ->default('in_progress')
                ->comment('in_progress|completed|skipped|locked');

            // Прогресс
            $t->unsignedTinyInteger('progress_percent')
                ->default(0)
                ->comment('Процент прохождения 0-100');

            // Время изучения
            $t->unsignedInteger('time_spent_seconds')
                ->default(0)
                ->comment('Затраченное время в секундах');

            // Активность
            $t->timestamp('last_viewed_at')->nullable()->comment('Последняя активность');
            $t->timestamp('completed_at')->nullable()->comment('Дата завершения');

            $t->timestamps();

            // Индексы
            $t->index(['user_id', 'school_course_id'], 'idx_school_progress_user_course');
            $t->index(['user_id', 'school_module_id'], 'idx_school_progress_user_module');
            $t->index(['user_id', 'status'], 'idx_school_progress_user_status');

            // Один прогресс по уроку на пользователя
            $t->unique(['user_id', 'school_lesson_id'], 'uq_school_progress_user_lesson');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_progress_records');
    }
};
