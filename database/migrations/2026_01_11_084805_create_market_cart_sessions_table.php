<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_cart_sessions', function (Blueprint $table) {
            $table->id()->comment('ID сессии корзины');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS
             * ========================================================= */

            $table->unsignedBigInteger('cart_id')
                ->comment('Корзина (market_carts.id)');

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), nullable для гостя');

            /* =========================================================
             * SESSION KEYS
             * ========================================================= */

            // Токен гостя (основной ключ для восстановления/merge)
            $table->string('guest_token', 64)->nullable()
                ->comment('Токен гостевой сессии (uuid/хеш), используется для восстановления корзины');

            // Опционально: если хочешь поддержать “native” session id / device id
            $table->string('session_key', 128)->nullable()
                ->comment('Ключ сессии (например, Laravel session id / device id), опционально');

            /* =========================================================
             * LIFECYCLE / TTL
             * ========================================================= */

            $table->boolean('is_active')->default(true)
                ->comment('Активна ли сессия (текущая)');

            $table->timestamp('last_seen_at')->nullable()
                ->comment('Последняя активность по этой сессии');

            $table->timestamp('expires_at')->nullable()
                ->comment('TTL сессии (после истечения можно чистить)');

            /* =========================================================
             * OPTIONAL META (удобно для антифрода/аналитики)
             * ========================================================= */

            $table->string('ip', 45)->nullable()->comment('IP (IPv4/IPv6)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent');
            $table->json('meta')->nullable()->comment('Доп. контекст (utm, source, device, etc.)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ, если вдруг где-то понадобится composite FK на сессии
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mcs_tenant_id');

            // 1) Главный быстрый lookup гостевой корзины: (storefront + guest_token)
            // В отличие от market_carts, тут история не мешает — это “сессионная” сущность
            $table->unique(['storefront_id', 'guest_token'], 'uq_mcs_storefront_guest_token');

            // 2) Быстрый lookup активной сессии пользователя
            $table->index(['storefront_id', 'user_id', 'is_active'], 'ix_mcs_storefront_user_active');

            // 3) Быстрый lookup по cart
            $table->index(['cart_id', 'is_active'], 'ix_mcs_cart_active');

            // 4) Очистка по TTL
            $table->index(['expires_at'], 'ix_mcs_expires_at');

            // 5) Полезно, если используешь session_key
            $table->index(['storefront_id', 'session_key'], 'ix_mcs_storefront_session_key');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mcs_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mcs_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // cart tenant-safe: корзина должна быть из той же компании+витрины
            $table->foreign(['company_id', 'storefront_id', 'cart_id'], 'fk_mcs_cart_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_carts')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_mcs_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Маркет: сессии корзин (guest/user), ключи восстановления, TTL, tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_cart_sessions');
    }
};
