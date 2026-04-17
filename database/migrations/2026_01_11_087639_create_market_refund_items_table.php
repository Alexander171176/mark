<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_refund_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции возврата');

            /* =========================================================
             * LINKS
             * ========================================================= */

            $table->foreignId('refund_id')
                ->comment('Возврат (market_refunds.id)')
                ->constrained('market_refunds')
                ->cascadeOnDelete();

            // Возврат почти всегда идёт по строке заказа
            $table->foreignId('order_item_id')
                ->comment('Строка заказа (market_order_items.id)')
                ->constrained('market_order_items')
                ->cascadeOnDelete();

            // Прямые ссылки на товар (для аналитики / отчётов)
            $table->foreignId('product_id')
                ->nullable()
                ->comment('Товар (market_products.id)')
                ->constrained('market_products')
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id)')
                ->constrained('market_product_variants')
                ->nullOnDelete();

            /* =========================================================
             * FLAGS
             * ========================================================= */

            $table->boolean('activity')
                ->default(true)
                ->comment('Активность позиции возврата');

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка');

            /* =========================================================
             * SNAPSHOT (на момент возврата)
             * ========================================================= */

            $table->string('title', 255)
                ->comment('Название позиции (snapshot)');

            $table->string('sku', 191)
                ->nullable()
                ->comment('SKU (snapshot)');

            $table->string('unit', 30)
                ->nullable()
                ->comment('Единица измерения (snapshot)');

            /* =========================================================
             * QUANTITY / AMOUNTS
             * ========================================================= */

            $table->decimal('quantity', 18, 6)
                ->default(1)
                ->comment('Количество к возврату');

            $table->decimal('price', 18, 2)
                ->default(0)
                ->comment('Цена за единицу (snapshot)');

            $table->decimal('discount', 18, 2)
                ->default(0)
                ->comment('Скидка по позиции (snapshot)');

            $table->decimal('tax_amount', 18, 2)
                ->default(0)
                ->comment('Налог/НДС по позиции');

            $table->decimal('total', 18, 2)
                ->default(0)
                ->comment('Итого к возврату по позиции');

            /* =========================================================
             * REASON / CONDITION
             * ========================================================= */

            $table->string('reason_code', 64)
                ->nullable()
                ->comment('Причина: defect/size_mismatch/not_like/...');

            $table->string('reason_text', 255)
                ->nullable()
                ->comment('Комментарий причины');

            $table->string('item_condition', 32)
                ->nullable()
                ->comment('Состояние товара: new/opened/used/damaged');

            /* =========================================================
             * META
             * ========================================================= */

            $table->json('meta')
                ->nullable()
                ->comment('Доп. данные (фото дефекта, признаки, служебные флаги)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            // список позиций возврата
            $table->index(
                ['refund_id', 'activity', 'sort'],
                'ix_mri_refund_list'
            );

            // аналитика по строкам заказа
            $table->index(
                ['order_item_id'],
                'ix_mri_order_item'
            );

            // аналитика по вариантам
            $table->index(
                ['product_variant_id'],
                'ix_mri_product_variant'
            );

            $table->comment(
                'Маркет: позиции возврата (refund items) — возвраты по строкам заказа, OZON-подход'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_refund_items');
    }
};
