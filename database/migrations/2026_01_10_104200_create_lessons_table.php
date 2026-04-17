<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица уроков
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $t) {
            $t->id();

            /**
             * Родительский модуль (жёсткий FK)
             * Удалили модуль — удалили все его уроки.
             */
            $t->foreignId('module_id')
                ->constrained('modules')
                ->cascadeOnDelete();

            // Управление порядком и активностью
            $t->unsignedInteger('sort')->default(0);     // Для Drag&Drop сортировки внутри модуля
            $t->boolean('activity')->default(true);      // Активен/скрыт в админке/витрине

            // Локаль (по аналогии с модулями/курсами)
            $t->string('locale', 10)
                ->default('ru')
                ->comment('Локаль урока (ru/en/kk и т.д.)');

            // Основные поля урока
            $t->string('title');                     // Заголовок урока
            $t->string('slug');                      // Слаг, уникален в пределах module_id
            $t->string('subtitle')->nullable();      // Подзаголовок/оффер
            $t->text('short')->nullable();           // Краткое описание (анонс / summary)
            $t->longText('description')->nullable(); // Полное описание (markdown/HTML)

            // SEO
            $t->string('meta_title', 160)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->string('meta_desc', 255)->nullable();

            /**
             * Полиморфный контент урока:
             * Article, Video, Quiz, Assignment, Live и т.д.
             *
             * content_type: FQCN модели (App\Models\Admin\Article\Article и т.п.)
             * content_id:   первичный ключ записи в соответствующей таблице
             *
             * nullableMorphs('content') создаст:
             *  - content_type (nullable, indexed)
             *  - content_id   (nullable, indexed)
             *  - составной индекс (content_type, content_id)
             */
            $t->nullableMorphs('content');

            // Публикация / видимость
            $t->timestamp('published_at')->nullable()
                ->comment('Когда урок считается опубликованным на витрине');

            $t->string('status', 32)
                ->nullable()
                ->comment('Состояние урока: draft, published, archived и т.п.');

            $t->string('availability', 32)
                ->nullable()
                ->comment('Доступность: public, unlisted, private и т.п.');

            // Тип доступа / монетизация (бесплатный, платный, бонусный и т.д.)
            $t->string('access_type', 32)
                ->default('free')
                ->comment('Тип доступа к уроку: free, paid, bonus и т.п.');

            // Метаданные содержания
            $t->unsignedTinyInteger('difficulty')->nullable();   // Трудность в баллах 0..5

            // Объём/длительность урока (единицы определяются бизнес-логикой)
            $t->unsignedInteger('duration')
                ->nullable()
                ->comment('Длительность урока (в секундах/минутах/условных единицах, определяется логикой приложения)');

            /**
             * Режим превью:
             *  - null / "none"   — превью нет
             *  - "full"          — урок целиком доступен как превью
             *  - "percent"       — preview_value = % контента (0..100)
             *  - "duration"      — preview_value = время (сек/мин и т.п.)
             *  - "chars"         — preview_value = кол-во символов текста
             * При необходимости можно добавлять новые режимы.
             */
            $t->string('preview_mode', 32)
                ->nullable()
                ->comment('Режим превью: none, full, percent, duration, chars и т.п.');

            $t->unsignedInteger('preview_value')
                ->nullable()
                ->comment('Числовой лимит превью, смысл зависит от preview_mode');

            // Денормы для витрины/аналитики
            $t->unsignedInteger('popularity')->default(0);   // Популярность
            $t->unsignedInteger('rating_count')->default(0); // Кол-во оценок
            $t->decimal('rating_avg', 3, 2)->default(0);     // Средняя оценка 0.00–5.00
            $t->unsignedBigInteger('views')->default(0);     // Просмотры
            $t->unsignedBigInteger('likes')->default(0);     // Лайки/реакции

            // Тех. поля
            $t->timestamps();
            $t->softDeletes();

            // Уникальность слага в рамках модуля
            $t->unique(['module_id', 'slug'], 'uq_lesson_module_slug');

            /**
             * Индексы для частых выборок
             */

            // сортировка уроков в рамках модуля (для Drag&Drop и вывода)
            $t->index(['module_id', 'sort'], 'idx_lesson_module_order');

            // публикация / доступность
            $t->index(['status', 'availability', 'published_at'], 'idx_lesson_pub');

            // фильтрация по активности/локали
            $t->index('activity', 'idx_lesson_activity');
            $t->index('locale', 'idx_lesson_locale');

            // тип доступа / монетизация
            $t->index('access_type', 'idx_lesson_access_type');

            // сложность урока
            $t->index('difficulty', 'idx_lesson_difficulty');

            // длительность урока
            $t->index('duration', 'idx_lesson_duration');

            // режим превью
            $t->index('preview_mode', 'idx_lesson_preview_mode');

            // рейтинг
            $t->index(['rating_avg', 'rating_count'], 'idx_lesson_rating');

            // популярность
            $t->index('popularity', 'idx_lesson_popularity');

            // популярность по просмотрам и лайкам
            $t->index('views', 'idx_lesson_views');
            $t->index('likes', 'idx_lesson_likes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
