<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_methods', function (Blueprint $table) {

            /* =========================================================
             * BASE
             * ========================================================= */

            $table->id()->comment('ID способа доставки');

            /* =========================================================
             * TENANT / STOREFRONT (поля)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность способа доставки');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en)');
            $table->string('slug', 191)->comment('Slug/код метода (уникален в рамках витрины+локали)');

            $table->string('title')->comment('Название способа доставки');
            $table->string('type', 32)->comment('Тип: courier|pickup|post|...');

            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->text('description')->nullable()->comment('Подробности');

            /* =========================================================
             * PRICING
             * ========================================================= */

            $table->string('price_mode', 20)->default('fixed')->comment('fixed|zone|distance|pickup_free|table');
            $table->decimal('base_price', 18, 2)->default(0)->comment('Базовая стоимость');

            $table->foreignId('currency_id')
                ->comment('Валюта стоимости доставки (currencies.id)');

            /* =========================================================
             * LIMITS
             * ========================================================= */

            $table->decimal('min_order_total', 18, 2)->nullable()->comment('Минимальная сумма заказа');
            $table->decimal('free_from_total', 18, 2)->nullable()->comment('Бесплатно от суммы');

            $table->decimal('min_weight', 18, 6)->nullable()->comment('Мин. вес');
            $table->decimal('max_weight', 18, 6)->nullable()->comment('Макс. вес');

            /* =========================================================
             * DELIVERY TIME
             * ========================================================= */

            $table->unsignedSmallInteger('min_days')->nullable()->comment('Мин. срок (дней)');
            $table->unsignedSmallInteger('max_days')->nullable()->comment('Макс. срок (дней)');

            /* =========================================================
             * OPTIONAL WAREHOUSE LINK
             * ========================================================= */

            $table->unsignedBigInteger('warehouse_id')
                ->nullable()
                ->comment('Склад (market_warehouses.id), если метод привязан к складу');

            $table->boolean('supports_time_slots')->default(false)->comment('Поддержка слотов доставки');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mdm_tenant_id');

            // (опционально, но полезно если где-то FK только по витрине)
            $table->unique(['storefront_id', 'id'], 'uq_mdm_storefront_id');

            // slug уникален в рамках витрины+локали
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mdm_storefront_locale_slug');

            $table->index(['company_id', 'storefront_id'], 'ix_mdm_tenant');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mdm_tenant_locale');

            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mdm_list');
            $table->index(['storefront_id', 'locale', 'type', 'activity', 'sort'], 'ix_mdm_type_list');
            $table->index(['storefront_id', 'locale', 'price_mode', 'activity', 'sort'], 'ix_mdm_price_mode_list');

            $table->index(['warehouse_id'], 'ix_mdm_warehouse_id');
            $table->index('locale', 'ix_mdm_locale');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mdm_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            // storefront принадлежит company (tenant-safe)
            $table->foreign(['company_id', 'storefront_id'], 'fk_mdm_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->foreign('currency_id', 'fk_mdm_currency')
                ->references('id')
                ->on('currencies')
                ->restrictOnDelete();

            /**
             * ВАЖНО:
             * Для композитного FK SET NULL невозможен (company_id/storefront_id NOT NULL).
             * Поэтому: RESTRICT — склад нельзя удалить, пока используется методом доставки.
             * Если нужно "удалять" склад — делай softDeletes у складов или сначала отвязывай warehouse_id.
             */
            $table->foreign(['company_id', 'storefront_id', 'warehouse_id'], 'fk_mdm_warehouse_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_warehouses')
                ->restrictOnDelete();

            $table->comment('Маркет: способы доставки, локали независимы (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_methods');
    }
};
