<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefront_settings', function (Blueprint $table) {
            $table->id()->comment('ID настроек витрины');

            // Tenant (изоляция)
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            // Витрина (market_storefronts.id)
            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // tenant-safe привязка витрины к компании
            $table->foreign(['company_id', 'storefront_id'], 'fk_msfs_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->string('theme')->nullable()->comment('Тема/шаблон витрины');
            $table->unsignedSmallInteger('settings_version')->default(1)->comment('Версия структуры settings JSON');

            $table->unsignedSmallInteger('products_per_page')->default(24)->comment('Товаров на странице каталога');
            $table->boolean('allow_guest_checkout')->default(true)->comment('Разрешить оформление заказа гостем');

            $table->boolean('show_out_of_stock')->default(true)->comment('Показывать товары без остатка');
            $table->boolean('enable_reviews')->default(true)->comment('Включить отзывы');

            $table->boolean('enable_delivery')->default(true)->comment('Включить доставку');
            $table->boolean('enable_payments')->default(true)->comment('Включить онлайн-оплату (маркет)');

            $table->json('settings')->nullable()->comment('Произвольные настройки витрины (JSON)');

            $table->timestamps();

            // one-to-one: ровно один набор настроек на витрину (tenant-safe)
            $table->unique(['company_id', 'storefront_id'], 'uq_msfs_company_storefront');

            // индексы под выборки
            $table->index(['company_id', 'storefront_id'], 'ix_msfs_tenant');
            $table->index(['enable_delivery', 'enable_payments'], 'ix_msfs_delivery_payments');

            $table->comment('Маркет: общие настройки витрины (one-to-one)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_settings');
    }
};
