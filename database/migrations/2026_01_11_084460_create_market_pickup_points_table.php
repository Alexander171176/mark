<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_pickup_points', function (Blueprint $table) {
            $table->id()->comment('ID пункта выдачи');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * OPTIONAL TENANT-SAFE WAREHOUSE LINK
             * ========================================================= */

            $table->unsignedBigInteger('warehouse_id')
                ->nullable()
                ->comment('Склад обслуживания (market_warehouses.id), опционально');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность пункта');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en)');
            $table->string('title')->comment('Название пункта');
            $table->string('slug', 191)->comment('Slug пункта');

            /* =========================================================
             * ADDRESS
             * ========================================================= */

            $table->string('country_code', 2)->nullable()->comment('Страна ISO2 (KZ/...)');
            $table->string('region', 128)->nullable()->comment('Область/регион');
            $table->string('city', 128)->nullable()->comment('Город');
            $table->string('district', 128)->nullable()->comment('Район/микрорайон');
            $table->string('postcode', 16)->nullable()->comment('Индекс');
            $table->text('address')->nullable()->comment('Полный адрес');
            $table->string('address_note', 255)->nullable()->comment('Как пройти/ориентир');

            /* =========================================================
             * GEO
             * ========================================================= */

            $table->decimal('lat', 10, 7)->nullable()->comment('Широта');
            $table->decimal('lng', 10, 7)->nullable()->comment('Долгота');

            /* =========================================================
             * CONTACTS
             * ========================================================= */

            $table->string('phone', 50)->nullable()->comment('Телефон');
            $table->string('email', 255)->nullable()->comment('Email');

            $table->string('messenger_type', 32)->nullable()->comment('whatsapp/telegram/...');
            $table->string('messenger_contact', 255)->nullable()->comment('номер/username/ссылка');
            $table->boolean('messenger_public')->default(true)->comment('Показывать клиентам');

            /* =========================================================
             * SCHEDULE
             * ========================================================= */

            $table->string('work_time', 255)->nullable()->comment('Например: Пн–Пт 09:00–18:00');
            $table->json('schedule')->nullable()->comment('Структурированный график (опционально)');

            /* =========================================================
             * CAPABILITIES
             * ========================================================= */

            $table->boolean('supports_cash')->default(false)->comment('Оплата наличными');
            $table->boolean('supports_card')->default(false)->comment('Оплата картой');
            $table->boolean('supports_return')->default(false)->comment('Возвраты');
            $table->boolean('supports_try_on')->default(false)->comment('Примерка');

            /* =========================================================
             * LIMITS
             * ========================================================= */

            $table->decimal('max_weight', 18, 6)->nullable()->comment('Макс. вес выдачи');
            $table->decimal('max_length', 18, 3)->nullable()->comment('Макс. длина');
            $table->decimal('max_width', 18, 3)->nullable()->comment('Макс. ширина');
            $table->decimal('max_height', 18, 3)->nullable()->comment('Макс. высота');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpp_tenant_id');

            // (опционально полезно, если где-то FK будет по витрине)
            $table->unique(['storefront_id', 'id'], 'uq_mpp_storefront_id');

            // slug уникален внутри storefront + locale
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mpp_storefront_locale_slug');

            $table->index(['company_id', 'storefront_id'], 'ix_mpp_tenant');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mpp_tenant_locale');

            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mpp_list');
            $table->index(['storefront_id', 'locale', 'city', 'activity', 'sort'], 'ix_mpp_city_list');
            $table->index(['storefront_id', 'locale', 'postcode'], 'ix_mpp_postcode');

            $table->index(['warehouse_id'], 'ix_mpp_warehouse_id');
            $table->index('locale', 'ix_mpp_locale');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mpp_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * ВАЖНО:
             * Композитный FK => SET NULL невозможен (company_id/storefront_id NOT NULL).
             * Поэтому tenant-safe вариант: RESTRICT.
             */
            $table->foreign(['company_id', 'storefront_id', 'warehouse_id'], 'fk_mpp_warehouse_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_warehouses')
                ->restrictOnDelete();

            $table->comment('Маркет: пункты выдачи (ПВЗ), отдельная сущность от складов, tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_pickup_points');
    }
};
