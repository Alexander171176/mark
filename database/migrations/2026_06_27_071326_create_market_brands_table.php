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
        Schema::create('market_brands', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_brands_user_id_idx')
                ->comment('Создатель/автор бренда (users.id)');

            $table->foreign('user_id', 'market_brands_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->unique('market_brands_url_unique')
                ->comment('Уникальный URL/slug бренда');

            $table->string('website', 500)
                ->nullable()
                ->comment('Официальный сайт бренда');

            $table->string('logo', 500)
                ->nullable()
                ->comment('Логотип бренда');

            $table->text('icon')
                ->nullable()
                ->comment('Иконка бренда: SVG, HTML, путь или код');

            $table->json('social_links')
                ->nullable()
                ->comment('Социальные сети бренда');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_brands_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_brands_activity_idx')
                ->comment('Активность');

            /**
             * Рекламные позиции
             */
            $table->boolean('left')
                ->default(false)
                ->index('market_brands_left_idx')
                ->comment('Показывать бренд в левой рекламной зоне');

            $table->boolean('main')
                ->default(false)
                ->index('market_brands_main_idx')
                ->comment('Показывать бренд в главной рекламной зоне');

            $table->boolean('right')
                ->default(false)
                ->index('market_brands_right_idx')
                ->comment('Показывать бренд в правой рекламной зоне');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_brands_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_brands_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_brands_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'market_brands_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_brands_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_brands_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_brands_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_brands_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_brands_views_idx')
                ->comment('Количество просмотров');

            $table->timestamps();

            /**
             * Быстрые выборки
             */
            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_brands_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_brands_show_window_idx'
            );

            $table->index(
                ['activity', 'sort'],
                'market_brands_activity_sort_idx'
            );

            $table->index(
                ['left', 'main', 'right'],
                'market_brands_placement_idx'
            );

            $table->comment(
                'Маркетплейс: бренды товаров. Основная таблица без переводов.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_brands');
    }
};
