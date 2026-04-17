<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_categories', function (Blueprint $table) {
            $table->id()->comment('ID категории');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company (изоляция)
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_categories_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TREE (self parent)
             * ========================================================= */

            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->comment('Родительская категория (market_categories.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность категории');

            $table->unsignedTinyInteger('level')->default(1)->comment('Уровень вложенности (кэш, 1..N)');
            $table->boolean('in_menu')->default(true)->comment('Показывать категорию в меню');

            /* =========================================================
             * CONTENT / LOCALE
             * ========================================================= */

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->text('svg')->nullable()->comment('Иконка (SVG)');
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

            /* =========================================================
             * METRICS
             * ========================================================= */

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES (СНАЧАЛА ИНДЕКСЫ, ПОТОМ self-FK!)
             * ========================================================= */

            // slug уникален внутри storefront + locale
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_market_categories_storefront_locale_slug');

            /**
             * ✅ ВАЖНО ДЛЯ tenant-safe FK:
             * (id — PK, но MySQL для композитных FK требует подходящий индекс/unique)
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_categories_tenant_id');

            $table->unique(['storefront_id','id'], 'uq_market_categories_storefront_id');

            /**
             * ✅ КРИТИЧНО ДЛЯ self-FK с учетом locale:
             * parent должен быть из той же витрины/компании и той же locale
             */
            $table->unique(['company_id', 'storefront_id', 'locale', 'id'], 'uq_market_categories_tenant_locale_id');

            // Индексы (твой набор)
            $table->index(['storefront_id', 'locale', 'parent_id', 'activity', 'sort'], 'ix_market_categories_list');
            $table->index(['storefront_id', 'locale', 'parent_id'], 'ix_market_categories_tree');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_categories_tenant_locale');
            $table->index(['storefront_id', 'locale', 'level'], 'ix_market_categories_level');
            $table->index(['storefront_id', 'locale', 'in_menu', 'activity', 'sort'], 'ix_market_categories_menu');
            $table->index('views', 'ix_market_categories_views');
            $table->index('locale', 'ix_market_categories_locale');

            /* =========================================================
             * SELF FOREIGN KEY (parent) — В КОНЦЕ!
             * ========================================================= */

            $table->foreign(['company_id', 'storefront_id', 'locale', 'parent_id'], 'fk_market_categories_parent_same_locale')
                ->references(['company_id', 'storefront_id', 'locale', 'id'])
                ->on('market_categories')
                ->cascadeOnDelete();

            $table->comment('Маркет: категории (дерево), локали независимы. Tenant-изоляция и защита parent внутри storefront + locale.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_categories');
    }
};
