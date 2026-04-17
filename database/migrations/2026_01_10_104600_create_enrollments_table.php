<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Зачисления пользователей на курсы/потоки
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();

            // Кто зачислён
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // На какой курс
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // В рамках какого потока/расписания (может быть null — “самообучение”)
            $t->foreignId('course_schedule_id')
                ->nullable()
                ->constrained('course_schedules')
                ->nullOnDelete();

            // Исходный заказ (если зачисление создано после оплаты)
            $t->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // Статус зачисления
            $t->string('status', 20)
                ->default('active')
                ->comment('active|completed|cancelled|expired|paused');

            // Временные метки жизненного цикла
            $t->timestamp('started_at')->nullable();     // когда реальный доступ начат
            $t->timestamp('expires_at')->nullable();     // дедлайн доступа (если есть)
            $t->timestamp('completed_at')->nullable();   // когда курс завершён

            // Прогресс по курсу (быстрая агрегация для списков)
            $t->unsignedTinyInteger('progress_percent')
                ->default(0)
                ->comment('Процент прохождения (0..100)');

            // Служебные заметки/метаданные
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();

            // Soft delete
            $t->softDeletes(); // deleted_at

            // Частые индексы/поиски
            $t->index(['user_id', 'course_id'], 'idx_enroll_user_course');
            $t->index(['status', 'expires_at'], 'idx_enroll_status_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
