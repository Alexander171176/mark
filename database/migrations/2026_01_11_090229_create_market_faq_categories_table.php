<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_faq_categories
 * Категории FAQ (дерево) для витрины, tenant-safe как категории/страницы.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_faq_categories', function (Blueprint $table) {
            $table->id()->comment('ID категории FAQ');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_faq_categories_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TREE
             * ========================================================= */

            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->comment('Родительская категория FAQ (market_faq_categories.id)');

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность категории');

            $table->unsignedTinyInteger('level')->default(1)->comment('Уровень вложенности (кэш, 1..N)');
            $table->boolean('in_menu')->default(true)->comment('Показывать в меню/FAQ навигации');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->text('svg')->nullable()->comment('Иконка (SVG), опционально');
            $table->string('title')->comment('Название категории');
            $table->string('slug', 191)->comment('Slug категории');

            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->text('description')->nullable()->comment('Описание');

            /* =========================================================
             * SEO
             * ========================================================= */

            $table->string('meta_title', 255)->nullable()->comment('SEO Title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO Keywords');
            $table->text('meta_desc')->nullable()->comment('SEO Description');

            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL (локально)');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // ✅ Критично для tenant-safe self-FK:
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_faq_categories_tenant_id');

            // slug уникален внутри storefront + locale
            $table->index(['storefront_id', 'locale', 'slug'], 'ix_market_faq_categories_route');

            // индексы как у категорий
            $table->index(['storefront_id', 'locale', 'parent_id', 'activity', 'sort'], 'ix_market_faq_categories_list');
            $table->index(['storefront_id', 'locale', 'parent_id'], 'ix_market_faq_categories_tree');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_faq_categories_tenant_locale');
            $table->index(['storefront_id', 'locale', 'level'], 'ix_market_faq_categories_level');
            $table->index(['storefront_id', 'locale', 'in_menu', 'activity', 'sort'], 'ix_market_faq_categories_menu');
            $table->index('views', 'ix_market_faq_categories_views');
            $table->index('locale', 'ix_market_faq_categories_locale');

            $table->comment('Маркет: категории FAQ (дерево), локали независимы. Tenant-изоляция и защита parent внутри storefront.');
        });

        // ✅ Self-FK добавляем ПОСЛЕ создания таблицы (и без SET NULL на company/storefront)
        Schema::table('market_faq_categories', function (Blueprint $table) {
            $table->foreign(['company_id', 'storefront_id', 'parent_id'], 'fk_market_faq_categories_parent_same_storefront')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_faq_categories')
                ->restrictOnDelete(); // ✅ вместо nullOnDelete()
        });
    }

    public function down(): void
    {
        Schema::table('market_faq_categories', function (Blueprint $table) {
            $table->dropForeign('fk_market_faq_categories_parent_same_storefront');
        });

        Schema::dropIfExists('market_faq_categories');
    }
};
