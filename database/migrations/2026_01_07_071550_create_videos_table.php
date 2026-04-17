<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id()->comment('PK');

            // Владелец/автор видео (сообщество)
            $table->unsignedBigInteger('user_id')
                ->index('videos_user_id_idx')
                ->comment('Автор/владелец видео (users.id)');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('videos_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('videos_activity_idx')
                ->comment('Активность/публикация видео (видимость)');

            $table->boolean('left')->default(false)->index('videos_left_idx')->comment('Флаг: показывать в левом блоке');
            $table->boolean('main')->default(false)->index('videos_main_idx')->comment('Флаг: показывать в главном блоке');
            $table->boolean('right')->default(false)->index('videos_right_idx')->comment('Флаг: показывать в правом блоке');

            $table->string('locale', 10)
                ->index('videos_locale_idx')
                ->comment('Локаль видео (например: ru, kk, en)');

            /**
             * Модерация (админом/владельцем площадки)
             * 0 = pending, 1 = approved, 2 = rejected
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('videos_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('videos_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('videos_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            $table->string('title', 255)->comment('Название видео');

            $table->string('url', 500)
                ->index('videos_url_idx')
                ->comment('URL/slug видео (без домена)');

            $table->string('short', 255)->nullable()->comment('Краткое описание (анонс)');
            $table->text('description')->nullable()->comment('Описание видео');

            $table->string('pseudonym', 255)
                ->nullable()
                ->comment('Автор/канал (отображаемое имя, если нужно)');

            $table->date('published_at')
                ->nullable()
                ->index('videos_published_at_idx')
                ->comment('Дата публикации (редакционная, может отличаться от created_at)');

            // Окно показа видео (планирование)
            $table->timestamp('show_from_at')
                ->nullable()
                ->index('videos_show_from_at_idx')
                ->comment('Показывать видео начиная с (плановая публикация)');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('videos_show_to_at_idx')
                ->comment('Показывать видео до (окончание показа)');

            $table->unsignedInteger('duration')
                ->nullable()
                ->comment('Длительность видео в секундах');

            $table->enum('source_type', ['local', 'youtube', 'vimeo', 'code'])
                ->default('local')
                ->index('videos_source_type_idx')
                ->comment('Источник видео: local/youtube/vimeo/code');

            $table->text('embed_code')
                ->nullable()
                ->comment('HTML/embed-код (для source_type=code)');

            $table->string('external_video_id', 500)
                ->nullable()
                ->comment('ID/URL/внешний идентификатор (youtube/vimeo/local/code)');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('videos_views_idx')
                ->comment('Счётчик просмотров видео');

            $table->string('meta_title', 255)->nullable()->comment('SEO: meta title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO: meta keywords');
            $table->text('meta_desc')->nullable()->comment('SEO: meta description');

            $table->timestamps();

            // Уникальность видео в рамках автора и локали
            $table->unique(['user_id', 'locale', 'title'], 'videos_user_locale_title_unique');
            $table->unique(['user_id', 'locale', 'url'], 'videos_user_locale_url_unique');

            // Частые выборки: автор/локаль/активность/сортировка
            $table->index(['user_id', 'locale', 'activity', 'sort'], 'videos_user_locale_activity_sort_idx');

            // Публичные выборки "видимые видео" по локали
            $table->index(['locale', 'activity', 'show_from_at', 'show_to_at'], 'videos_locale_activity_show_window_idx');

            // --- FK (явные имена) ---
            $table->foreign('user_id', 'videos_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('moderated_by', 'videos_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Блог сообщества: видео. Локали независимы. Автор = пользователь. Есть модерация и планирование показа.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
