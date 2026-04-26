<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_videos', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Автор / владелец
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_videos_user_id_idx')
                ->comment('Автор/владелец видео (users.id)');

            /**
             * Отображение / сортировка
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('blog_videos_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('blog_videos_activity_idx')
                ->comment('Активность/публикация видео');

            /**
             * 🔥 Из add_is_private_to_videos_table
             */
            $table->boolean('is_private')
                ->default(false)
                ->index('blog_videos_is_private_idx')
                ->comment('Приватное видео (не показывать в публичной части)');

            /**
             * Позиции
             */
            $table->boolean('left')->default(false)->index('blog_videos_left_idx');
            $table->boolean('main')->default(false)->index('blog_videos_main_idx');
            $table->boolean('right')->default(false)->index('blog_videos_right_idx');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('blog_videos_moderation_status_idx');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('blog_videos_moderated_by_idx');

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('blog_videos_moderated_at_idx');

            $table->string('moderation_note', 500)->nullable();

            /**
             * Общий slug
             */
            $table->string('url', 500)
                ->index('blog_videos_url_idx')
                ->comment('URL/slug видео (единый для всех локалей)');

            /**
             * Публикация
             */
            $table->date('published_at')->nullable()->index();
            $table->timestamp('show_from_at')->nullable()->index();
            $table->timestamp('show_to_at')->nullable()->index();

            /**
             * Видео данные
             */
            $table->unsignedInteger('duration')->nullable();

            $table->enum('source_type', ['local', 'youtube', 'vimeo', 'code'])
                ->default('local')
                ->index('blog_videos_source_type_idx');

            $table->text('embed_code')->nullable();

            $table->string('external_video_id', 500)->nullable();

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('blog_videos_views_idx');

            $table->timestamps();

            /**
             * Уникальность slug в рамках автора
             */
            $table->unique(
                ['user_id', 'url'],
                'blog_videos_user_url_unique'
            );

            /**
             * Частые выборки
             */
            $table->index(
                ['user_id', 'activity', 'sort'],
                'blog_videos_user_activity_sort_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'blog_videos_activity_show_window_idx'
            );

            /**
             * FK
             */
            $table->foreign('user_id', 'blog_videos_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('moderated_by', 'blog_videos_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Блог: видео (основная таблица без переводов).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_videos');
    }
};
