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
        Schema::create('market_attribute_groups', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_attribute_groups_user_id_idx')
                ->comment('Создатель группы (users.id)');

            $table->foreign(
                'user_id',
                'market_attribute_groups_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('code', 100)
                ->unique('market_attribute_groups_code_unique')
                ->comment('Уникальный системный код группы (не переводится)');

            $table->text('icon')
                ->nullable()
                ->comment('SVG, HTML либо путь к иконке');

            $table->string('color', 50)
                ->nullable()
                ->comment('Цвет группы (#0ea5e9, bg-sky-500 и т.п.)');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_attribute_groups_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_attribute_groups_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_attribute_groups_status_idx')
                ->comment('Статус: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_attribute_groups_moderation_status_idx')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_attribute_groups_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign(
                'moderated_by',
                'market_attribute_groups_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_attribute_groups_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_attribute_groups_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_attribute_groups_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_attribute_groups_show_to_at_idx')
                ->comment('Окончание показа');

            $table->timestamps();

            /**
             * Индексы
             */
            $table->index(
                ['user_id', 'activity', 'sort'],
                'market_attribute_groups_user_activity_sort_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_attribute_groups_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_attribute_groups_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: группы характеристик. Основная таблица без переводов.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attribute_groups');
    }
};
