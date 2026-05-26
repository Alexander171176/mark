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
        Schema::create('school_instructor_profiles', function (Blueprint $t) {
            $t->id();

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность профиля');

            // Пользователь (владелец профиля)
            $t->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Связь с пользователем');

            // Основные непереводимые поля
            $t->string('slug')->comment('Уникальный ЧПУ (единый для всех языков)');

            // Доп. поля
            $t->unsignedTinyInteger('experience_years')->nullable()->comment('Опыт работы в годах');
            $t->decimal('hourly_rate', 10, 2)->nullable()->comment('Почасовая ставка');

            // Метрики
            $t->unsignedInteger('rating_count')->default(0)->comment('Количество оценок');
            $t->decimal('rating_avg', 3, 2)->nullable()->comment('Средний рейтинг (0.00–5.00)');
            $t->unsignedBigInteger('views')->default(0)->comment('Количество просмотров');

            // Соцсети
            $t->json('social_links')->nullable()->comment('Социальные сети (JSON)');

            $t->timestamps();

            // Уникальность slug
            $t->unique('slug', 'uq_school_instructor_slug');

            // Индексы
            $t->index(['activity', 'sort'], 'idx_school_instructor_activity_sort');
            $t->index('views', 'idx_school_instructor_views');
            $t->index('rating_count', 'idx_school_instructor_rating_count');
            $t->index('rating_avg', 'idx_school_instructor_rating_avg');
            $t->index('user_id', 'idx_school_instructor_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_instructor_profiles');
    }
};
