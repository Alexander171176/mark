<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Статические/маркетинговые страницы CMS
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $t) {
            $t->id();

            // Иерархия страниц (родительская страница)
            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('pages')
                ->nullOnDelete();

            // Автор (кто создал/ведёт страницу)
            $t->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $t->string('title');                 // Заголовок страницы
            $t->string('slug')->unique();        // Уникальный URL-слизень (ЧПУ)
            $t->text('excerpt')->nullable();     // Короткое описание/подзаголовок
            $t->longText('content')->nullable(); // Основной контент (HTML/JSON/Markdown)

            // Публикация и состояния
            $t->string('status', 16)
                ->default('draft')               // draft|published|archived
                ->comment('draft|published|archived');
            $t->boolean('activity')->default(true); // Флаг активности
            $t->timestamp('published_at')->nullable(); // Дата публикации

            // Настройки отображения
            $t->string('template')->nullable();  // Имя blade/компонента для шаблона
            $t->string('layout')->nullable();    // Макет, если используется
            $t->string('locale', 10)->nullable()->comment('Напр. en, ru, ru-RU');

            // Сортировка в пределах родителя
            $t->unsignedInteger('sort')->default(0);

            // Произвольные данные (например, блоки/настройки)
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['parent_id', 'sort'], 'idx_pages_parent_sort');
            $t->index(['status', 'published_at'], 'idx_pages_status_published');
            $t->index(['locale', 'slug'], 'idx_pages_locale_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
