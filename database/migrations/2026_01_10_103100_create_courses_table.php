<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица курсов
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $t) {
            $t->id();

            // Владелец/преподаватель курса
            $t->foreignId('instructor_profile_id')
                ->constrained('instructor_profiles')
                ->cascadeOnDelete(); // удалить профиль -> удалить его курсы


            $t->unsignedInteger('sort')->default(0);         // Для Drag&Drop сортировки
            $t->boolean('activity')->default(true);          // Активен/скрыт в админке/витрине

            $t->boolean('is_new')->default(false)->index();  // в новинках
            $t->boolean('is_hit')->default(false)->index();  // в рекомендованных
            $t->boolean('is_sale')->default(false)->index(); // в распродаже

            $t->boolean('left')->default(false)->index();    // Выводить в левой колонке
            $t->boolean('main')->default(false)->index();    // Выводить в главном
            $t->boolean('right')->default(false)->index();   // Выводить в правой колонке

            // Локаль (единый паттерн как в тегах/категориях)
            $t->string('locale', 10)->default('ru'); // Локаль

            // Основные поля витрины
            $t->string('title');                     // Заголовок курса
            $t->string('slug')->unique();            // ЧПУ-идентификатор
            $t->string('subtitle')->nullable();      // Подзаголовок/оффер
            $t->text('short')->nullable();           // Краткое описание (до ~255 символов)
            $t->longText('description')->nullable(); // Полное описание (markdown/HTML)

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            // Публикация/видимость
            $t->timestamp('published_at')->nullable();           // Когда опубликован

            // Метаданные содержания
            $t->string('level', 32)->nullable();           // beginner|intermediate|advanced (Уровень)
            $t->string('status', 32)->nullable();          // draft|published|archived (Состояние)
            $t->string('availability', 32)->nullable();    // unlisted|public|private (Доступность)

            $t->unsignedTinyInteger('difficulty')->nullable();   // Трудность в баллах 0..5
            $t->unsignedInteger('duration')->nullable(); // Общая длительность

            // Метрики для быстрого рендера
            $t->unsignedInteger('students_count')->default(0);      // Кол-во студентов
            $t->unsignedInteger('popularity')->default(0);          // Популярность
            $t->unsignedInteger('rating_count')->default(0);        // Кол-во оценок
            $t->decimal('rating_avg', 3, 2)->default(0); // Средняя оценка 0.00–5.00
            $t->unsignedBigInteger('views')->default(0);            // Просмотры
            $t->unsignedBigInteger('likes')->default(0);            // Лайки

            // Тех. поля
            $t->timestamps();
            $t->softDeletes();

            /**
             * Индексы для частых выборок
             */

            // публикация / доступность
            $t->index(['status', 'availability', 'published_at'], 'idx_course_pub');

            // "на главной" + популярность (раньше был is_featured)
            $t->index(['main', 'popularity'], 'idx_course_main_pop');

            // рейтинг
            $t->index(['rating_avg', 'rating_count'], 'idx_course_rating');

            // активность + сортировка
            $t->index(['activity', 'sort'], 'idx_course_activity_sort');

            // отдельные поля
            $t->index('locale');
            $t->index('views');
            $t->index('likes');

            // ВАЖНО:
            // - НЕ делаем $t->index('slug'), т.к. уже есть ->unique()
            // - НЕ делаем $t->index('instructor_profile_id'), т.к. foreignId()->constrained() уже создаёт индекс

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
