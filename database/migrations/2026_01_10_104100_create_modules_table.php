<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица модулей курса
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $t) {
            $t->id();

            // Родительский курс
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete(); // удаляем курс — удаляем его модули

            $t->unsignedInteger('sort')->default(0);         // Для Drag&Drop сортировки
            $t->boolean('activity')->default(true);          // Активен/скрыт в админке/витрине

            // Локаль (единый паттерн как в тегах/категориях)
            $t->string('locale', 10)->default('ru'); // Локаль

            // Основные поля модуля
            $t->string('title');                     // Заголовок модуля
            $t->string('slug');                      // Слаг (уникален в рамках course_id)
            $t->string('subtitle')->nullable();      // Подзаголовок/оффер
            $t->text('short')->nullable();           // Краткое описание
            $t->longText('description')->nullable(); // Полное описание (markdown/HTML)

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            // Публикация/видимость
            $t->timestamp('published_at')->nullable();           // Когда опубликован

            // Метаданные содержания
            $t->string('status', 32)->nullable();          // draft|published|archived (Состояние)
            $t->string('availability', 32)->nullable();    // unlisted|public|private (Доступность)

            $t->unsignedTinyInteger('difficulty')->nullable();   // Трудность в баллах 0..5
            $t->unsignedInteger('duration')->nullable(); // Общая длительность

            $t->unsignedInteger('lessons_count')->default(0);    // Денорм: кол-во уроков в модуле
            $t->unsignedInteger('popularity')->default(0);       // Популярность
            $t->unsignedInteger('rating_count')->default(0);     // Кол-во оценок
            $t->decimal('rating_avg', 3, 2)->default(0);         // Средняя оценка 0.00–5.00
            $t->unsignedBigInteger('views')->default(0);         // Просмотры
            $t->unsignedBigInteger('likes')->default(0);         // Лайки

            // Тех. поля
            $t->timestamps();
            $t->softDeletes();

            // Уникальность слага в пределах курса (одно и то же имя — в разных курсах можно)
            $t->unique(['course_id', 'slug'], 'uq_module_course_slug');

            /**
             * Индексы для частых выборок
             */

            // сортировка модулей в рамках курса (для Drag&Drop и вывода)
            $t->index(['course_id', 'sort'], 'idx_module_course_order');

            // публикация / доступность
            $t->index(['status', 'availability', 'published_at'], 'idx_module_pub');

            // рейтинг
            $t->index(['rating_avg', 'rating_count'], 'idx_module_rating');

            // активность + сортировка
            $t->index(['activity', 'sort'], 'idx_module_activity_sort');

            // популярность отдельно (если будешь сортировать модули по популярности)
            $t->index('popularity');

            // отдельные поля
            $t->index('locale');
            $t->index('views');
            $t->index('likes');

            // ВАЖНО:
            // - НЕ делаем отдельный индекс на course_id: foreignId()->constrained() уже создаёт индекс
            // - НЕ делаем индекс на slug, т.к. он уже участвует в составном unique(['course_id', 'slug'])
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
