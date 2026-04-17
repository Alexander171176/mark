<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_favorites', function (Blueprint $table) {
            $table->id()->comment('ID избранного');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * USER
             * ========================================================= */

            $table->foreignId('user_id')
                ->comment('Пользователь (users.id)');

            /* =========================================================
             * PRODUCT / VARIANT
             * ========================================================= */

            $table->unsignedBigInteger('product_id')
                ->comment('Товар (market_products.id)');

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант (market_product_variants.id), опционально');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // (на будущее, если вдруг понадобится tenant-safe ссылка на эту таблицу)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mf_tenant_id');

            /**
             * Практичный кейс:
             * - избранное обычно на уровне товара
             * - если variant_id заполнен — это “точечное” избранное варианта
             *
             * Поэтому:
             * 1) уникальность “товар в избранном” (без учета варианта) — обязательно
             * 2) уникальность “вариант в избранном” — отдельно, когда variant_id NOT NULL
             *
             * (MySQL допускает много NULL в UNIQUE — это нам подходит.)
             */
            $table->unique(['storefront_id', 'user_id', 'product_id'], 'uq_mf_sf_user_product');
            $table->unique(['storefront_id', 'user_id', 'product_variant_id'], 'uq_mf_sf_user_variant');

            $table->index(['company_id', 'storefront_id', 'user_id'], 'ix_mf_tenant_user');
            $table->index(['storefront_id', 'user_id'], 'ix_mf_sf_user');

            $table->index(['storefront_id', 'product_id'], 'ix_mf_sf_product');
            $table->index(['storefront_id', 'product_variant_id'], 'ix_mf_sf_variant');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // company
            $table->foreign('company_id', 'fk_mf_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company (tenant-safe)
            $table->foreign(['company_id', 'storefront_id'], 'fk_mf_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // user (справочник пользователей)
            $table->foreign('user_id', 'fk_mf_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            /**
             * PRODUCT:
             * Здесь рационально CASCADE:
             * товар удалили из витрины/каталога -> избранное на него должно исчезнуть.
             */
            $table->foreign(['storefront_id', 'product_id'], 'fk_mf_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * VARIANT (nullable):
             * ❗️Важно: НЕ используем nullOnDelete на композитном FK (storefront_id NOT NULL).
             * Рационально CASCADE: вариант удалили -> “избранное варианта” исчезает.
             */
            $table->foreign(['storefront_id', 'product_variant_id'], 'fk_mf_storefront_variant')
                ->references(['storefront_id', 'id'])
                ->on('market_product_variants')
                ->cascadeOnDelete();

            $table->comment('Маркет: избранное пользователей (tenant-safe, товар/вариант)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_favorites');
    }
};
