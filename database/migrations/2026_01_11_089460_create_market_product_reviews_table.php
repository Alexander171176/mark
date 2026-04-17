<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_product_reviews', function (Blueprint $table) {
            $table->id()->comment('ID отзыва');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mprev_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * AUTHOR
             * ========================================================= */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Автор отзыва (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * TARGET (PRODUCT / VARIANT) - tenant-safe by storefront
             * ========================================================= */

            $table->unsignedBigInteger('product_id')
                ->comment('Товар (market_products.id)');

            $table->foreign(['storefront_id', 'product_id'], 'fk_mprev_storefront_product')
                ->references(['storefront_id', 'id'])
                ->on('market_products')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id), если отзыв к варианту');

            /**
             * ⚠️ ВАЖНО:
             * (storefront_id, variant_id) -> ... ON DELETE SET NULL невозможен,
             * потому что storefront_id NOT NULL.
             * Поэтому только RESTRICT (или CASCADE — но обычно не надо).
             */
            $table->foreign(['storefront_id', 'variant_id'], 'fk_mprev_storefront_variant')
                ->references(['storefront_id', 'id'])
                ->on('market_product_variants')
                ->restrictOnDelete();

            /* =========================================================
             * VERIFIED PURCHASE (optional, tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ (market_orders.id), если отзыв по покупке');

            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mprev_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            $table->unsignedBigInteger('order_item_id')
                ->nullable()
                ->comment('Позиция заказа (market_order_items.id), если отзыв по конкретной позиции');

            $table->foreign(['company_id', 'storefront_id', 'order_item_id'], 'fk_mprev_order_item_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_items')
                ->restrictOnDelete();

            /* =========================================================
             * CONTENT
             * ========================================================= */

            $table->unsignedTinyInteger('rating')->comment('Оценка 1..5');
            $table->string('title', 255)->nullable()->comment('Заголовок отзыва');
            $table->text('text')->nullable()->comment('Текст отзыва');

            $table->json('pros')->nullable()->comment('Плюсы (json массив)');
            $table->json('cons')->nullable()->comment('Минусы (json массив)');
            $table->json('media')->nullable()->comment('Медиа (ссылки/ids)');

            /* =========================================================
             * MODERATION
             * ========================================================= */

            $table->string('status', 32)->default('pending')
                ->comment('Статус: pending/approved/rejected/hidden');

            $table->foreignId('moderated_by_user_id')
                ->nullable()
                ->comment('Кто модерировал (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')->nullable()->comment('Когда модерировали');
            $table->timestamp('published_at')->nullable()->comment('Когда опубликован');

            /* =========================================================
             * TECH / ANTIFRAUD
             * ========================================================= */

            $table->string('ip', 64)->nullable()->comment('IP (опционально)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent (опционально)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mprev_tenant_id');

            $table->unique(['storefront_id', 'user_id', 'product_id'], 'uq_mprev_storefront_user_product');

            $table->index(['company_id', 'storefront_id'], 'ix_mprev_tenant');

            $table->index(['storefront_id', 'status', 'created_at'], 'ix_mprev_storefront_status_time');
            $table->index(['storefront_id', 'product_id', 'status', 'created_at'], 'ix_mprev_product_status_time');

            $table->index(['order_id'], 'ix_mprev_order');
            $table->index(['order_item_id'], 'ix_mprev_order_item');
            $table->index(['variant_id'], 'ix_mprev_variant');

            $table->comment('Маркет: отзывы о товарах с модерацией (и подтверждённой покупкой), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_product_reviews');
    }
};
