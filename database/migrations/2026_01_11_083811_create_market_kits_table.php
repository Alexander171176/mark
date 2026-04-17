<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_kits', function (Blueprint $table) {
            $table->id()->comment('ID комплекта');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_kits_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */
            $table->boolean('activity')->default(false)->comment('Активность комплекта');
            $table->unsignedInteger('sort')->default(0)->comment('Позиция сортировки');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */
            $table->string('sku', 191)->nullable()->comment('Артикул комплекта (уникален в рамках витрины)');
            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('title')->comment('Название комплекта');
            $table->string('slug', 191)->comment('Slug комплекта');
            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->longText('description')->nullable()->comment('Описание');

            /* =========================================================
             * PRICING
             * ========================================================= */
            $table->decimal('price', 18, 2)->default(0)->comment('Цена');
            $table->decimal('old_price', 18, 2)->default(0)->comment('Старая цена');
            $table->decimal('discount_price', 18, 2)->nullable()->comment('Цена со скидкой');
            $table->boolean('is_manual')->default(true)->comment('Цена задана вручную (иначе пересчитана по курсу)');

            $table->foreignId('currency_id')
                ->comment('Валюта (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->string('barcode', 64)->nullable()->comment('Штрихкод');

            /* =========================================================
             * SEO
             * ========================================================= */
            $table->string('meta_title', 255)->nullable()->comment('SEO Title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO Keywords');
            $table->text('meta_desc')->nullable()->comment('SEO Description');

            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            /* =========================================================
             * METRICS / SERVICE
             * ========================================================= */
            $table->decimal('rating_avg', 3, 2)->default(0)->comment('Средний рейтинг');
            $table->unsignedInteger('rating_count')->default(0)->comment('Кол-во оценок');

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // slug уникален в рамках витрины и локали
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_market_kits_storefront_locale_slug');

            // SKU уникален в рамках витрины (NULL допускается многократно в MySQL)
            $table->unique(['storefront_id', 'sku'], 'uq_market_kits_storefront_sku');

            /**
             * ✅ Критично для tenant-safe FK из других таблиц:
             * (company_id, storefront_id, kit_id) -> (company_id, storefront_id, id)
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_kits_tenant_id');

            /**
             * ✅ Полезно, если где-то FK делается только по витрине:
             * (storefront_id, kit_id) -> (storefront_id, id)
             */
            $table->unique(['storefront_id', 'id'], 'uq_market_kits_storefront_id');

            // Листинг/фильтры
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_kits_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_market_kits_list');

            // Частые фильтры/поиски
            $table->index('locale', 'ix_market_kits_locale');
            $table->index('barcode', 'ix_market_kits_barcode');
            $table->index('views', 'ix_market_kits_views');
            $table->index('price', 'ix_market_kits_price');
            $table->index('old_price', 'ix_market_kits_old_price');
            $table->index('discount_price', 'ix_market_kits_discount_price');

            $table->comment('Маркет: комплекты товаров (kits), локали независимы, tenant-safe по company+storefront');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_kits');
    }
};
