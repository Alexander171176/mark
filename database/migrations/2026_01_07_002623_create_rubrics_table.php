<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id()->comment('PK');

            // Владелец рубрики (сообщество)
            $table->unsignedBigInteger('user_id')
                ->index('rubrics_user_id_idx')
                ->comment('Владелец/автор рубрики (users.id)');

            $table->foreign('user_id', 'rubrics_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // Дерево рубрик
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->index('rubrics_parent_id_idx')
                ->comment('Родительская рубрика (rubrics.id), NULL = корневая');

            $table->foreign('parent_id', 'rubrics_parent_id_fk')
                ->references('id')
                ->on('rubrics')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')
                ->default(1)
                ->index()
                ->comment('Уровень вложенности (кэш, 1..N). Корень = 1');

            $table->boolean('in_menu')
                ->default(true)
                ->index()
                ->comment('Показывать рубрику в меню');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index()
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index()
                ->comment('Активность/публикация рубрики (видимость)');

            /**
             * Модерация (владельцем/админом)
             * 0 = pending (ожидает), 1 = approved (одобрено), 2 = rejected (отклонено)
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index()
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('rubrics_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'rubrics_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index()
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            $table->text('icon')
                ->nullable()
                ->comment('Иконка рубрики (SVG/HTML/путь/код)');

            $table->string('locale', 10)
                ->index()
                ->comment('Локаль рубрики (например: ru, kk, en)');

            $table->string('title', 255)
                ->comment('Название рубрики');

            $table->string('url', 255)
                ->index()
                ->comment('URL/slug рубрики (без домена)');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание (анонс)');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание рубрики');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index()
                ->comment('Счётчик просмотров рубрики');

            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO: meta title');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO: meta keywords');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO: meta description');

            $table->timestamps(); // created_at / updated_at

            /**
             * Ключи и индексы
             */

            // Уникальность рубрики в рамках владельца и локали
            $table->unique(['user_id', 'locale', 'title'], 'rubrics_user_locale_title_unique');
            $table->unique(['user_id', 'locale', 'url'], 'rubrics_user_locale_url_unique');

            // Быстрые выборки дерева/списков в админке
            $table->index(['user_id', 'locale', 'parent_id', 'sort'], 'rubrics_user_locale_parent_sort_idx');

            $table->comment('Блог сообщества: рубрики (дерево). Локали независимы. Владелец = пользователь. Есть модерация.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubrics');
    }
};
