<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_order_statuses', function (Blueprint $table) {
            $table->id()->comment('ID статуса заказа');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность статуса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('code', 64)->comment('Системный код статуса (new/paid/shipped/delivered/cancelled/...)');
            $table->string('title', 255)->comment('Название статуса (для UI)');

            $table->string('type', 32)
                ->default('info')
                ->comment('Тип для UI: info/success/warning/danger');

            $table->boolean('is_final')
                ->default(false)
                ->comment('Финальный статус (например delivered/cancelled/returned)');

            $table->boolean('is_default')
                ->default(false)
                ->comment('Статус по умолчанию для новых заказов (уникальность контролируется в приложении)');

            $table->json('meta')
                ->nullable()
                ->comment('Доп.данные (цвет, иконка, правила, маппинг и т.п.)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mos_tenant_id');

            // code уникален в рамках витрины+локали
            $table->unique(['storefront_id', 'locale', 'code'], 'uq_mos_storefront_locale_code');

            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mos_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mos_list');

            // быстрое получение default/final
            $table->index(['storefront_id', 'locale', 'is_default'], 'ix_mos_default');
            $table->index(['storefront_id', 'locale', 'is_final'], 'ix_mos_final');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mos_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mos_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment('Маркет: статусы заказов (tenant-safe), локали независимы');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_statuses');
    }
};
