<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('market_categories', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_categories_user_id_idx')
                ->comment('Создатель/автор категории (users.id)');

            $table->foreign('user_id', 'market_categories_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Дерево категорий
             */
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->index('market_categories_parent_id_idx')
                ->comment('Родительская категория (market_categories.id), NULL = корневая');

            $table->foreign('parent_id', 'market_categories_parent_id_fk')
                ->references('id')
                ->on('market_categories')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')
                ->default(1)
                ->index('market_categories_level_idx')
                ->comment('Уровень вложенности категории. Корень = 1');

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->unique('market_categories_url_unique')
                ->comment('Уникальный URL/slug категории');

            $table->text('icon')
                ->nullable()
                ->comment('Иконка категории: SVG, HTML, путь или код');

            /**
             * Отображение
             */
            $table->boolean('in_menu')
                ->default(true)
                ->index('market_categories_in_menu_idx')
                ->comment('Показывать категорию в меню');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_categories_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_categories_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_categories_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_categories_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_categories_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'market_categories_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_categories_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_categories_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_categories_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_categories_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_categories_views_idx')
                ->comment('Количество просмотров');

            $table->timestamps();

            /**
             * Быстрые выборки дерева
             */
            $table->index(
                ['parent_id', 'sort'],
                'market_categories_parent_sort_idx'
            );

            $table->index(
                ['user_id', 'parent_id', 'sort'],
                'market_categories_user_parent_sort_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_categories_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_categories_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: общие категории товаров с иерархией. Категории создаются пользователями/магазинами и проходят модерацию.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_categories');
    }
};
