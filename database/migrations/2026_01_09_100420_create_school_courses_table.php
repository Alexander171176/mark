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
        Schema::create('school_courses', function (Blueprint $t) {
            $t->id();

            // Владелец/преподаватель курса
            $t->foreignId('school_instructor_profile_id')
                ->constrained('school_instructor_profiles')
                ->cascadeOnDelete()
                ->comment('Преподаватель курса');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность курса');

            // Флаги витрины
            $t->boolean('is_new')->default(false)->comment('Новый курс');
            $t->boolean('is_hit')->default(false)->comment('Популярный/рекомендуемый курс');
            $t->boolean('is_sale')->default(false)->comment('Курс со скидкой');

            // Блоки вывода
            $t->boolean('left')->default(false)->comment('Выводить в левой колонке');
            $t->boolean('main')->default(false)->comment('Выводить в главном блоке');
            $t->boolean('right')->default(false)->comment('Выводить в правой колонке');

            // Непереводимые поля
            $t->string('slug')->comment('Уникальный ЧПУ курса');

            // Публикация/видимость
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            // Метаданные курса
            $t->string('level', 32)->nullable()->comment('Уровень: beginner/intermediate/advanced');
            $t->string('status', 32)->nullable()->comment('Статус: draft/published/archived');
            $t->string('availability', 32)->nullable()->comment('Доступность: unlisted/public/private');

            $t->unsignedTinyInteger('difficulty')->nullable()->comment('Сложность курса 0-5');
            $t->unsignedInteger('duration')->nullable()->comment('Общая длительность курса');

            // Метрики
            $t->unsignedInteger('students_count')->default(0)->comment('Количество студентов');
            $t->unsignedInteger('popularity')->default(0)->comment('Популярность');
            $t->unsignedInteger('rating_count')->default(0)->comment('Количество оценок');
            $t->decimal('rating_avg', 3, 2)->default(0)->comment('Средний рейтинг 0.00–5.00');
            $t->unsignedBigInteger('views')->default(0)->comment('Количество просмотров');
            $t->unsignedBigInteger('likes')->default(0)->comment('Количество лайков');

            $t->timestamps();

            // Уникальности
            $t->unique('slug', 'uq_school_course_slug');

            // Индексы
            $t->index(['status', 'availability', 'published_at'], 'idx_school_course_pub');
            $t->index(['main', 'popularity'], 'idx_school_course_main_pop');
            $t->index(['rating_avg', 'rating_count'], 'idx_school_course_rating');
            $t->index(['activity', 'sort'], 'idx_school_course_activity_sort');

            $t->index('is_new', 'idx_school_course_is_new');
            $t->index('is_hit', 'idx_school_course_is_hit');
            $t->index('is_sale', 'idx_school_course_is_sale');
            $t->index('left', 'idx_school_course_left');
            $t->index('main', 'idx_school_course_main');
            $t->index('right', 'idx_school_course_right');
            $t->index('views', 'idx_school_course_views');
            $t->index('likes', 'idx_school_course_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_courses');
    }
};
