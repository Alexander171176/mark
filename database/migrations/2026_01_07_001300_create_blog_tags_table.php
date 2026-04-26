<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Владелец тега
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_tags_user_id_idx')
                ->comment('Владелец/автор тега (users.id)');

            $table->foreign('user_id', 'blog_tags_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Отображение / сортировка
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('blog_tags_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('blog_tags_activity_idx')
                ->comment('Активность/публикация тега');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('blog_tags_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('blog_tags_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'blog_tags_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('blog_tags_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Общие поля
             */
            $table->text('icon')
                ->nullable()
                ->comment('Иконка тега (SVG/HTML/код)');

            $table->string('slug', 255)
                ->index('blog_tags_slug_idx')
                ->comment('Slug тега (единый для всех локалей)');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('blog_tags_views_idx')
                ->comment('Счётчик просмотров');

            $table->timestamps();

            /**
             * Уникальность slug в рамках владельца
             */
            $table->unique(
                ['user_id', 'slug'],
                'blog_tags_user_slug_unique'
            );

            /**
             * Частые выборки
             */
            $table->index(
                ['user_id', 'activity', 'sort'],
                'blog_tags_user_activity_sort_idx'
            );

            $table->comment('Блог: теги (основная таблица без переводов).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tags');
    }
};
