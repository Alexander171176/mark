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
        Schema::create('market_attribute_values', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Характеристика
             */
            $table->unsignedBigInteger('market_attribute_id')
                ->index('market_attribute_values_attribute_id_idx')
                ->comment('Характеристика (market_attributes.id)');

            $table->foreign(
                'market_attribute_id',
                'market_attribute_values_attribute_id_fk'
            )
                ->references('id')
                ->on('market_attributes')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('code', 100)
                ->comment('Системный код значения характеристики');

            $table->text('icon')
                ->nullable()
                ->comment('SVG, HTML либо путь к иконке');

            $table->string('color', 50)
                ->nullable()
                ->comment('Цвет значения');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_attribute_values_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_attribute_values_activity_idx')
                ->comment('Активность');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_attribute_values_status_idx')
                ->comment('Статус: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_attribute_values_moderation_status_idx')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_attribute_values_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign(
                'moderated_by',
                'market_attribute_values_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_attribute_values_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_attribute_values_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_attribute_values_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_attribute_values_show_to_at_idx')
                ->comment('Окончание показа');

            $table->timestamps();

            /**
             * Уникальность внутри характеристики
             */
            $table->unique(
                ['market_attribute_id', 'code'],
                'market_attribute_values_attribute_code_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_attribute_id', 'activity', 'sort'],
                'market_attribute_values_attribute_activity_sort_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_attribute_values_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_attribute_values_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: значения характеристик для select и multiselect.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_attribute_values');
    }
};
