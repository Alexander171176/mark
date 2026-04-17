<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id()->comment('PK');

            // Владелец тега (сообщество)
            $table->unsignedBigInteger('user_id')
                ->index('tags_user_id_idx')
                ->comment('Владелец/автор тега (users.id)');

            $table->foreign('user_id', 'tags_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->index()
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index()
                ->comment('Активность/публикация тега (видимость)');

            /**
             * Модерация (админом/владельцем площадки)
             * 0 = pending, 1 = approved, 2 = rejected
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index()
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('tags_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'tags_moderated_by_fk')
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
                ->comment('Иконка тега (SVG/HTML/путь/код)');

            $table->string('locale', 10)
                ->index()
                ->comment('Локаль тега (например: ru, kk, en)');

            $table->string('name', 255)
                ->comment('Название тега');

            $table->string('slug', 255)
                ->index()
                ->comment('Slug тега (без домена)');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание (анонс)');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание тега');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index()
                ->comment('Счётчик просмотров тега');

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

            // Уникальность тега в рамках владельца и локали
            $table->unique(['user_id', 'locale', 'name'], 'tags_user_locale_name_unique');
            $table->unique(['user_id', 'locale', 'slug'], 'tags_user_locale_slug_unique');

            // Частые выборки: автор/локаль/активность/сортировка
            $table->index(['user_id', 'locale', 'activity', 'sort'], 'tags_user_locale_activity_sort_idx');

            $table->comment('Блог сообщества: теги. Локали независимы. Владелец = пользователь. Есть модерация.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
