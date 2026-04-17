<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id()->comment('PK');

            // Владелец/автор статьи (сообщество)
            $table->unsignedBigInteger('user_id')
                ->index('articles_user_id_idx')
                ->comment('Автор/владелец статьи (users.id)');

            $table->foreign('user_id', 'articles_user_id_fk')
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
                ->comment('Активность/публикация статьи (видимость)');

            $table->boolean('left')->default(false)->index()->comment('Флаг: показывать в левом блоке');
            $table->boolean('main')->default(false)->index()->comment('Флаг: показывать в главном блоке');
            $table->boolean('right')->default(false)->index()->comment('Флаг: показывать в правом блоке');

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
                ->index('articles_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'articles_moderated_by_fk')
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

            // Резерв для импорта со сторонних сайтов (старое поле)
            $table->text('img')
                ->nullable()
                ->comment('Резервное поле для импорта (старое изображение/ссылка)');

            $table->string('locale', 10)
                ->index()
                ->comment('Локаль статьи (например: ru, kk, en)');

            $table->string('title', 255)->comment('Заголовок статьи');

            $table->string('url', 500)
                ->index()
                ->comment('URL/slug статьи (без домена)');

            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание (анонс)');
            $table->text('description')->nullable()->comment('Текст/контент статьи');

            $table->string('pseudonym', 255)
                ->nullable()
                ->comment('Псевдоним автора (если не хотим показывать имя пользователя)');

            $table->date('published_at')
                ->nullable()
                ->index()
                ->comment('Дата публикации (редакционная, может отличаться от created_at)');

            // Окно показа статьи (планирование)
            $table->timestamp('show_from_at')
                ->nullable()
                ->index()
                ->comment('Показывать статью начиная с (плановая публикация)');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index()
                ->comment('Показывать статью до (окончание показа)');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index()
                ->comment('Счётчик просмотров');

            $table->string('meta_title', 255)->nullable()->comment('SEO: meta title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO: meta keywords');
            $table->text('meta_desc')->nullable()->comment('SEO: meta description');

            $table->timestamps();

            // Уникальность статьи в рамках автора и локали
            $table->unique(['user_id', 'locale', 'title'], 'articles_user_locale_title_unique');
            $table->unique(['user_id', 'locale', 'url'], 'articles_user_locale_url_unique');

            // Частые выборки: автор/локаль/публикация/сортировка
            $table->index(['user_id', 'locale', 'activity', 'sort'], 'articles_user_locale_activity_sort_idx');

            // Публичные выборки "видимые статьи" по локали (опционально, но полезно)
            $table->index(['locale', 'activity', 'show_from_at', 'show_to_at'], 'articles_locale_activity_show_window_idx');

            $table->comment('Блог сообщества: статьи. Локали независимы. Автор = пользователь. Есть модерация и планирование показа.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
