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
        Schema::create('school_assignments', function (Blueprint $t) {
            $t->id();

            // Привязки задания
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

            // Автор задания
            $t->foreignId('school_instructor_profile_id')->nullable()
                ->constrained('school_instructor_profiles')
                ->nullOnDelete()
                ->comment('Преподаватель');

            // Непереводимые поля
            $t->string('slug')->unique()->comment('Уникальный ЧПУ задания');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность задания');

            // Блоки вывода
            $t->boolean('left')->default(false)->comment('Выводить в левой колонке');
            $t->boolean('main')->default(false)->comment('Выводить в центре');
            $t->boolean('right')->default(false)->comment('Выводить в правой колонке');

            // Публикация
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            // Параметры задания
            $t->string('status', 16)->default('draft')->comment('Статус: draft/published/archived');
            $t->string('visibility', 16)->default('enrolled')->comment('Видимость: public/enrolled/private');
            $t->unsignedSmallInteger('attempts_limit')->default(0)->comment('Лимит попыток, 0 = без ограничений');
            $t->string('grading_type', 16)->default('manual')->comment('Тип проверки: manual/auto');
            $t->unsignedSmallInteger('max_score')->default(100)->comment('Максимальный балл');
            $t->timestamp('due_at')->nullable()->comment('Дедлайн');

            $t->timestamps();

            // Индексы
            $t->index(
                ['school_course_id', 'school_module_id', 'school_lesson_id', 'sort'],
                'idx_school_assignment_context_sort'
            );

            $t->index(
                ['status', 'activity', 'published_at'],
                'idx_school_assignment_state_published'
            );

            $t->index('due_at', 'idx_school_assignment_due');
            $t->index(['activity', 'sort'], 'idx_school_assignment_activity_sort');
            $t->index('left', 'idx_school_assignment_left');
            $t->index('main', 'idx_school_assignment_main');
            $t->index('right', 'idx_school_assignment_right');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_assignments');
    }
};
