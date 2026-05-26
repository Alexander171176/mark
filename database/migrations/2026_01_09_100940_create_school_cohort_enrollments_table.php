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
        Schema::create('school_cohort_enrollments', function (Blueprint $t) {
            $t->id();

            // Поток курса
            $t->foreignId('school_course_schedule_id')
                ->constrained('school_course_schedules')
                ->cascadeOnDelete()
                ->comment('Поток курса');

            // Пользователь
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Статус записи
            $t->string('status', 20)
                ->default('pending')
                ->comment('pending|approved|rejected|cancelled');

            // Дата записи
            $t->timestamp('enrolled_at')
                ->nullable()
                ->comment('Дата записи на курс');

            // Заметки администратора
            $t->text('notes')
                ->nullable()
                ->comment('Комментарий администратора');

            $t->timestamps();

            // Один пользователь — одна запись на один поток
            $t->unique(
                ['school_course_schedule_id', 'user_id'],
                'uq_school_schedule_user'
            );

            // Индексы
            $t->index(['status', 'enrolled_at'], 'idx_school_enrollment_status_date');
            $t->index(
                ['school_course_schedule_id', 'status'],
                'idx_school_enrollment_schedule_status'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_cohort_enrollments');
    }
};
