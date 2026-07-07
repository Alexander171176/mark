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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('cms_pages_user_id_idx')
                ->comment('Создатель/автор страницы (users.id)');

            $table->foreign('user_id', 'cms_pages_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Дерево страниц
             */
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->index('cms_pages_parent_id_idx')
                ->comment('Родительская страница (cms_pages.id), NULL = корневая');

            $table->foreign('parent_id', 'cms_pages_parent_id_fk')
                ->references('id')
                ->on('cms_pages')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')
                ->default(1)
                ->index('cms_pages_level_idx')
                ->comment('Уровень вложенности страницы. Корень = 1. Максимум 3 уровня');

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->unique('cms_pages_url_unique')
                ->comment('Уникальный URL/slug страницы, например: /blog, /school, /market, /contacts');

            $table->text('icon')
                ->nullable()
                ->comment('Иконка страницы: SVG, HTML, путь или код');

            /**
             * Отображение
             */
            $table->boolean('in_menu')
                ->default(false)
                ->index('cms_pages_in_menu_idx')
                ->comment('Показывать страницу в меню/header');

            $table->boolean('in_footer')
                ->default(false)
                ->index('cms_pages_in_footer_idx')
                ->comment('Показывать страницу в footer');

            $table->boolean('show_content')
                ->default(false)
                ->index('cms_pages_show_content_idx')
                ->comment('Показывать собственный HTML-контент страницы из CMS');

            $table->boolean('show_seo')
                ->default(false)
                ->index('cms_pages_show_seo_idx')
                ->comment('Использовать собственные SEO-мета поля страницы из CMS');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('cms_pages_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('cms_pages_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('cms_pages_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('cms_pages_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('cms_pages_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('cms_pages_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('cms_pages_views_idx')
                ->comment('Количество просмотров');

            $table->timestamps();

            /**
             * Быстрые выборки дерева
             */
            $table->index(
                ['parent_id', 'sort'],
                'cms_pages_parent_sort_idx'
            );

            $table->index(
                ['user_id', 'parent_id', 'sort'],
                'cms_pages_user_parent_sort_idx'
            );

            $table->index(
                ['activity', 'status'],
                'cms_pages_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'cms_pages_show_window_idx'
            );

            $table->index(
                ['activity', 'in_menu', 'sort'],
                'cms_pages_menu_idx'
            );

            $table->index(
                ['activity', 'in_footer', 'sort'],
                'cms_pages_footer_idx'
            );

            $table->comment(
                'CMS: страницы сайта с трёхуровневой иерархией, меню, футером, HTML-контентом и SEO-настройками.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
