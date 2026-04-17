<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица заданий (привязываются к курсу/модулю/уроку)
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $t) {
            $t->id();

            // Привязки (все опциональны: задание может быть у курса в целом, у модуля или конкретного урока)
            $t->foreignId('course_id')->nullable()
                ->constrained('courses')->nullOnDelete(); // FK -> courses.id

            $t->foreignId('module_id')->nullable()
                ->constrained('modules')->nullOnDelete(); // FK -> modules.id

            $t->foreignId('lesson_id')->nullable()
                ->constrained('lessons')->nullOnDelete(); // FK -> lessons.id

            // Автор задания (преподаватель)
            $t->foreignId('instructor_profile_id')->nullable()
                ->constrained('instructor_profiles')->nullOnDelete(); // FK -> instructor_profiles.id

            $t->string('locale', 10)
                ->default('ru')
                ->comment('Локаль задания (ru/en/kk и т.д.)');

            // Основные поля
            $t->string('title');                      // Заголовок задания
            $t->string('slug')->unique();             // ЧПУ
            $t->string('subtitle')->nullable();       // Подзаголовок/оффер
            $t->text('short')->nullable();            // Краткое описание (анонс)
            $t->text('description')->nullable();      // Описание
            $t->longText('instructions')->nullable(); // Подробные инструкции

            $t->unsignedInteger('sort')->default(0);  // Для сортировки внутри модуля/урока
            $t->boolean('activity')->default(true);   // Флаг включения


            $t->boolean('left')->default(false)->index();  // Флаг включения в левой колонке
            $t->boolean('main')->default(false)->index();  // Флаг включения в центре
            $t->boolean('right')->default(false)->index(); // Флаг включения в правой колонке

            // Дата публикации
            $t->timestamp('published_at')->nullable()
                ->comment('Когда задание считается опубликованным');

            // Параметры
            $t->string('status', 16)->default('draft');        // draft|published|archived
            $t->string('visibility', 16)->default('enrolled'); // public|enrolled|private
            $t->unsignedSmallInteger('attempts_limit')->default(0); // 0 = без ограничений
            $t->string('grading_type', 16)->default('manual'); // manual|auto
            $t->unsignedSmallInteger('max_score')->default(100);    // Максимальный балл
            $t->timestamp('due_at')->nullable();                     // Дедлайн (если есть)

            $t->timestamps();
            $t->softDeletes();

            // 🔹 Частые индексы

            // Контекст + сортировка (для выборок по курсу/модулю/уроку с упорядочиванием)
            $t->index(
                ['course_id', 'module_id', 'lesson_id', 'sort'],
                'idx_assignment_context_sort'
            );

            // Состояние + публикация (для "опубликованных и активных" заданий)
            $t->index(
                ['status', 'activity', 'published_at'],
                'idx_assignment_state_published'
            );

            // Дедлайны (поиск ближайших/просроченных)
            $t->index('due_at', 'idx_assignment_due');

            // Локаль (фильтрация по языку)
            $t->index('locale', 'idx_assignment_locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
