<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица квизов/тестов
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $t) {
            $t->id();

            // Привязка к контенту (произвольно глубоко, всё nullable)
            $t->foreignId('course_id')->nullable()
                ->constrained('courses')->nullOnDelete();
            $t->foreignId('module_id')->nullable()
                ->constrained('modules')->nullOnDelete();
            $t->foreignId('lesson_id')->nullable()
                ->constrained('lessons')->nullOnDelete();

            $t->string('locale', 10)
                ->default('ru')
                ->comment('Локаль задания (ru/en/kk и т.д.)');

            $t->string('title');                        // Заголовок квиза
            $t->string('slug')->unique();               // Уникальный ЧПУ
            $t->text('short')->nullable();            // Краткое описание (анонс)
            $t->text('description')->nullable();      // Описание

            $t->enum('type', ['graded','practice'])     // graded — с оценкой, practice — тренировочный
            ->default('graded')
                ->comment('graded|practice');

            $t->unsignedSmallInteger('attempts_limit')
            ->default(0)  // 0 = не ограничено
                ->comment('0 = unlimited');
            $t->unsignedSmallInteger('time_limit_minutes')->nullable()
                ->comment('null = без лимита по времени');
            $t->unsignedTinyInteger('pass_score')->default(70)
                ->comment('Проходной порог в процентах (0..100)');

            $t->unsignedInteger('sort')->default(0);  // Для сортировки внутри модуля/урока
            $t->boolean('activity')->default(true);   // Флаг включения
            $t->timestamp('published_at')->nullable();  // Когда опубликован

            $t->boolean('left')->default(false)->index();  // Флаг включения в левой колонке
            $t->boolean('main')->default(false)->index();  // Флаг включения в центре
            $t->boolean('right')->default(false)->index(); // Флаг включения в правой колонке

            $t->timestamps();

            // Индексы для выборок

            // Квиз в контексте курса/модуля/урока + локаль
            $t->index(
                ['course_id', 'module_id', 'lesson_id', 'locale'],
                'idx_quiz_context_locale'
            );

            // Публикация квизов по локали + активности + дате
            $t->index(
                ['locale', 'activity', 'published_at'],
                'idx_quiz_locale_pub'
            );

            // Быстрый выбор квизов в левой колонке
            $t->index(
                ['locale', 'left', 'activity', 'published_at'],
                'idx_quiz_left_pub'
            );

            // Быстрый выбор квизов в центральной колонке
            $t->index(
                ['locale', 'main', 'activity', 'published_at'],
                'idx_quiz_main_pub'
            );

            // Быстрый выбор квизов в правой колонке
            $t->index(
                ['locale', 'right', 'activity', 'published_at'],
                'idx_quiz_right_pub'
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
