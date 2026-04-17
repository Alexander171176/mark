<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_carts', function (Blueprint $table) {
            $table->id()->comment('ID корзины');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * OWNER (user/guest)
             * =========================================================
             * ВАЖНО: guest_token теперь живёт в market_cart_sessions,
             * тут оставляем только владельца-пользователя (если есть).
             */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь-владелец (users.id), nullable для гостя');

            /* =========================================================
             * STATUS / LIFECYCLE
             * ========================================================= */

            $table->string('status', 20)
                ->default('active')
                ->comment('Статус: active/abandoned/converted/expired/merged');

            $table->timestamp('last_activity_at')->nullable()
                ->comment('Последняя активность (для abandoned)');

            $table->timestamp('abandoned_at')->nullable()
                ->comment('Когда помечена как брошенная');

            $table->timestamp('converted_at')->nullable()
                ->comment('Когда конвертирована в заказ');

            $table->timestamp('expires_at')->nullable()
                ->comment('TTL корзины (если используешь истечение)');

            // Если объединили гостевую в пользовательскую — ссылка на целевую корзину
            $table->unsignedBigInteger('merged_into_cart_id')->nullable()
                ->comment('Если корзина была слита, ID целевой корзины');

            /* =========================================================
             * OPTIONAL META
             * ========================================================= */

            $table->string('currency_code', 10)->nullable()
                ->comment('Текущая валюта UI (не обязательно равна валютам позиций)');

            $table->string('note', 255)->nullable()
                ->comment('Заметка админа/системная');

            $table->json('meta')->nullable()
                ->comment('Доп. контекст (utm, source, device, etc.)');

            $table->timestamps();
            $table->softDeletes();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_carts_tenant_id');

            // Быстрый доступ к корзинам пользователя в пределах витрины
            $table->index(['storefront_id', 'user_id', 'status'], 'ix_market_carts_storefront_user_status');

            // Поиск брошенных/просроченных
            $table->index(['status', 'last_activity_at'], 'ix_market_carts_status_last_activity');
            $table->index(['storefront_id', 'status', 'last_activity_at'], 'ix_market_carts_sf_status_last_activity');
            $table->index(['expires_at'], 'ix_market_carts_expires_at');

            // Tenant list
            $table->index(['company_id', 'storefront_id'], 'ix_market_carts_tenant');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_market_carts_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company (tenant-safe)
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_carts_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_market_carts_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // self FK (nullable => nullOnDelete допустим)
            $table->foreign('merged_into_cart_id', 'fk_market_carts_merged_into')
                ->references('id')
                ->on('market_carts')
                ->nullOnDelete();

            $table->comment('Маркет: корзины (tenant-safe, история/состояния; гостевые ключи в market_cart_sessions)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_carts');
    }
};
