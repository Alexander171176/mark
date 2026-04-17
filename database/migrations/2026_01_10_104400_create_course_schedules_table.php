<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Таблица расписаний / потоков курса.
     * Один курс может иметь множество потоков (групп).
     * Студенты записываются на конкретный поток.
     */
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $t) {
            $t->id();

            // К какому курсу относится поток
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // Ведущий преподаватель этого потока
            $t->foreignId('instructor_profile_id')
                ->nullable()
                ->constrained('instructor_profiles')
                ->nullOnDelete();

            $t->unsignedInteger('sort')->default(0);  // Для сортировки

            // Быстрое включение/выключение (как у всех сущностей в админке)
            $t->boolean('activity')->default(true);

            $t->string('locale', 10)
                ->default('ru')
                ->comment('Локаль расписания (ru/en/kk и т.д.)');

            // Название потока (например: «Поток Апрель 2025»)
            $t->string('title');
            $t->string('slug')->unique();             // ЧПУ
            $t->string('subtitle')->nullable();       // Подзаголовок/оффер
            $t->text('short')->nullable();            // Краткое описание (анонс / summary)
            $t->longText('description')->nullable();  // Полное описание (markdown/HTML)

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            // Даты проведения потока (когда идёт обучение)
            $t->timestamp('starts_at')->nullable();    // Дата начала потока
            $t->timestamp('ends_at')->nullable();      // Дата окончания потока

            // Окно набора студентов (период записи)
            $t->timestamp('enroll_starts_at')->nullable(); // Когда открывается запись
            $t->timestamp('enroll_ends_at')->nullable();   // Когда запись закрывается

            // Максимальное число участников
            // 0 = безлимит (принимаем сколько угодно)
            $t->unsignedInteger('capacity')->default(0);

            // Формат проведения:
            // true  = онлайн
            // false = офлайн (в аудитории)
            $t->boolean('is_online')->default(true);

            // Адрес офлайн-группы (если is_online = false)
            $t->string('location')->nullable();

            // Ссылка на Zoom/Meet/Teams (если is_online = true)
            $t->string('meeting_url')->nullable();

            // Часовой пояс потока (напр. Asia/Almaty)
            $t->string('timezone', 64)->default('UTC');

            /**
             * Статус потока:
             *  - draft     — черновик, скрыт
             *  - published — опубликован, можно записываться
             *  - archived  — завершён и скрыт
             *  - cancelled — поток отменён
             */
            $t->string('status', 16)->default('draft');

            $t->unsignedBigInteger('views')->default(0);     // Просмотры

            // Заметки администратора (не показываются студентам)
            $t->text('notes')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // ================== ИНДЕКСЫ ==================

            // Чаще всего: выбрать потоки конкретного курса по статусу
            $t->index(
                ['course_id', 'status'],
                'idx_course_schedule_course_status'
            );

            // Списки в админке: фильтр по локали, активности и статусу
            $t->index(
                ['locale', 'activity', 'status'],
                'idx_course_schedule_locale_activity_status'
            );

            // Списки с сортировкой по sort (обычно по активности)
            $t->index(
                ['activity', 'sort'],
                'idx_course_schedule_activity_sort'
            );

            // Поиск ближайших/прошедших потоков
            $t->index(
                ['starts_at', 'ends_at'],
                'idx_course_schedule_dates'
            );

            // Потоки, доступные сейчас для записи
            $t->index(
                ['enroll_starts_at', 'enroll_ends_at'],
                'idx_course_schedule_enroll_window'
            );

            // Разделение по формату и часовому поясу
            $t->index(
                ['is_online', 'timezone'],
                'idx_course_schedule_format'
            );

            // Популярные потоки (подбор по просмотрам)
            $t->index(
                ['views'],
                'idx_course_schedule_views'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};
