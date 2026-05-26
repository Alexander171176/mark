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
        Schema::create('school_enrollments', function (Blueprint $t) {
            $t->id();

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Поток (может быть null — self-paced обучение)
            $t->foreignId('school_course_schedule_id')
                ->nullable()
                ->constrained('school_course_schedules')
                ->nullOnDelete()
                ->comment('Поток курса');

            // Заказ (если есть)
            $t->foreignId('school_order_id')
                ->nullable()
                ->constrained('school_orders')
                ->nullOnDelete()
                ->comment('Заказ');

            // Статус
            $t->string('status', 20)
                ->default('active')
                ->comment('active|completed|cancelled|expired|paused');

            // Жизненный цикл
            $t->timestamp('started_at')->nullable()->comment('Начало доступа');
            $t->timestamp('expires_at')->nullable()->comment('Дата окончания доступа');
            $t->timestamp('completed_at')->nullable()->comment('Завершение курса');

            // Прогресс
            $t->unsignedTinyInteger('progress_percent')
                ->default(0)
                ->comment('Прогресс 0-100');

            // Доп. данные
            $t->text('notes')->nullable()->comment('Заметки');
            $t->json('meta')->nullable()->comment('Доп. мета');

            $t->timestamps();

            // Индексы
            $t->index(['user_id', 'school_course_id'], 'idx_school_enroll_user_course');
            $t->index(['status', 'expires_at'], 'idx_school_enroll_status_expires');

            // 💡 (очень рекомендую)
            $t->unique(
                ['user_id', 'school_course_id', 'school_course_schedule_id'],
                'uq_school_enrollment_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_enrollments');
    }
};
