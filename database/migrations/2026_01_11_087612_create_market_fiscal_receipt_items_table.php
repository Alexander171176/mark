<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_fiscal_receipt_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции фискального чека');

            /* =========================================================
             * PARENT RECEIPT
             * ========================================================= */
            $table->foreignId('fiscal_receipt_id')
                ->comment('Фискальный чек (market_fiscal_receipts.id)')
                ->constrained('market_fiscal_receipts')
                ->cascadeOnDelete();

            /**
             * ✅ КРИТИЧНО:
             * Для tenant-safe FK из других таблиц (если понадобится) удобно иметь уникальный ключ:
             * (id, fiscal_receipt_id) — не обязателен.
             *
             * Но ВАЖНО другое: child-таблица должна быть tenant-safe сама по себе,
             * иначе можно “скрестить” строки чека разных витрин.
             * Поэтому дублируем company_id/storefront_id из родителя.
             */

            /* =========================================================
             * TENANT-SAFE (дублируем из market_fiscal_receipts)
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания (market_companies.id), дублируется из чека')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id), дублируется из чека');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfri_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * tenant-safe связь на чек:
             * Требование (в market_fiscal_receipts): индекс/unique на (company_id, storefront_id, id)
             * Если его нет — добавь (unique или index) в market_fiscal_receipts.
             */
            $table->foreign(['company_id', 'storefront_id', 'fiscal_receipt_id'], 'fk_mfri_receipt_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_fiscal_receipts')
                ->cascadeOnDelete();

            /* =========================================================
             * OPTIONAL LINKS (tenant-safe)
             * Важно: nullable + композит => нельзя nullOnDelete (из-за company/storefront NOT NULL)
             * Поэтому RESTRICT.
             *
             * Требования к родителям:
             * - market_order_items: индекс/unique на (company_id, storefront_id, id)
             * - market_products: индекс/unique на (storefront_id, id) (у тебя уже так)
             * - market_product_variants: индекс/unique на (storefront_id, id)
             * ========================================================= */
            $table->unsignedBigInteger('order_item_id')->nullable()
                ->comment('Строка заказа (market_order_items.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'order_item_id'], 'fk_mfri_order_item_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_items')
                ->restrictOnDelete();

            $table->unsignedBigInteger('product_id')->nullable()
                ->comment('Товар (market_products.id), опционально');

            $table->foreign(['storefront_id', 'product_id'], 'fk_mfri_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->restrictOnDelete();

            $table->unsignedBigInteger('product_variant_id')->nullable()
                ->comment('Вариант товара (market_product_variants.id), опционально');

            $table->foreign(['storefront_id', 'product_variant_id'], 'fk_mfri_storefront_variant')
                ->references(['storefront_id', 'id'])
                ->on('market_product_variants')
                ->restrictOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */
            $table->boolean('activity')->default(true)->comment('Активность позиции');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * SNAPSHOT (соответствие чеку)
             * ========================================================= */
            $table->string('title', 255)->comment('Название позиции (snapshot)');
            $table->string('sku', 191)->nullable()->comment('SKU (snapshot)');
            $table->string('unit', 30)->nullable()->comment('Ед. измерения (snapshot)');

            /* =========================================================
             * QTY / PRICES / TAX
             * ========================================================= */
            $table->decimal('quantity', 18, 6)->default(1)->comment('Количество');
            $table->decimal('price', 18, 2)->default(0)->comment('Цена за ед.');
            $table->decimal('discount', 18, 2)->default(0)->comment('Скидка по позиции');

            $table->decimal('tax_rate', 6, 3)->nullable()->comment('Ставка налога/НДС');
            $table->decimal('tax_amount', 18, 2)->default(0)->comment('Налог/НДС по позиции');

            $table->decimal('total', 18, 2)->default(0)->comment('Итого по позиции');

            /* =========================================================
             * META
             * ========================================================= */
            $table->json('meta')->nullable()->comment('Доп. данные (маркировка, коды, параметры провайдера)');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */
            $table->index(['company_id', 'storefront_id'], 'ix_mfri_tenant');
            $table->index(['fiscal_receipt_id', 'activity', 'sort'], 'ix_mfri_list');
            $table->index(['company_id', 'storefront_id', 'fiscal_receipt_id'], 'ix_mfri_receipt_tenant');

            $table->index(['order_item_id'], 'ix_mfri_order_item');
            $table->index(['product_id'], 'ix_mfri_product');
            $table->index(['product_variant_id'], 'ix_mfri_variant');

            $table->comment('Маркет: позиции фискального чека (snapshot данных), tenant-safe, для sale/refund');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_fiscal_receipt_items');
    }
};
