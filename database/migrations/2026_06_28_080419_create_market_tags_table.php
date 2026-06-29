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
        Schema::create('market_tags', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_tags_user_id_idx')
                ->comment('Создатель/автор тега (users.id)');

            $table->foreign('user_id', 'market_tags_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->unique('market_tags_url_unique')
                ->comment('Уникальный URL/slug тега');

            $table->text('icon')
                ->nullable()
                ->comment('Иконка тега: SVG, HTML, путь или код');

            $table->string('color', 50)
                ->nullable()
                ->comment('Цвет тега для бейджа, например: #0ea5e9 или bg-sky-500');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_tags_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_tags_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_tags_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_tags_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_tags_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'market_tags_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_tags_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_tags_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_tags_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_tags_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_tags_views_idx')
                ->comment('Количество просмотров');

            $table->timestamps();

            /**
             * Быстрые выборки
             */
            $table->index(
                ['user_id', 'activity', 'sort'],
                'market_tags_user_activity_sort_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_tags_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_tags_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: теги товаров. Основная таблица без переводов.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_tags');
    }
};
