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
        Schema::create('market_product_bundle_items', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Комплект товаров.
             */
            $table->unsignedBigInteger('market_product_bundle_id')
                ->index('market_product_bundle_items_bundle_id_idx')
                ->comment('Комплект товаров (market_product_bundles.id)');

            $table->foreign(
                'market_product_bundle_id',
                'market_product_bundle_items_bundle_id_fk'
            )
                ->references('id')
                ->on('market_product_bundles')
                ->cascadeOnDelete();

            /**
             * Товар, входящий в комплект.
             *
             * Товар указывается всегда, даже если дополнительно
             * выбран его конкретный вариант.
             */
            $table->unsignedBigInteger('market_product_id')
                ->index('market_product_bundle_items_product_id_idx')
                ->comment('Товар комплекта (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_bundle_items_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Конкретный вариант товара.
             *
             * Если значение отсутствует, в комплект входит
             * базовый товар без фиксации конкретного варианта.
             */
            $table->unsignedBigInteger('market_product_variant_id')
                ->nullable()
                ->index('market_product_bundle_items_variant_id_idx')
                ->comment('Вариант товара комплекта (market_product_variants.id)');

            $table->foreign(
                'market_product_variant_id',
                'market_product_bundle_items_variant_id_fk'
            )
                ->references('id')
                ->on('market_product_variants')
                ->cascadeOnDelete();

            /**
             * Количество товара в одном комплекте.
             *
             * Примеры:
             * - 1 ноутбук;
             * - 2 аккумулятора;
             * - 3 лампы.
             */
            $table->unsignedInteger('quantity')
                ->default(1)
                ->index('market_product_bundle_items_quantity_idx')
                ->comment('Количество товара или варианта в одном комплекте');

            /**
             * Цена одной единицы внутри комплекта.
             *
             * null — использовать актуальную эффективную цену
             * товара или выбранного варианта.
             *
             * Значение можно использовать для ручной фиксации
             * стоимости позиции внутри комплекта.
             */
            $table->decimal('unit_price', 15, 2)
                ->nullable()
                ->index('market_product_bundle_items_unit_price_idx')
                ->comment('Цена одной единицы внутри комплекта');

            /**
             * Настройки скидки позиции.
             *
             * Допустимые значения discount_type:
             * - fixed — фиксированная скидка;
             * - percent — процентная скидка.
             *
             * null — отдельная скидка позиции не используется.
             */
            $table->string('discount_type', 50)
                ->nullable()
                ->index('market_product_bundle_items_discount_type_idx')
                ->comment('Тип скидки позиции: fixed, percent');

            $table->decimal('discount_value', 15, 2)
                ->nullable()
                ->comment('Размер скидки позиции комплекта');

            /**
             * Отображение.
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_product_bundle_items_sort_idx')
                ->comment('Порядок позиции внутри комплекта');

            $table->boolean('activity')
                ->default(true)
                ->index('market_product_bundle_items_activity_idx')
                ->comment('Активность позиции комплекта');

            $table->timestamps();

            /**
             * Один и тот же конкретный вариант не должен
             * повторяться внутри одного комплекта.
             *
             * Для строк без варианта дополнительную защиту
             * от повторения товара реализуем в Request,
             * поскольку MySQL допускает несколько NULL
             * в составном уникальном индексе.
             */
            $table->unique(
                [
                    'market_product_bundle_id',
                    'market_product_id',
                    'market_product_variant_id',
                ],
                'market_product_bundle_items_bundle_product_variant_unique'
            );

            /**
             * Быстрая загрузка состава комплекта
             * в установленном порядке.
             */
            $table->index(
                [
                    'market_product_bundle_id',
                    'activity',
                    'sort',
                ],
                'market_product_bundle_items_bundle_activity_sort_idx'
            );

            /**
             * Поиск комплектов, содержащих конкретный товар.
             */
            $table->index(
                [
                    'market_product_id',
                    'activity',
                ],
                'market_product_bundle_items_product_activity_idx'
            );

            /**
             * Поиск комплектов, содержащих конкретный
             * вариант товара.
             */
            $table->index(
                [
                    'market_product_variant_id',
                    'activity',
                ],
                'market_product_bundle_items_variant_activity_idx'
            );

            $table->comment(
                'Маркетплейс: состав комплектов товаров. Каждая строка связывает комплект с товаром или его конкретным вариантом.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_bundle_items');
    }
};
