<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Владелец/автор статьи (сообщество)
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_articles_user_id_idx')
                ->comment('Автор/владелец статьи (users.id)');

            $table->foreign('user_id', 'blog_articles_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Отображение / сортировка / активность
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('blog_articles_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('blog_articles_activity_idx')
                ->comment('Активность/публикация статьи (видимость)');

            $table->boolean('left')
                ->default(false)
                ->index('blog_articles_left_idx')
                ->comment('Флаг: показывать в левом блоке');

            $table->boolean('main')
                ->default(false)
                ->index('blog_articles_main_idx')
                ->comment('Флаг: показывать в главном блоке');

            $table->boolean('right')
                ->default(false)
                ->index('blog_articles_right_idx')
                ->comment('Флаг: показывать в правом блоке');

            /**
             * Модерация
             * 0 = pending (ожидает)
             * 1 = approved (одобрено)
             * 2 = rejected (отклонено)
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('blog_articles_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('blog_articles_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'blog_articles_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('blog_articles_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            /**
             * Резерв для импорта со сторонних сайтов
             */
            $table->text('img')
                ->nullable()
                ->comment('Резервное поле для импорта (старое изображение/ссылка)');

            /**
             * Общий URL/slug статьи
             */
            $table->string('url', 500)
                ->index('blog_articles_url_idx')
                ->comment('Общий URL/slug статьи (без домена), один для всех локалей');

            /**
             * Дата публикации / окно показа
             */
            $table->date('published_at')
                ->nullable()
                ->index('blog_articles_published_at_idx')
                ->comment('Дата публикации (редакционная, может отличаться от created_at)');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('blog_articles_show_from_at_idx')
                ->comment('Показывать статью начиная с (плановая публикация)');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('blog_articles_show_to_at_idx')
                ->comment('Показывать статью до (окончание показа)');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('blog_articles_views_idx')
                ->comment('Счётчик просмотров');

            $table->timestamps();

            /**
             * Уникальность slug в рамках автора
             */
            $table->unique(['user_id', 'url'], 'blog_articles_user_url_unique');

            /**
             * Частые выборки: автор/активность/сортировка
             */
            $table->index(
                ['user_id', 'activity', 'sort'],
                'blog_articles_user_activity_sort_idx'
            );

            /**
             * Публичные выборки "видимые статьи"
             */
            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'blog_articles_activity_show_window_idx'
            );

            $table->comment('Блог сообщества: статьи. Основная таблица без переводов. Автор = пользователь. Есть модерация и планирование показа.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
