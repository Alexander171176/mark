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
        Schema::create('school_course_schedules', function (Blueprint $t) {
            $t->id();

            // Курс, к которому относится поток
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Ведущий преподаватель потока
            $t->foreignId('school_instructor_profile_id')
                ->nullable()
                ->constrained('school_instructor_profiles')
                ->nullOnDelete()
                ->comment('Преподаватель потока');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность потока');

            // Непереводимые поля
            $t->string('slug')->unique()->comment('Уникальный ЧПУ потока');

            // Даты проведения
            $t->timestamp('starts_at')->nullable()->comment('Дата начала потока');
            $t->timestamp('ends_at')->nullable()->comment('Дата окончания потока');

            // Окно записи
            $t->timestamp('enroll_starts_at')->nullable()->comment('Дата начала записи');
            $t->timestamp('enroll_ends_at')->nullable()->comment('Дата окончания записи');

            // Вместимость
            $t->unsignedInteger('capacity')->default(0)->comment('Максимум участников, 0 = безлимит');

            // Формат проведения
            $t->boolean('is_online')->default(true)->comment('Онлайн-формат');

            // Ссылки/адреса
            $t->string('location')->nullable()->comment('Адрес офлайн-группы');
            $t->string('meeting_url')->nullable()->comment('Ссылка на онлайн-встречу');

            // Часовой пояс
            $t->string('timezone', 64)->default('UTC')->comment('Часовой пояс');

            // Статус
            $t->string('status', 16)->default('draft')->comment('Статус потока');

            // Метрики
            $t->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            // Административные заметки
            $t->text('notes')->nullable()->comment('Внутренние заметки администратора');

            $t->timestamps();

            // Индексы
            $t->index(['school_course_id', 'status'], 'idx_school_schedule_course_status');
            $t->index(['activity', 'status'], 'idx_school_schedule_activity_status');
            $t->index(['activity', 'sort'], 'idx_school_schedule_activity_sort');
            $t->index(['starts_at', 'ends_at'], 'idx_school_schedule_dates');
            $t->index(['enroll_starts_at', 'enroll_ends_at'], 'idx_school_schedule_enroll_window');
            $t->index(['is_online', 'timezone'], 'idx_school_schedule_format');
            $t->index('views', 'idx_school_schedule_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_schedules');
    }
};
