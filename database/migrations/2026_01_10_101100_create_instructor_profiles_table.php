<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('instructor_profiles', function (Blueprint $t) {
            $t->id();

            // Служебные поля
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('activity')->default(true);

            // Пользователь (1:N профилей на одного пользователя)
            $t->foreignId('user_id')
                ->constrained()       // references users(id)
                ->cascadeOnDelete();

            // Локализация (RU/EN/KK...)
            $t->string('locale', 10);

            // Основные данные профиля
            $t->string('title')->nullable();      // должность/позиция
            $t->string('slug');                   // ЧПУ (уникален в рамках locale)
            $t->string('short', 255)->nullable();
            $t->text('bio')->nullable();          // HTML/Markdown

            // Доп. поля
            $t->unsignedTinyInteger('experience_years')->nullable();
            $t->decimal('hourly_rate', 10, 2)->nullable();

            // Метрики
            $t->unsignedInteger('rating_count')->default(0);
            $t->decimal('rating_avg', 3, 2)->nullable();  // 0.00–5.00
            $t->unsignedBigInteger('views')->default(0);

            // Соцсети и прочее
            $t->json('social_links')->nullable(); // {github,linkedin,telegram,...}

            // SEO
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->text('meta_desc')->nullable();

            // Тех
            $t->timestamps();
            $t->softDeletes();

            /* ---------- уникальности в рамках локали (как у категорий) ---------- */
            $t->unique(['locale', 'slug']); // ЧПУ уникален в пределах языка

            /* ---------- индексы под частые фильтры/сортировки ---------- */
            $t->index(['locale', 'sort']);            // сортировка в рамках локали
            $t->index(['locale', 'activity']);        // фильтр активных по локали
            $t->index(['locale', 'rating_avg']);      // сортировка/фильтр по рейтингу
            $t->index(['locale', 'user_id']);         // выбор профилей пользователя по локали

            // точечные индексы (для счётчиков/поиска)
            $t->index('views');
            $t->index('rating_count');

            // опционально (MySQL 8+):
            // $t->check("locale IN ('ru','en','kk')");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};
