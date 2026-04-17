<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_cart_event_items', function (Blueprint $table) {
            $table->id()->comment('ID снимка позиции события');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * EVENT (tenant-safe)
             * =========================================================
             * Единый подход: ссылка на событие через композитный FK
             * (company_id, storefront_id, cart_event_id) -> market_cart_events
             *
             * Поэтому:
             * 1) НЕ используем одновременно foreignId()->constrained() на cart_event_id
             *    и композитный FK (это лишние/дублирующиеся constraints).
             * 2) Поле делаем unsignedBigInteger, FK объявляем в конце.
             */

            $table->unsignedBigInteger('cart_event_id')
                ->comment('Событие (market_cart_events.id)');

            /* =========================================================
             * OPTIONAL LINKS (snapshot may outlive entities)
             * =========================================================
             * Рационально: это "снимок", поэтому ссылки должны быть мягкими:
             * - cart_item_id: nullOnDelete ок
             * - product_id / variant_id: делаем обычные FK на id (без storefront_id),
             *   иначе невозможно nullOnDelete при storefront_id NOT NULL.
             *   Снапшот-данные (title/sku/price/meta) всё равно сохранятся.
             */

            $table->unsignedBigInteger('cart_item_id')->nullable()
                ->comment('Позиция корзины (market_cart_items.id), если есть');

            $table->unsignedBigInteger('product_id')->nullable()
                ->comment('Товар (market_products.id), если есть');

            $table->unsignedBigInteger('product_variant_id')->nullable()
                ->comment('Вариант (market_product_variants.id), если есть');

            /* =========================================================
             * SNAPSHOT DATA
             * ========================================================= */

            $table->string('title_snapshot', 255)->nullable()->comment('Название на момент события');
            $table->string('sku_snapshot', 191)->nullable()->comment('SKU на момент события');
            $table->string('barcode_snapshot', 64)->nullable()->comment('Barcode на момент события');

            $table->unsignedInteger('qty')->default(1)->comment('Количество на момент события');

            $table->decimal('price', 18, 2)->nullable()->comment('Цена');
            $table->decimal('old_price', 18, 2)->nullable()->comment('Старая цена');
            $table->decimal('discount_price', 18, 2)->nullable()->comment('Цена со скидкой');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), nullable');

            $table->json('meta')->nullable()->comment('Опции/атрибуты/выбранные свойства на момент события');

            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mcei_tenant');

            $table->index(['cart_event_id'], 'ix_mcei_event');
            $table->index(['company_id', 'storefront_id', 'cart_event_id'], 'ix_mcei_event_tenant');

            $table->index(['cart_item_id'], 'ix_mcei_cart_item');
            $table->index(['product_id'], 'ix_mcei_product');
            $table->index(['product_variant_id'], 'ix_mcei_variant');

            $table->index(['storefront_id', 'product_id'], 'ix_mcei_sf_product');
            $table->index(['storefront_id', 'product_variant_id'], 'ix_mcei_sf_variant');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mcei_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mcei_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe: событие обязано быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'cart_event_id'], 'fk_mcei_event_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_cart_events')
                ->cascadeOnDelete();

            // cart_item может исчезнуть (корзину почистили) — снимок остаётся
            $table->foreign('cart_item_id', 'fk_mcei_cart_item')
                ->references('id')
                ->on('market_cart_items')
                ->nullOnDelete();

            // для снапшота связи мягкие (nullable => nullOnDelete)
            $table->foreign('product_id', 'fk_mcei_product')
                ->references('id')
                ->on('market_products')
                ->nullOnDelete();

            $table->foreign('product_variant_id', 'fk_mcei_variant')
                ->references('id')
                ->on('market_product_variants')
                ->nullOnDelete();

            $table->foreign('currency_id', 'fk_mcei_currency')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            $table->comment('Маркет: снимки позиций корзины по событиям (analytics/audit), tenant-safe по событию');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_cart_event_items');
    }
};
