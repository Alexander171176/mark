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
        Schema::create('market_product_variants', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Родительский товар.
             *
             * Один товар может содержать несколько вариантов.
             * Каждый вариант принадлежит только одному товару.
             */
            $table->unsignedBigInteger('market_product_id')
                ->index('market_product_variants_product_id_idx')
                ->comment('Родительский товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_variants_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Валюта варианта.
             *
             * Если валюта не указана, можно использовать валюту
             * родительского товара.
             */
            $table->unsignedBigInteger('currency_id')
                ->nullable()
                ->index('market_product_variants_currency_id_idx')
                ->comment('Валюта варианта (currencies.id)');

            $table->foreign(
                'currency_id',
                'market_product_variants_currency_id_fk'
            )
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            /**
             * Стабильный код комбинации варианта.
             *
             * Примеры:
             * - black-xl;
             * - 16gb-512gb;
             * - red-500ml.
             */
            $table->string('code', 255)
                ->nullable()
                ->index('market_product_variants_code_idx')
                ->comment('Стабильный код комбинации варианта');

            /**
             * Торговые идентификаторы.
             */
            $table->string('sku', 100)
                ->nullable()
                ->index('market_product_variants_sku_idx')
                ->comment('Внутренний артикул варианта');

            $table->string('vendor_code', 100)
                ->nullable()
                ->index('market_product_variants_vendor_code_idx')
                ->comment('Артикул варианта у производителя или поставщика');

            $table->string('barcode', 100)
                ->nullable()
                ->index('market_product_variants_barcode_idx')
                ->comment('Штрихкод варианта товара');

            /**
             * Цены.
             *
             * Nullable-поля позволяют наследовать цену
             * родительского товара.
             */
            $table->decimal('price', 15, 2)
                ->nullable()
                ->comment('Цена варианта');

            $table->decimal('old_price', 15, 2)
                ->nullable()
                ->comment('Старая цена варианта');

            $table->decimal('purchase_price', 15, 2)
                ->nullable()
                ->comment('Закупочная цена варианта');

            $table->decimal('wholesale_price', 15, 2)
                ->nullable()
                ->comment('Оптовая цена варианта');

            $table->unsignedInteger('wholesale_min_quantity')
                ->nullable()
                ->comment('Минимальное количество для оптовой цены');

            /**
             * Остаток.
             */
            $table->unsignedInteger('quantity')
                ->default(0)
                ->index('market_product_variants_quantity_idx')
                ->comment('Количество варианта на складе');

            $table->boolean('in_stock')
                ->default(false)
                ->index('market_product_variants_in_stock_idx')
                ->comment('Вариант находится в наличии');

            /**
             * Физические параметры.
             *
             * Если значения не указаны, используются параметры товара.
             */
            $table->decimal('weight', 10, 3)
                ->nullable()
                ->comment('Вес варианта в килограммах');

            $table->decimal('length', 10, 2)
                ->nullable()
                ->comment('Длина варианта в сантиметрах');

            $table->decimal('width', 10, 2)
                ->nullable()
                ->comment('Ширина варианта в сантиметрах');

            $table->decimal('height', 10, 2)
                ->nullable()
                ->comment('Высота варианта в сантиметрах');

            /**
             * Основной вариант товара.
             *
             * Проверку наличия только одного основного варианта
             * для товара будем выполнять в Request и Controller.
             */
            $table->boolean('is_default')
                ->default(false)
                ->index('market_product_variants_default_idx')
                ->comment('Основной вариант товара');

            /**
             * Управление отображением.
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_product_variants_sort_idx')
                ->comment('Порядок сортировки варианта');

            $table->boolean('activity')
                ->default(false)
                ->index('market_product_variants_activity_idx')
                ->comment('Активность варианта');

            /**
             * Статус публикации.
             *
             * Поддерживаемые значения:
             * - draft;
             * - published;
             * - archived.
             */
            $table->string('status', 50)
                ->default('draft')
                ->index('market_product_variants_status_idx')
                ->comment('Статус варианта: draft, published, archived');

            /**
             * Модерация.
             *
             * 0 — ожидает модерации;
             * 1 — одобрен;
             * 2 — отклонён.
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_product_variants_moderation_status_idx')
                ->comment('Статус модерации: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_product_variants_moderated_by_idx')
                ->comment('Модератор варианта (users.id)');

            $table->foreign(
                'moderated_by',
                'market_product_variants_moderated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_product_variants_moderated_at_idx')
                ->comment('Дата и время модерации варианта');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий или причина решения модератора');

            /**
             * Период публикации.
             */
            $table->timestamp('published_at')
                ->nullable()
                ->index('market_product_variants_published_at_idx')
                ->comment('Дата и время публикации варианта');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_product_variants_show_from_at_idx')
                ->comment('Дата и время начала показа варианта');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_product_variants_show_to_at_idx')
                ->comment('Дата и время окончания показа варианта');

            $table->timestamps();

            /**
             * Уникальные значения внутри одного товара.
             *
             * Одинаковые SKU или code могут теоретически существовать
             * у разных товаров, но не внутри одного родительского товара.
             */
            $table->unique(
                ['market_product_id', 'code'],
                'market_product_variants_product_code_unique'
            );

            $table->unique(
                ['market_product_id', 'sku'],
                'market_product_variants_product_sku_unique'
            );

            /**
             * Быстрые административные и публичные выборки.
             */
            $table->index(
                [
                    'market_product_id',
                    'activity',
                    'sort',
                ],
                'market_product_variants_product_activity_sort_idx'
            );

            $table->index(
                [
                    'market_product_id',
                    'status',
                    'moderation_status',
                ],
                'market_product_variants_product_status_idx'
            );

            $table->index(
                [
                    'market_product_id',
                    'in_stock',
                    'activity',
                ],
                'market_product_variants_product_stock_idx'
            );

            $table->index(
                [
                    'activity',
                    'show_from_at',
                    'show_to_at',
                ],
                'market_product_variants_show_window_idx'
            );

            $table->comment(
                'Маркетплейс: варианты товаров с собственными ценами, остатками, изображениями и характеристиками.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_variants');
    }
};
