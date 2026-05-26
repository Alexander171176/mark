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
        Schema::create('school_modules', function (Blueprint $t) {
            $t->id();

            // Родительский курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность модуля');

            // Непереводимые поля
            $t->string('slug')->comment('ЧПУ модуля (уникален в рамках курса)');

            // Публикация
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            // Метаданные
            $t->string('status', 32)->nullable()->comment('Статус: draft/published/archived');
            $t->string('availability', 32)->nullable()->comment('Доступ: unlisted/public/private');

            $t->unsignedTinyInteger('difficulty')->nullable()->comment('Сложность 0-5');
            $t->unsignedInteger('duration')->nullable()->comment('Длительность');

            // Метрики
            $t->unsignedInteger('lessons_count')->default(0)->comment('Количество уроков');
            $t->unsignedInteger('popularity')->default(0)->comment('Популярность');
            $t->unsignedInteger('rating_count')->default(0)->comment('Количество оценок');
            $t->decimal('rating_avg', 3, 2)->default(0)->comment('Средний рейтинг');
            $t->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $t->unsignedBigInteger('likes')->default(0)->comment('Лайки');

            $t->timestamps();

            // Уникальность slug в рамках курса
            $t->unique(['school_course_id', 'slug'], 'uq_school_module_course_slug');

            // Индексы
            $t->index(['school_course_id', 'sort'], 'idx_school_module_course_order');
            $t->index(['status', 'availability', 'published_at'], 'idx_school_module_pub');
            $t->index(['rating_avg', 'rating_count'], 'idx_school_module_rating');
            $t->index(['activity', 'sort'], 'idx_school_module_activity_sort');

            $t->index('popularity', 'idx_school_module_popularity');
            $t->index('views', 'idx_school_module_views');
            $t->index('likes', 'idx_school_module_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_modules');
    }
};
