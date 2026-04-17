<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Отзывы/рейтинги к курсам, модулям, урокам, бандлам и пр. (polymorphic)
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();

            // Автор отзыва
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // К какой сущности относится отзыв (Course, Module, Lesson, Bundle, ...)
            $t->morphs('reviewable'); // reviewable_type, reviewable_id (индексы добавятся автоматически)

            // Оценка и текст
            $t->unsignedTinyInteger('rating')
                ->comment('1..5');
            $t->string('title')->nullable();  // краткий заголовок
            $t->text('body')->nullable();     // развернутый комментарий

            // Модерация/публикация
            $t->string('status', 16)
                ->default('pending')
                ->comment('pending|approved|rejected');
            $t->boolean('is_public')->default(true);   // скрыть/показать на витрине
            $t->timestamp('published_at')->nullable(); // момент публикации (когда стал виден)

            // Счётчики взаимодействий
            $t->unsignedInteger('helpful_count')->default(0);  // "полезно"
            $t->unsignedInteger('reported_count')->default(0); // жалобы

            // Служебные поля
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Один пользователь — один отзыв на одну сущность
            $t->unique(['user_id', 'reviewable_type', 'reviewable_id'], 'uniq_user_per_reviewable');

            // Частые индексы
            $t->index(['status', 'published_at'], 'idx_status_published');
            $t->index(['rating'], 'idx_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
