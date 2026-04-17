<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_product_variants', function (Blueprint $table) {
            $table->id()->comment('ID варианта товара');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mpv_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * PRODUCT (tenant-safe)
             * ========================================================= */
            $table->unsignedBigInteger('product_id')
                ->comment('Товар (market_products.id)');

            // товар обязан быть из этого же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'product_id'], 'fk_mpv_product_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */
            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность варианта');

            $table->string('sku', 191)->comment('SKU варианта (уникален в рамках товара)');
            $table->string('title', 255)->nullable()->comment('Название варианта');

            $table->unsignedInteger('quantity')->default(0)->comment('Остаток');
            $table->string('unit', 30)->nullable()->comment('Единица измерения');
            $table->unsignedInteger('weight')->default(0)->comment('Вес (граммы)');
            $table->string('availability', 255)->nullable()->comment('Наличие (текст/статус)');

            $table->decimal('price', 18, 2)->default(0)->comment('Цена');
            $table->decimal('old_price', 18, 2)->default(0)->comment('Старая цена');
            $table->decimal('discount_price', 18, 2)->nullable()->comment('Цена со скидкой');
            $table->boolean('is_manual')->default(true)->comment('Цена задана вручную');

            // Валюта — справочник, рационально запрещать удаление если есть ссылки
            $table->foreignId('currency_id')
                ->comment('Валюта (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->string('barcode', 64)->nullable()->comment('Штрихкод');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Для tenant-safe FK из других таблиц: (company_id, storefront_id, variant_id) -> (company_id, storefront_id, id)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpv_tenant_id');

            // Для FK по витрине: (storefront_id, variant_id) -> (storefront_id, id)
            $table->unique(['storefront_id', 'id'], 'uq_mpv_storefront_id');

            // SKU уникален в рамках товара
            $table->unique(['product_id', 'sku'], 'uq_mpv_product_sku');

            // Индексы
            $table->index(['company_id', 'storefront_id'], 'ix_mpv_tenant');
            $table->index(['product_id', 'activity', 'sort'], 'ix_mpv_product_list');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mpv_storefront_list');

            $table->index('availability', 'ix_mpv_availability');
            $table->index('price', 'ix_mpv_price');
            $table->index('old_price', 'ix_mpv_old_price');
            $table->index('discount_price', 'ix_mpv_discount_price');
            $table->index('barcode', 'ix_mpv_barcode');

            $table->comment('Маркет: варианты товаров (tenant-safe по company+storefront)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_product_variants');
    }
};
