<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_cart_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции корзины');

            /* =========================================================
             * TENANT / STOREFRONT (дублируем из корзины)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания (market_companies.id), дублируется из корзины');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id), дублируется из корзины');

            /* =========================================================
             * CART (tenant-safe)
             * =========================================================
             * Единый подход: cart_id ссылается на market_carts через композитный FK
             * (company_id, storefront_id, cart_id) -> market_carts(company_id, storefront_id, id)
             * Поэтому НЕ делаем cart_id ->constrained() (чтобы не плодить дубль-FK).
             */

            $table->unsignedBigInteger('cart_id')
                ->comment('Корзина (market_carts.id)');

            /* =========================================================
             * PRODUCT / VARIANT (мягкие ссылки: снимок в корзине)
             * ========================================================= */

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), nullable (история/удаления товаров)');

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id), nullable');

            /* =========================================================
             * STATE
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность позиции');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка позиции');
            $table->boolean('checked')->default(true)->comment('Выбрана ли позиция');

            $table->unsignedInteger('qty')->default(1)->comment('Количество');

            /* =========================================================
             * SNAPSHOT (чтобы корзина жила независимо от каталога)
             * ========================================================= */

            $table->string('title_snapshot', 255)->nullable()->comment('Название на момент добавления');
            $table->string('sku_snapshot', 191)->nullable()->comment('SKU на момент добавления');
            $table->string('barcode_snapshot', 64)->nullable()->comment('Barcode на момент добавления');

            $table->decimal('price_snapshot', 18, 2)->nullable()->comment('Цена на момент добавления/обновления');
            $table->decimal('old_price_snapshot', 18, 2)->nullable()->comment('Старая цена на момент добавления');
            $table->decimal('discount_price_snapshot', 18, 2)->nullable()->comment('Цена со скидкой на момент добавления');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта snapshot (currencies.id), nullable');

            $table->json('meta')->nullable()->comment('Опции/атрибуты/выбранные свойства и др.');

            $table->timestamps();
            $table->softDeletes();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            /**
             * ✅ Практично и без “двойных уникальностей”:
             * - один товар без варианта должен быть единственный в корзине
             * - один вариант должен быть единственный в корзине
             *
             * В MySQL можно сделать через GENERATED COLUMN, но мы держим рационально:
             * 1) уникальность по варианту (когда variant NOT NULL)
             * 2) для товара без варианта — индекс + контроль в приложении
             *
             * Если хочешь 100% на уровне БД — скажи, дам вариант с generated key.
             */
            $table->unique(['cart_id', 'product_variant_id'], 'uq_mci_cart_variant');

            // ускорение “один товар без варианта”
            $table->index(['cart_id', 'product_id', 'product_variant_id'], 'ix_mci_cart_product_variant');

            // списки корзины
            $table->index(['cart_id', 'activity', 'sort'], 'ix_mci_cart_list');

            // tenant
            $table->index(['company_id', 'storefront_id'], 'ix_mci_tenant');

            // поиск по каталогу/аналитика
            $table->index(['storefront_id', 'product_id'], 'ix_mci_sf_product');
            $table->index(['storefront_id', 'product_variant_id'], 'ix_mci_sf_variant');

            $table->index(['currency_id'], 'ix_mci_currency');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mci_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mci_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe: корзина обязана быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'cart_id'], 'fk_mci_cart_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_carts')
                ->cascadeOnDelete();

            // мягкие ссылки (корзина не должна “умирать” из-за чистки каталога)
            $table->foreign('product_id', 'fk_mci_product')
                ->references('id')
                ->on('market_products')
                ->nullOnDelete();

            $table->foreign('product_variant_id', 'fk_mci_variant')
                ->references('id')
                ->on('market_product_variants')
                ->nullOnDelete();

            $table->foreign('currency_id', 'fk_mci_currency')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();

            $table->comment('Маркет: позиции корзины (tenant-safe, snapshots, checked/qty, мягкие связи к каталогу)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_cart_items');
    }
};
