<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_rubrics', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Владелец рубрики (сообщество)
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_rubrics_user_id_idx')
                ->comment('Владелец/автор рубрики (users.id)');

            $table->foreign('user_id', 'blog_rubrics_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Дерево рубрик
             */
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->index('blog_rubrics_parent_id_idx')
                ->comment('Родительская рубрика (blog_rubrics.id), NULL = корневая');

            $table->foreign('parent_id', 'blog_rubrics_parent_id_fk')
                ->references('id')
                ->on('blog_rubrics')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')
                ->default(1)
                ->index('blog_rubrics_level_idx')
                ->comment('Уровень вложенности (кэш, 1..N). Корень = 1');

            /**
             * Отображение / активность
             */
            $table->boolean('in_menu')
                ->default(true)
                ->index('blog_rubrics_in_menu_idx')
                ->comment('Показывать рубрику в меню');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('blog_rubrics_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('blog_rubrics_activity_idx')
                ->comment('Активность/публикация рубрики (видимость)');

            /**
             * Модерация
             * 0 = pending (ожидает)
             * 1 = approved (одобрено)
             * 2 = rejected (отклонено)
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('blog_rubrics_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('blog_rubrics_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'blog_rubrics_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('blog_rubrics_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            /**
             * Общие поля рубрики
             */
            $table->text('icon')
                ->nullable()
                ->comment('Иконка рубрики (SVG/HTML/путь/код)');

            $table->string('url', 255)
                ->index('blog_rubrics_url_idx')
                ->comment('Общий URL/slug рубрики (без домена), один для всех локалей');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('blog_rubrics_views_idx')
                ->comment('Счётчик просмотров рубрики');

            $table->timestamps();

            /**
             * Уникальность slug в рамках владельца
             */
            $table->unique(['user_id', 'url'], 'blog_rubrics_user_url_unique');

            /**
             * Быстрые выборки дерева/списков в админке
             */
            $table->index(['user_id', 'parent_id', 'sort'], 'blog_rubrics_user_parent_sort_idx');

            $table->comment('Блог сообщества: рубрики (дерево). Основная таблица без переводов.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_rubrics');
    }
};
