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
        Schema::create('market_products', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Владелец / создатель
             */
            $table->unsignedBigInteger('user_id')
                ->index('market_products_user_id_idx')
                ->comment('Создатель/владелец товара (users.id)');

            $table->foreign('user_id', 'market_products_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * Компания / магазин / бренд
             */
            $table->unsignedBigInteger('market_company_id')
                ->nullable()
                ->index('market_products_company_id_idx')
                ->comment('Компания-поставщик (market_companies.id)');

            $table->foreign('market_company_id', 'market_products_company_id_fk')
                ->references('id')
                ->on('market_companies')
                ->nullOnDelete();

            $table->unsignedBigInteger('market_shop_id')
                ->nullable()
                ->index('market_products_shop_id_idx')
                ->comment('Магазин товара (market_shops.id)');

            $table->foreign('market_shop_id', 'market_products_shop_id_fk')
                ->references('id')
                ->on('market_shops')
                ->nullOnDelete();

            $table->unsignedBigInteger('market_brand_id')
                ->nullable()
                ->index('market_products_brand_id_idx')
                ->comment('Бренд товара (market_brands.id)');

            $table->foreign('market_brand_id', 'market_products_brand_id_fk')
                ->references('id')
                ->on('market_brands')
                ->nullOnDelete();

            /**
             * Основные данные
             */
            $table->string('url', 500)
                ->comment('Уникальный URL/slug товара');

            $table->string('sku', 100)
                ->nullable()
                ->index('market_products_sku_idx')
                ->comment('Внутренний артикул товара');

            $table->string('vendor_code', 100)
                ->nullable()
                ->index('market_products_vendor_code_idx')
                ->comment('Артикул производителя/поставщика');

            $table->string('barcode', 100)
                ->nullable()
                ->index('market_products_barcode_idx')
                ->comment('Штрихкод товара');

            /**
             * Цена / остатки
             */
            $table->unsignedBigInteger('currency_id')
                ->nullable()
                ->index('market_products_currency_id_idx')
                ->comment('Валюта товара (currencies.id)');

            $table->foreign('currency_id', 'market_products_currency_id_fk')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            $table->decimal('price', 15, 2)
                ->default(0)
                ->index('market_products_price_idx')
                ->comment('Текущая цена товара');

            $table->decimal('old_price', 15, 2)
                ->nullable()
                ->index('market_products_old_price_idx')
                ->comment('Старая цена товара');

            $table->decimal('purchase_price', 15, 2)
                ->nullable()
                ->comment('Закупочная цена товара');

            $table->decimal('wholesale_price', 15, 2)
                ->nullable()
                ->index('market_products_wholesale_price_idx')
                ->comment('Оптовая цена товара');

            $table->unsignedInteger('wholesale_min_quantity')
                ->nullable()
                ->index('market_products_wholesale_min_quantity_idx')
                ->comment('Минимальное количество для оптовой цены');

            $table->unsignedInteger('quantity')
                ->default(0)
                ->index('market_products_quantity_idx')
                ->comment('Количество товара на складе');

            $table->boolean('in_stock')
                ->default(false)
                ->index('market_products_in_stock_idx')
                ->comment('Есть в наличии');

            /**
             * Физические параметры
             */
            $table->decimal('weight', 10, 3)
                ->nullable()
                ->comment('Вес товара');

            $table->decimal('length', 10, 2)
                ->nullable()
                ->comment('Длина товара');

            $table->decimal('width', 10, 2)
                ->nullable()
                ->comment('Ширина товара');

            $table->decimal('height', 10, 2)
                ->nullable()
                ->comment('Высота товара');

            /**
             * Отображение
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_products_sort_idx')
                ->comment('Сортировка');

            $table->boolean('activity')
                ->default(false)
                ->index('market_products_activity_idx')
                ->comment('Активность');

            $table->boolean('left')
                ->default(false)
                ->index('market_products_left_idx')
                ->comment('Показывать товар в левой рекламной зоне');

            $table->boolean('main')
                ->default(false)
                ->index('market_products_main_idx')
                ->comment('Показывать товар в главной рекламной зоне');

            $table->boolean('right')
                ->default(false)
                ->index('market_products_right_idx')
                ->comment('Показывать товар в правой рекламной зоне');

            $table->boolean('is_new')
                ->default(false)
                ->index('market_products_is_new_idx')
                ->comment('Показывать товар в новинках');

            $table->boolean('is_hit')
                ->default(false)
                ->index('market_products_is_hit_idx')
                ->comment('Показывать товар в хитах продаж');

            $table->boolean('is_sale')
                ->default(false)
                ->index('market_products_is_sale_idx')
                ->comment('Показывать товар в распродаже');

            /**
             * Публикация
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_products_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            /**
             * Модерация
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_products_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_products_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'market_products_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_products_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            /**
             * Окно публикации
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_products_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_products_show_from_at_idx')
                ->comment('Начало показа');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_products_show_to_at_idx')
                ->comment('Окончание показа');

            /**
             * Счётчики / агрегаты
             */
            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_products_views_idx')
                ->comment('Количество просмотров');

            $table->unsignedInteger('likes_count')
                ->default(0)
                ->index('market_products_likes_count_idx')
                ->comment('Количество лайков');

            $table->decimal('rating_avg', 3, 2)
                ->default(0)
                ->index('market_products_rating_avg_idx')
                ->comment('Средний рейтинг товара');

            $table->unsignedInteger('rating_count')
                ->default(0)
                ->index('market_products_rating_count_idx')
                ->comment('Количество оценок/отзывов');

            $table->timestamps();

            /**
             * Уникальность
             */
            $table->unique(
                ['user_id', 'url'],
                'market_products_user_url_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_company_id', 'activity', 'sort'],
                'market_products_company_activity_sort_idx'
            );

            $table->index(
                ['market_shop_id', 'activity', 'sort'],
                'market_products_shop_activity_sort_idx'
            );

            $table->index(
                ['market_brand_id', 'activity', 'sort'],
                'market_products_brand_activity_sort_idx'
            );

            $table->index(
                ['activity', 'is_new', 'sort'],
                'market_products_new_idx'
            );

            $table->index(
                ['activity', 'is_hit', 'sort'],
                'market_products_hit_idx'
            );

            $table->index(
                ['activity', 'is_sale', 'sort'],
                'market_products_sale_idx'
            );

            $table->index(
                ['activity', 'status', 'moderation_status'],
                'market_products_public_status_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_products_show_window_idx'
            );

            $table->index(
                ['activity', 'in_stock', 'price'],
                'market_products_stock_price_idx'
            );

            $table->index(
                ['left', 'main', 'right'],
                'market_products_placement_idx'
            );

            $table->comment(
                'Маркетплейс: товары. Основная таблица без переводов. Связи с категориями, тегами, изображениями, атрибутами, отзывами и похожими товарами вынесены отдельно.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_products');
    }
};
