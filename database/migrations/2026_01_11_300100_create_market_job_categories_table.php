<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_job_categories', function (Blueprint $table) {
            $table->id()->comment('ID категории вакансий');

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mjc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * ✅ КРИТИЧНО:
             * Индекс (company_id, storefront_id, id) должен существовать ДО self-FK,
             * иначе MySQL выдаст 1822 Missing index.
             */
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mjc_tenant_id');

            /* PARENT (self) tenant-safe */
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->comment('Родительская категория (market_job_categories.id)');

            /**
             * ⚠️ SET NULL нельзя, потому что company_id/storefront_id NOT NULL.
             * Поэтому используем RESTRICT.
             */
            $table->foreign(['company_id', 'storefront_id', 'parent_id'], 'fk_mjc_parent_same_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_categories')
                ->restrictOnDelete();

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность категории');

            $table->unsignedTinyInteger('level')->default(1)->comment('Уровень вложенности (кэш, 1..N)');
            $table->boolean('in_menu')->default(true)->comment('Показывать категорию в меню');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->text('svg')->nullable()->comment('Иконка (SVG), опционально');
            $table->string('title')->comment('Название категории');
            $table->string('slug', 191)->comment('Slug категории');

            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->text('description')->nullable()->comment('Описание');

            // SEO
            $table->string('meta_title', 255)->nullable()->comment('SEO Title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO Keywords');
            $table->text('meta_desc')->nullable()->comment('SEO Description');

            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL (локально)');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            $table->timestamps();

            /* UNIQUE / INDEXES */

            // slug уникален внутри storefront + locale
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mjc_storefront_locale_slug');

            // полезно, если кто-то будет ссылаться через (storefront_id, id)
            $table->unique(['storefront_id', 'id'], 'uq_mjc_storefront_id');

            $table->index(['storefront_id', 'locale', 'parent_id', 'activity', 'sort'], 'ix_mjc_list');
            $table->index(['storefront_id', 'locale', 'parent_id'], 'ix_mjc_tree');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mjc_tenant_locale');
            $table->index(['storefront_id', 'locale', 'level'], 'ix_mjc_level');
            $table->index(['storefront_id', 'locale', 'in_menu', 'activity', 'sort'], 'ix_mjc_menu');
            $table->index('views', 'ix_mjc_views');
            $table->index('locale', 'ix_mjc_locale');

            $table->comment('Маркет: категории вакансий (дерево), tenant-safe parent внутри витрины.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_job_categories');
    }
};
