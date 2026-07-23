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
             * Группа характеристик.
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
             * Создатель.
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
             * Основные данные.
             */
            $table->string('code', 100)
                ->unique('market_attributes_code_unique')
                ->comment('Уникальный системный код характеристики, не переводится');

            $table->text('icon')
                ->nullable()
                ->comment('SVG, HTML либо путь к иконке');

            $table->string('color', 50)
                ->nullable()
                ->comment('Цвет характеристики');

            /**
             * Тип значения.
             */
            $table->string('type', 50)
                ->default('string')
                ->index('market_attributes_type_idx')
                ->comment(
                    'Тип значения: string, text, integer, decimal, boolean, date, datetime, select, multiselect'
                );

            $table->string('unit', 50)
                ->nullable()
                ->index('market_attributes_unit_idx')
                ->comment(
                    'Единица измерения: mm, cm, m, g, kg, V, W, kW, Hz, %, шт и т.п.'
                );

            /**
             * Обязательность.
             */
            $table->boolean('required')
                ->default(false)
                ->index('market_attributes_required_idx')
                ->comment('Обязательное поле');

            /**
             * Возможность фильтрации.
             */
            $table->boolean('filterable')
                ->default(false)
                ->index('market_attributes_filterable_idx')
                ->comment('Используется в фильтрах');

            /**
             * Использование характеристики
             * при формировании вариантов товара.
             *
             * Примеры:
             * - цвет;
             * - размер;
             * - объём;
             * - память.
             *
             * Не рекомендуется использовать:
             * - производитель;
             * - гарантия;
             * - страна производства.
             */
            $table->boolean('use_for_variants')
                ->default(false)
                ->index('market_attributes_use_for_variants_idx')
                ->comment(
                    'Использовать характеристику при создании вариантов товара'
                );

            /**
             * Показывать в карточке товара.
             */
            $table->boolean('visible')
                ->default(true)
                ->index('market_attributes_visible_idx')
                ->comment('Показывать характеристику в публичной части');

            /**
             * Отображение.
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_attributes_sort_idx')
                ->comment('Порядок сортировки');

            $table->boolean('activity')
                ->default(false)
                ->index('market_attributes_activity_idx')
                ->comment('Активность характеристики');

            /**
             * Публикация.
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_attributes_status_idx')
                ->comment(
                    'Статус характеристики: draft, published, archived'
                );

            /**
             * Модерация.
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_attributes_moderation_status_idx')
                ->comment(
                    'Статус модерации: 0=pending, 1=approved, 2=rejected'
                );

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_attributes_moderated_by_idx')
                ->comment('Модератор характеристики (users.id)');

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
                ->comment('Дата и время модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий или причина решения модератора');

            /**
             * Период публикации.
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_attributes_published_at_idx')
                ->comment('Дата и время публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_attributes_show_from_at_idx')
                ->comment('Дата и время начала показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_attributes_show_to_at_idx')
                ->comment('Дата и время окончания показа');

            $table->timestamps();

            /**
             * Быстрые выборки характеристик группы.
             */
            $table->index(
                [
                    'market_attribute_group_id',
                    'activity',
                    'sort',
                ],
                'market_attributes_group_activity_sort_idx'
            );

            /**
             * Публичные характеристики.
             */
            $table->index(
                [
                    'activity',
                    'status',
                    'moderation_status',
                ],
                'market_attributes_public_status_idx'
            );

            /**
             * Окно показа.
             */
            $table->index(
                [
                    'activity',
                    'show_from_at',
                    'show_to_at',
                ],
                'market_attributes_show_window_idx'
            );

            /**
             * Характеристики для формирования вариантов.
             */
            $table->index(
                [
                    'use_for_variants',
                    'activity',
                    'status',
                    'moderation_status',
                ],
                'market_attributes_variants_public_idx'
            );

            $table->comment(
                'Маркетплейс: характеристики товаров, фильтров и вариантов товаров.'
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
