<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_invoice_items', function (Blueprint $table) {
            $table->id()->comment('ID строки инвойса');

            /* =========================================================
             * TENANT (дублируем из invoice)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id), дублируется из инвойса')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id), дублируется из инвойса');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mii_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * INVOICE (tenant-safe)
             * Требование: в market_invoices должен быть UNIQUE/INDEX (id, company_id, storefront_id)
             * ========================================================= */

            $table->unsignedBigInteger('invoice_id')
                ->comment('Инвойс (market_invoices.id)');

            $table->foreign(['company_id', 'storefront_id', 'invoice_id'], 'fk_mii_invoice_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_invoices')
                ->cascadeOnDelete();

            /* =========================================================
             * OPTIONAL LINK: ORDER ITEM (tenant-safe)
             * Требование: в market_order_items нужен UNIQUE/INDEX (storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_item_id')
                ->nullable()
                ->comment('Строка заказа (market_order_items.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'order_item_id'], 'fk_mii_order_item_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_items')
                ->restrictOnDelete();

            /* =========================================================
             * PRODUCT / VARIANT (optional, tenant-safe)
             * ВАЖНО: нельзя nullOnDelete() на композитном FK, т.к. storefront_id NOT NULL
             * ========================================================= */

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'product_id'], 'fk_mii_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->restrictOnDelete();

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id), опционально');

            $table->foreign(['company_id', 'storefront_id', 'product_variant_id'], 'fk_mii_variant_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_product_variants')
                ->restrictOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность строки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            // Snapshot на момент выставления
            $table->string('title', 255)->comment('Название позиции (snapshot)');
            $table->string('sku', 191)->nullable()->comment('SKU (snapshot)');
            $table->string('unit', 30)->nullable()->comment('Единица измерения (snapshot)');

            $table->decimal('quantity', 18, 6)->default(1)->comment('Количество');
            $table->decimal('price', 18, 2)->default(0)->comment('Цена за единицу');
            $table->decimal('discount', 18, 2)->default(0)->comment('Скидка по строке');

            $table->decimal('tax_rate', 6, 3)->nullable()->comment('Ставка налога/НДС (например 12.000)');
            $table->decimal('tax_amount', 18, 2)->default(0)->comment('Сумма налога/НДС по строке');

            $table->decimal('subtotal', 18, 2)->default(0)->comment('Сумма строки без налога');
            $table->decimal('total', 18, 2)->default(0)->comment('Итого по строке');

            $table->json('meta')->nullable()->comment('Доп. данные строки (атрибуты, коды, маркировки)');
            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mii_tenant');
            $table->index(['invoice_id', 'activity', 'sort'], 'ix_market_invoice_items_list');
            $table->index(['order_item_id'], 'ix_market_invoice_items_order_item');
            $table->index(['product_id'], 'ix_mii_product');
            $table->index(['product_variant_id'], 'ix_market_invoice_items_variant');
            $table->index(['storefront_id', 'product_id'], 'ix_mii_sf_product');

            $table->comment('Маркет: строки инвойса (позиции счёта), tenant-safe, snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_invoice_items');
    }
};
