<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefront_pages', function (Blueprint $table) {
            $table->id()->comment('ID страницы витрины');

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_storefront_pages_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->comment('Родительская страница (market_storefront_pages.id)');

            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $table->boolean('activity')->default(true)->comment('Активность страницы');

            $table->unsignedTinyInteger('level')->default(1)->comment('Уровень вложенности (кэш, 1..N)');
            $table->boolean('in_menu')->default(true)->comment('Показывать страницу в меню');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->text('svg')->nullable()->comment('Иконка (SVG)');

            $table->string('title')->comment('Название страницы');
            $table->string('slug', 191)->comment('Slug страницы');

            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');

            $table->longText('content')->nullable()->comment('Контент страницы (HTML/Markdown/JSON — как решите)');
            $table->text('description')->nullable()->comment('Описание (опционально, как у категорий)');

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

            // ✅ ключ для tenant-safe ссылок и self-FK (должен существовать ДО добавления FK)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_storefront_pages_tenant_id');

            $table->index(['storefront_id', 'locale', 'slug'], 'ix_market_storefront_pages_route');

            $table->index(['storefront_id', 'locale', 'parent_id', 'activity', 'sort'], 'ix_market_storefront_pages_list');
            $table->index(['storefront_id', 'locale', 'parent_id'], 'ix_market_storefront_pages_tree');
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_storefront_pages_tenant_locale');
            $table->index(['storefront_id', 'locale', 'level'], 'ix_market_storefront_pages_level');
            $table->index(['storefront_id', 'locale', 'in_menu', 'activity', 'sort'], 'ix_market_storefront_pages_menu');
            $table->index('views', 'ix_market_storefront_pages_views');
            $table->index('locale', 'ix_market_storefront_pages_locale');

            $table->comment('Маркет: страницы витрины (дерево), локали независимы. Tenant-изоляция и защита parent внутри storefront.');
        });

        // ✅ Добавляем self-FK ПОСЛЕ создания таблицы (индексы уже точно есть)
        Schema::table('market_storefront_pages', function (Blueprint $table) {
            $table->foreign(['company_id', 'storefront_id', 'parent_id'], 'fk_market_storefront_pages_parent_same_storefront')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_storefront_pages')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('market_storefront_pages', function (Blueprint $table) {
            $table->dropForeign('fk_market_storefront_pages_parent_same_storefront');
        });

        Schema::dropIfExists('market_storefront_pages');
    }
};
