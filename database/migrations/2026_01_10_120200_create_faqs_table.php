<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Часто задаваемые вопросы (FAQ)
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $t) {
            $t->id();

            // Основные поля
            $t->string('question');                 // Вопрос (заголовок)
            $t->text('answer');                     // Ответ (HTML/markdown/текст)
            $t->string('slug')->unique();           // ЧПУ для ссылки/якоря
            $t->string('category', 128)->nullable();// Категория/группа (опционально)
            $t->string('locale', 10)->nullable();   // Локаль (например, ru, en)

            // Публикация и сортировка
            $t->boolean('activity')->default(true);// Опубликовано?
            $t->unsignedInteger('sort')->default(0); // Порядок отображения

            // Дополнительно
            $t->json('meta')->nullable();           // Произвольные метаданные

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['activity', 'sort'], 'idx_faq_active_pos');
            $t->index(['category', 'locale'], 'idx_faq_category_locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
