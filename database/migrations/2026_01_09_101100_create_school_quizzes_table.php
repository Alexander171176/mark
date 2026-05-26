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
        Schema::create('school_quizzes', function (Blueprint $t) {
            $t->id();

            // Привязки к структуре школы
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

            // Непереводимые поля
            $t->string('slug')->unique()->comment('Уникальный ЧПУ квиза');

            // Тип квиза
            $t->enum('type', ['graded', 'practice'])
                ->default('graded')
                ->comment('Тип квиза: graded|practice');

            // Ограничения
            $t->unsignedSmallInteger('attempts_limit')
                ->default(0)
                ->comment('Лимит попыток, 0 = без ограничений');

            $t->unsignedSmallInteger('time_limit_minutes')
                ->nullable()
                ->comment('Лимит времени в минутах, null = без лимита');

            $t->unsignedTinyInteger('pass_score')
                ->default(70)
                ->comment('Проходной порог в процентах 0-100');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность квиза');
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            // Блоки вывода
            $t->boolean('left')->default(false)->comment('Выводить в левой колонке');
            $t->boolean('main')->default(false)->comment('Выводить в центре');
            $t->boolean('right')->default(false)->comment('Выводить в правой колонке');

            $t->timestamps();

            // Индексы
            $t->index(
                ['school_course_id', 'school_module_id', 'school_lesson_id'],
                'idx_school_quiz_context'
            );

            $t->index(
                ['activity', 'published_at'],
                'idx_school_quiz_activity_pub'
            );

            $t->index(
                ['left', 'activity', 'published_at'],
                'idx_school_quiz_left_pub'
            );

            $t->index(
                ['main', 'activity', 'published_at'],
                'idx_school_quiz_main_pub'
            );

            $t->index(
                ['right', 'activity', 'published_at'],
                'idx_school_quiz_right_pub'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_quizzes');
    }
};
