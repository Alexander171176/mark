<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_brands', function (Blueprint $table) {
            $table->id()->comment('ID бренда');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_brands_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность бренда');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('is_featured')->default(false)->comment('Выводить как “популярный/избранный бренд”');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->text('logo')->nullable()->comment('Резервное поле логотипа (URL/путь), если нужно');

            $table->string('title')->comment('Название бренда');
            $table->string('slug', 191)->comment('Slug бренда');

            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->longText('description')->nullable()->comment('Описание бренда');

            $table->string('country_code', 2)->nullable()->comment('Страна бренда ISO2 (опционально)');
            $table->string('site_url', 255)->nullable()->comment('Сайт бренда (опционально)');

            /* =========================================================
             * SEO
             * ========================================================= */

            $table->string('meta_title', 255)->nullable()->comment('SEO Title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO Keywords');
            $table->text('meta_desc')->nullable()->comment('SEO Description');

            $table->string('canonical_url')->nullable()->comment('Canonical URL (локально)');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            /* =========================================================
             * METRICS / SERVICE
             * ========================================================= */

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // slug уникален внутри storefront + locale
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_market_brands_storefront_locale_slug');

            /**
             * ✅ Критично для tenant-safe FK из pivot’ов, где достаточно (storefront_id, brand_id)
             * (storefront_id, id) должен быть UNIQUE.
             */
            $table->unique(['storefront_id', 'id'], 'uq_market_brands_storefront_id');

            /**
             * ✅ Если где-то понадобится строгая tenant-ссылка (company_id, storefront_id, brand_id)
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_brands_tenant_id');

            // Индексы для списков/фильтров
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_brands_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_market_brands_list');
            $table->index(['storefront_id', 'locale', 'is_featured', 'activity', 'sort'], 'ix_market_brands_featured_list');

            $table->index('locale', 'ix_market_brands_locale');
            $table->index('views', 'ix_market_brands_views');
            $table->index('activity', 'ix_market_brands_activity');

            $table->comment('Маркет: бренды, локали независимы. Tenant-изоляция (company/storefront) + индексы под фильтры.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_brands');
    }
};
