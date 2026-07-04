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
        Schema::create('market_attributes', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Группа характеристик
             */
            $table->unsignedBigInteger('market_attribute_group_id')
                ->index('market_attributes_group_id_idx')
                ->comment('Группа характеристик (market_attribute_groups.id)');

            $table->foreign(
                'market_attribute_group_id',
                'market_attributes_group_id_fk'
            )
                ->references('id')
                ->on('market_attribute_groups')
                ->cascadeOnDelete();

            /**
             * Создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_attributes_user_id_idx')
                ->comment('Создатель характеристики (users.id)');

            $table->foreign(
                'user_id',
                'market_attributes_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('code', 100)
                ->unique('market_attributes_code_unique')
                ->comment('Уникальный системный код характеристики (не переводится)');

            $table->text('icon')
                ->nullable()
                ->comment('SVG, HTML либо путь к иконке');

            $table->string('color', 50)
                ->nullable()
                ->comment('Цвет характеристики');

            /**
             * Тип значения
             */
            $table->string('type', 50)
                ->default('string')
                ->index('market_attributes_type_idx')
                ->comment('Тип: string, text, integer, decimal, boolean, date, datetime, select, multiselect');

            $table->string('unit', 50)
                ->nullable()
                ->index('market_attributes_unit_idx')
                ->comment('Единица измерения: mm, cm, m, g, kg, V, W, kW, Hz, %, шт и т.п.');

            /**
             * Обязательность
             */
            $table->boolean('required')
                ->default(false)
                ->index('market_attributes_required_idx')
                ->comment('Обязательное поле');

            /**
             * Возможность фильтрации
             */
            $table->boolean('filterable')
                ->default(false)
                ->index('market_attributes_filterable_idx')
                ->comment('Используется в фильтрах');

            /**
             * Показывать в карточке товара
             */
            $table->boolean('visible')
                ->default(true)
                ->index('market_attributes_visible_idx')
                ->comment('Показывать характеристику в публичной части');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_attributes_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_attributes_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_attributes_status_idx')
                ->comment('Статус: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_attributes_moderation_status_idx')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_attributes_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign(
                'moderated_by',
                'market_attributes_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_attributes_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Публикация
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_attributes_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_attributes_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_attributes_show_to_at_idx')
                ->comment('Окончание показа');

            $table->timestamps();

            /**
             * Индексы
             */
            $table->index(
                ['market_attribute_group_id', 'activity', 'sort'],
                'market_attributes_group_activity_sort_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_attributes_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_attributes_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: характеристики товаров и других сущностей.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attributes');
    }
};
