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
        Schema::create('market_product_bundles', function (Blueprint $table) {

            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_product_bundles_user_id_idx')
                ->comment('Создатель/владелец комплекта (users.id)');

            $table->foreign(
                'user_id',
                'market_product_bundles_user_id_fk'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Компания / магазин
             */
            $table->unsignedBigInteger('market_company_id')
                ->nullable()
                ->index('market_product_bundles_company_id_idx')
                ->comment('Компания-поставщик (market_companies.id)');

            $table->foreign(
                'market_company_id',
                'market_product_bundles_company_id_fk'
            )
                ->references('id')
                ->on('market_companies')
                ->nullOnDelete();

            $table->unsignedBigInteger('market_shop_id')
                ->nullable()
                ->index('market_product_bundles_shop_id_idx')
                ->comment('Магазин комплекта (market_shops.id)');

            $table->foreign(
                'market_shop_id',
                'market_product_bundles_shop_id_fk'
            )
                ->references('id')
                ->on('market_shops')
                ->nullOnDelete();

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->comment('Уникальный URL/slug комплекта');

            $table->string('sku', 100)
                ->nullable()
                ->index('market_product_bundles_sku_idx')
                ->comment('Внутренний артикул комплекта');

            $table->string('vendor_code', 100)
                ->nullable()
                ->index('market_product_bundles_vendor_code_idx')
                ->comment('Артикул производителя/поставщика');

            $table->string('barcode', 100)
                ->nullable()
                ->index('market_product_bundles_barcode_idx')
                ->comment('Штрихкод комплекта');

            /**
             * Цена
             */
            $table->unsignedBigInteger('currency_id')
                ->nullable()
                ->index('market_product_bundles_currency_id_idx')
                ->comment('Валюта комплекта (currencies.id)');

            $table->foreign(
                'currency_id',
                'market_product_bundles_currency_id_fk'
            )
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            $table->boolean('calculate_price')
                ->default(true)
                ->index('market_product_bundles_calculate_price_idx')
                ->comment('Автоматически рассчитывать цену комплекта');

            $table->decimal('price', 15, 2)
                ->default(0)
                ->index('market_product_bundles_price_idx')
                ->comment('Цена комплекта');

            $table->decimal('old_price', 15, 2)
                ->nullable()
                ->index('market_product_bundles_old_price_idx')
                ->comment('Старая цена комплекта');

            $table->decimal('purchase_price', 15, 2)
                ->nullable()
                ->comment('Закупочная стоимость комплекта');

            $table->decimal('wholesale_price', 15, 2)
                ->nullable()
                ->index('market_product_bundles_wholesale_price_idx')
                ->comment('Оптовая цена комплекта');

            $table->unsignedInteger('wholesale_min_quantity')
                ->nullable()
                ->index('market_product_bundles_wholesale_min_quantity_idx')
                ->comment('Минимальное количество для оптовой цены');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_product_bundles_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_product_bundles_activity_idx')
                ->comment('Активность');

            $table->boolean('left')
                ->default(false)
                ->index('market_product_bundles_left_idx')
                ->comment('Показывать в левой рекламной зоне');

            $table->boolean('main')
                ->default(false)
                ->index('market_product_bundles_main_idx')
                ->comment('Показывать в главной рекламной зоне');

            $table->boolean('right')
                ->default(false)
                ->index('market_product_bundles_right_idx')
                ->comment('Показывать в правой рекламной зоне');

            $table->boolean('is_new')
                ->default(false)
                ->index('market_product_bundles_is_new_idx')
                ->comment('Показывать в новинках');

            $table->boolean('is_hit')
                ->default(false)
                ->index('market_product_bundles_is_hit_idx')
                ->comment('Показывать в хитах');

            $table->boolean('is_sale')
                ->default(false)
                ->index('market_product_bundles_is_sale_idx')
                ->comment('Показывать в распродаже');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_product_bundles_status_idx')
                ->comment('Статус: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_product_bundles_moderation_status_idx')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_product_bundles_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign(
                'moderated_by',
                'market_product_bundles_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_product_bundles_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Период публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_product_bundles_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_product_bundles_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_product_bundles_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Статистика
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_product_bundles_views_idx')
                ->comment('Количество просмотров');

            $table->unsignedInteger('likes_count')
                ->default(0)
                ->index('market_product_bundles_likes_count_idx')
                ->comment('Количество лайков');

            $table->decimal('rating_avg', 3, 2)
                ->default(0)
                ->index('market_product_bundles_rating_avg_idx')
                ->comment('Средний рейтинг');

            $table->unsignedInteger('rating_count')
                ->default(0)
                ->index('market_product_bundles_rating_count_idx')
                ->comment('Количество оценок');

            $table->timestamps();

            /**
             * Уникальность
             */
            $table->unique(
                ['user_id', 'url'],
                'market_product_bundles_user_url_unique'
            );

            /**
             * Индексы
             */
            $table->index(
                ['market_company_id', 'activity', 'sort'],
                'market_product_bundles_company_activity_sort_idx'
            );

            $table->index(
                ['market_shop_id', 'activity', 'sort'],
                'market_product_bundles_shop_activity_sort_idx'
            );

            $table->index(
                ['activity', 'is_new', 'sort'],
                'market_product_bundles_new_idx'
            );

            $table->index(
                ['activity', 'is_hit', 'sort'],
                'market_product_bundles_hit_idx'
            );

            $table->index(
                ['activity', 'is_sale', 'sort'],
                'market_product_bundles_sale_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_product_bundles_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_product_bundles_show_window_idx'
            );

            $table->index(
                ['left', 'main', 'right'],
                'market_product_bundles_placement_idx'
            );

            $table->comment(
                'Маркетплейс: комплекты товаров. Основная таблица без переводов. Состав комплекта хранится отдельно.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_bundles');
    }
};
