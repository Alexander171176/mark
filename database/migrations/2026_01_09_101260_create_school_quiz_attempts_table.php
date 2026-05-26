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
        Schema::create('school_quiz_attempts', function (Blueprint $t) {
            $t->id();

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Квиз
            $t->foreignId('school_quiz_id')
                ->constrained('school_quizzes')
                ->cascadeOnDelete()
                ->comment('Квиз');

            // Контекст прохождения
            $t->foreignId('school_enrollment_id')->nullable()
                ->constrained('school_enrollments')
                ->nullOnDelete()
                ->comment('Зачисление');

            $t->foreignId('school_course_id')->nullable()
                ->constrained('school_courses')
                ->nullOnDelete()
                ->comment('Курс');

            $t->foreignId('school_module_id')->nullable()
                ->constrained('school_modules')
                ->nullOnDelete()
                ->comment('Модуль');

            $t->foreignId('school_lesson_id')->nullable()
                ->constrained('school_lessons')
                ->nullOnDelete()
                ->comment('Урок');

            // Номер попытки
            $t->unsignedSmallInteger('attempt_number')
                ->default(1)
                ->comment('Номер попытки для пользователя и квиза');

            // Баллы
            $t->unsignedSmallInteger('score')->default(0)->comment('Набранные баллы');
            $t->unsignedSmallInteger('max_score')->default(0)->comment('Максимальные баллы');
            $t->unsignedTinyInteger('percent')->default(0)->comment('Процент 0-100');

            // Статус
            $t->enum('status', ['in_progress', 'completed', 'graded'])
                ->default('in_progress')
                ->comment('Статус попытки');

            // Время прохождения
            $t->timestamp('started_at')->nullable()->comment('Начало попытки');
            $t->timestamp('finished_at')->nullable()->comment('Завершение попытки');
            $t->unsignedInteger('duration_seconds')->default(0)->comment('Длительность в секундах');

            // Технические поля
            $t->string('ip_address', 45)->nullable()->comment('IP адрес');
            $t->string('user_agent', 512)->nullable()->comment('User Agent');

            $t->timestamps();

            // Индексы
            $t->unique(
                ['user_id', 'school_quiz_id', 'attempt_number'],
                'uq_school_attempt_user_quiz_n'
            );

            $t->index(['school_quiz_id', 'status'], 'idx_school_attempt_quiz_status');
            $t->index('school_enrollment_id', 'idx_school_attempt_enrollment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quiz_attempts');
    }
};
