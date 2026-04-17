<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Отзывы пользователей/клиентов
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $t) {
            $t->id();

            // Текст и автор
            $t->text('quote');                     // Текст отзыва (можно HTML/markdown)
            $t->string('author_name');             // Имя автора
            $t->string('author_title')->nullable();// Должность/роль автора (опционально)
            $t->string('company')->nullable();     // Компания/организация (опционально)

            // Медиа/ссылки
            $t->string('avatar_url')->nullable();  // URL аватара (если не используете медиа-библиотеку)
            $t->string('source_url')->nullable();  // Ссылка на источник/пост (если есть)

            // Мета/управление показом
            $t->unsignedTinyInteger('rating')->nullable(); // Оценка 1..5 (если применимо)
            $t->boolean('activity')->default(true);       // Публикация на сайте
            $t->unsignedInteger('sort')->default(0);   // Порядок сортировки
            $t->string('locale', 10)->nullable();          // Локаль (ru, en и т.д.)
            $t->json('meta')->nullable();                  // Произвольные метаданные

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['activity', 'sort'], 'idx_testimonial_active_pos');
            $t->index(['locale', 'sort'], 'idx_testimonial_locale_pos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
