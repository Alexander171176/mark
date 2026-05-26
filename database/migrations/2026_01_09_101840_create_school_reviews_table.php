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
        Schema::create('school_reviews', function (Blueprint $t) {
            $t->id();

            // Автор отзыва
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Автор отзыва');

            // Сущность отзыва: курс, модуль, урок, набор и т.д.
            $t->morphs('reviewable');

            // Оценка и текст
            $t->unsignedTinyInteger('rating')->comment('Оценка 1-5');
            $t->string('title')->nullable()->comment('Заголовок отзыва');
            $t->text('body')->nullable()->comment('Текст отзыва');

            // Модерация
            $t->string('status', 16)
                ->default('pending')
                ->comment('pending|approved|rejected');

            $t->boolean('is_public')->default(true)->comment('Публичность отзыва');
            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            // Счётчики
            $t->unsignedInteger('helpful_count')->default(0)->comment('Количество отметок полезно');
            $t->unsignedInteger('reported_count')->default(0)->comment('Количество жалоб');

            // Дополнительные данные
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Один пользователь — один отзыв на одну сущность
            $t->unique(
                ['user_id', 'reviewable_type', 'reviewable_id'],
                'uq_school_user_reviewable'
            );

            // Индексы
            $t->index(['status', 'published_at'], 'idx_school_review_status_published');
            $t->index('rating', 'idx_school_review_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_reviews');
    }
};
