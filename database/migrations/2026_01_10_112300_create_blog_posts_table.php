<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Блоговые записи/статьи CMS
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $t) {
            $t->id();

            // Автор статьи
            $t->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $t->string('title');                 // Заголовок
            $t->string('slug')->unique();        // ЧПУ (уникальный)
            $t->text('excerpt')->nullable();     // Краткое описание/анонс
            $t->longText('content')->nullable(); // Основной контент (HTML/MD)

            // Состояние публикации
            $t->string('status', 16)
                ->default('draft')               // draft|published|archived
                ->comment('draft|published|archived');
            $t->boolean('activity')->default(true); // Флаг активности
            $t->timestamp('published_at')->nullable(); // Дата/время публикации

            // Метаданные/настройки
            $t->string('cover_image_url')->nullable(); // URL обложки (если без медиабиблиотеки)
            $t->unsignedInteger('reading_time')->default(0); // Оценка времени чтения (мин)
            $t->json('meta')->nullable();          // Произвольные данные (JSON)
            $t->string('locale', 10)->nullable();  // Локаль (ru, en, ...)

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['status', 'published_at'], 'idx_blog_status_published');
            $t->index(['author_id', 'created_at'], 'idx_blog_author_created');
            $t->index(['locale', 'slug'], 'idx_blog_locale_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
