<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_shops', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Компания-владелец
             * Одна компания → один магазин
             */
            $table->unsignedBigInteger('market_company_id')
                ->unique('market_shops_company_id_unique')
                ->comment('Компания-владелец магазина (market_companies.id). Одна компания = один магазин');

            $table->foreign('market_company_id', 'market_shops_company_id_fk')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            /**
             * Создатель магазина
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_shops_user_id_idx')
                ->comment('Создатель магазина (users.id)');

            $table->foreign('user_id', 'market_shops_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->unique('market_shops_url_unique')
                ->comment('Уникальный URL магазина');

            $table->string('email')
                ->nullable()
                ->comment('Email магазина');

            $table->string('phone', 100)
                ->nullable()
                ->comment('Телефон магазина');

            $table->string('logo', 500)
                ->nullable()
                ->comment('Логотип магазина');

            /**
             * Социальные сети
             */
            $table->json('social_links')
                ->nullable()
                ->comment('Социальные сети магазина');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_shops_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_shops_activity_idx')
                ->comment('Активность');

            $table->boolean('left')
                ->default(false)
                ->comment('Левая колонка');

            $table->boolean('main')
                ->default(false)
                ->comment('Главная зона');

            $table->boolean('right')
                ->default(false)
                ->comment('Правая колонка');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_shops_status_idx')
                ->comment('draft|published|archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_shops_moderation_status_idx')
                ->comment('0-pending 1-approved 2-rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'market_shops_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Публикация и окно показа
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_shops_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_shops_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_shops_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->comment('Количество просмотров');

            $table->timestamps();

            $table->comment('Маркетплейс: магазин компании. Одна компания может иметь только один магазин.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_shops');
    }
};
