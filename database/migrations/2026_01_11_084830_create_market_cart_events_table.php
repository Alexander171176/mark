<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_cart_events', function (Blueprint $table) {
            $table->id()->comment('ID события корзины');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * CART (tenant-safe)
             * =========================================================
             * Единый подход: ссылка на корзину через композитный FK
             * (company_id, storefront_id, cart_id) -> market_carts(company_id, storefront_id, id)
             *
             * Поэтому НЕ делаем cart_id ->constrained(), чтобы не было дубля FK.
             */

            $table->unsignedBigInteger('cart_id')
                ->comment('Корзина (market_carts.id)');

            /* =========================================================
             * ACTOR
             * ========================================================= */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь-инициатор (users.id), null для гостя');

            $table->string('guest_token', 64)
                ->nullable()
                ->comment('Токен гостя (копия из корзины/сессии)');

            /* =========================================================
             * EVENT
             * ========================================================= */

            $table->string('event', 40)->comment('Тип события (add_item/remove_item/qty_set/...)');
            $table->string('source', 30)->default('web')->comment('Источник (web/mobile/api/admin/system)');
            $table->timestamp('event_at')->useCurrent()->comment('Время события');

            /* =========================================================
             * OPTIONAL LINKS (снимок/аудит → связи мягкие)
             * ========================================================= */

            $table->unsignedBigInteger('cart_item_id')
                ->nullable()
                ->comment('Позиция корзины (market_cart_items.id), nullable');

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), nullable');

            $table->unsignedBigInteger('product_variant_id')
                ->nullable()
                ->comment('Вариант (market_product_variants.id), nullable');

            /* =========================================================
             * CONTEXT
             * ========================================================= */

            $table->string('ip', 45)->nullable()->comment('IP (IPv4/IPv6)');
            $table->string('user_agent', 512)->nullable()->comment('User-Agent');
            $table->string('session_id', 191)->nullable()->comment('Session id (если есть)');
            $table->string('request_id', 64)->nullable()->comment('Корреляция запросов (trace id)');

            /* =========================================================
             * PAYLOAD
             * ========================================================= */

            $table->json('payload')->nullable()->comment('Данные события');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            /**
             * ✅ КРИТИЧНО для tenant-safe ссылок из market_cart_event_items:
             * FK (company_id, storefront_id, cart_event_id) -> (company_id, storefront_id, id)
             * Поэтому нужен UNIQUE/INDEX на (company_id, storefront_id, id) в этой таблице.
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mce_tenant_id');

            // Время/ленты
            $table->index(['company_id', 'storefront_id', 'event_at'], 'ix_mce_tenant_time');
            $table->index(['storefront_id', 'cart_id', 'event_at'], 'ix_mce_cart_time');
            $table->index(['storefront_id', 'user_id', 'event_at'], 'ix_mce_user_time');
            $table->index(['storefront_id', 'guest_token', 'event_at'], 'ix_mce_guest_time');

            // Типы событий / аналитика
            $table->index(['event', 'event_at'], 'ix_mce_event_time');
            $table->index(['product_id', 'event_at'], 'ix_mce_product_time');
            $table->index(['product_variant_id', 'event_at'], 'ix_mce_variant_time');

            // Корреляция запросов
            $table->index(['request_id'], 'ix_mce_request_id');

            // Для join'ов tenant-safe
            $table->index(['company_id', 'storefront_id', 'cart_id'], 'ix_mce_cart_tenant');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mce_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mce_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // tenant-safe: корзина обязана быть из этой же company+storefront
            $table->foreign(['company_id', 'storefront_id', 'cart_id'], 'fk_mce_cart_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_carts')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_mce_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // мягкие ссылки: это история/аудит, не должны ломаться при удалениях сущностей
            $table->foreign('cart_item_id', 'fk_mce_cart_item')
                ->references('id')
                ->on('market_cart_items')
                ->nullOnDelete();

            $table->foreign('product_id', 'fk_mce_product')
                ->references('id')
                ->on('market_products')
                ->nullOnDelete();

            $table->foreign('product_variant_id', 'fk_mce_variant')
                ->references('id')
                ->on('market_product_variants')
                ->nullOnDelete();

            $table->comment('Маркет: события корзины (history/analytics), tenant-safe по company+storefront');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_cart_events');
    }
};
