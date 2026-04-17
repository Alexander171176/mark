<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_return_items', function (Blueprint $table) {
            $table->id()->comment('ID позиции возврата товара');

            /* =========================================================
             * TENANT / STOREFRONT (tenant-safe)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mreti_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * RETURN LINK (tenant-safe)
             * Требование: в market_returns есть uq (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('return_id')
                ->comment('Возврат товара (market_returns.id)');

            $table->foreign(['company_id', 'storefront_id', 'return_id'], 'fk_mreti_return_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_returns')
                ->cascadeOnDelete();

            /* =========================================================
             * ORDER ITEM LINK (tenant-safe)
             * Требование: в market_order_items есть uq (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('order_item_id')
                ->comment('Строка заказа (market_order_items.id)');

            $table->foreign(['company_id', 'storefront_id', 'order_item_id'], 'fk_mreti_order_item_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_order_items')
                ->cascadeOnDelete();

            /* =========================================================
             * PRODUCT / VARIANT (optional)
             * ВАЖНО: FK делаем обычными (без композитов), а tenant-safe
             * контролируем на уровне приложения (или триггеров), иначе
             * MySQL часто требует доп. уникальные индексы в referenced таблицах.
             * ========================================================= */

            $table->foreignId('product_id')
                ->nullable()
                ->comment('Товар (market_products.id), опционально')
                ->constrained('market_products')
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->comment('Вариант товара (market_product_variants.id), опционально')
                ->constrained('market_product_variants')
                ->nullOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность позиции');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * SNAPSHOT (чтобы возврат не зависел от будущих правок товара)
             * ========================================================= */

            $table->string('title', 255)->comment('Название (snapshot)');
            $table->string('sku', 191)->nullable()->comment('SKU (snapshot)');
            $table->string('unit', 30)->nullable()->comment('Единица измерения (snapshot)');

            $table->decimal('quantity', 18, 6)->default(1)->comment('Количество к возврату');

            /* =========================================================
             * REASON / CONDITION / INSPECTION
             * ========================================================= */

            $table->string('reason_code', 64)->nullable()->comment('Причина по позиции');
            $table->string('reason_text', 255)->nullable()->comment('Комментарий причины');

            $table->string('item_condition', 32)->nullable()->comment('Состояние: new/opened/used/damaged');

            $table->string('inspection_result', 32)->nullable()->comment('Итог осмотра: accepted/rejected/partial');
            $table->string('inspection_note', 255)->nullable()->comment('Комментарий осмотра');

            $table->json('meta')->nullable()->comment('Доп. данные (фото дефекта, признаки)');
            $table->timestamps();

            /* =========================================================
             * INDEXES
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mreti_tenant');

            $table->index(
                ['company_id', 'storefront_id', 'return_id', 'activity', 'sort'],
                'ix_mreti_return_list'
            );

            $table->index(
                ['company_id', 'storefront_id', 'order_item_id'],
                'ix_mreti_order_item'
            );

            $table->index(['product_id'], 'ix_mreti_product');
            $table->index(['product_variant_id'], 'ix_mreti_variant');

            // (опционально) защита от дублей: одна строка заказа один раз в рамках возврата
            $table->unique(['return_id', 'order_item_id'], 'uq_mreti_return_order_item');

            $table->comment('Маркет: позиции возврата товара (return items) — tenant-safe по return/order_item, со snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_return_items');
    }
};
