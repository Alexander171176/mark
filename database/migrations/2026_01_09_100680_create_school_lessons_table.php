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
        Schema::create('school_lessons', function (Blueprint $t) {
            $t->id();

            // Родительский модуль
            $t->foreignId('school_module_id')
                ->constrained('school_modules')
                ->cascadeOnDelete()
                ->comment('Модуль');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность урока');

            // Непереводимые поля
            $t->string('slug')->comment('ЧПУ урока (уникален в рамках модуля)');

            /**
             * Полиморфный контент
             */
            $t->nullableMorphs('content'); // content_type + content_id

            // Публикация
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            $t->string('status', 32)->nullable()->comment('Статус: draft/published/archived');
            $t->string('availability', 32)->nullable()->comment('Доступность');

            // Доступ / монетизация
            $t->string('access_type', 32)
                ->default('free')
                ->comment('Тип доступа: free, paid, bonus');

            // Метаданные
            $t->unsignedTinyInteger('difficulty')->nullable()->comment('Сложность 0-5');
            $t->unsignedInteger('duration')->nullable()->comment('Длительность');

            // Превью
            $t->string('preview_mode', 32)->nullable()->comment('Тип превью');
            $t->unsignedInteger('preview_value')->nullable()->comment('Значение превью');

            // Метрики
            $t->unsignedInteger('popularity')->default(0)->comment('Популярность');
            $t->unsignedInteger('rating_count')->default(0)->comment('Количество оценок');
            $t->decimal('rating_avg', 3, 2)->default(0)->comment('Средний рейтинг');
            $t->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $t->unsignedBigInteger('likes')->default(0)->comment('Лайки');

            $t->timestamps();

            // Уникальность
            $t->unique(['school_module_id', 'slug'], 'uq_school_lesson_module_slug');

            // Индексы
            $t->index(['school_module_id', 'sort'], 'idx_school_lesson_module_order');
            $t->index(['status', 'availability', 'published_at'], 'idx_school_lesson_pub');
            $t->index('activity', 'idx_school_lesson_activity');

            $t->index('access_type', 'idx_school_lesson_access_type');
            $t->index('difficulty', 'idx_school_lesson_difficulty');
            $t->index('duration', 'idx_school_lesson_duration');
            $t->index('preview_mode', 'idx_school_lesson_preview_mode');

            $t->index(['rating_avg', 'rating_count'], 'idx_school_lesson_rating');
            $t->index('popularity', 'idx_school_lesson_popularity');
            $t->index('views', 'idx_school_lesson_views');
            $t->index('likes', 'idx_school_lesson_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_lessons');
    }
};
