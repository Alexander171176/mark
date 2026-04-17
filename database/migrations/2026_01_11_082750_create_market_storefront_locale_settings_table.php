<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefront_locale_settings', function (Blueprint $table) {
            $table->id()->comment('ID локальных настроек витрины');

            // Tenant (изоляция)
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            // Витрина (market_storefronts.id)
            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_msfls_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->unsignedInteger('sort')->default(0)->comment('Позиция сортировки (локали витрины)');
            $table->boolean('activity')->default(false)->comment('Активность витрины в данной локали');

            $table->string('title')->nullable()->comment('Локальное название витрины');
            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок, слоган');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->text('description')->nullable()->comment('Локальное описание витрины');

            $table->string('meta_title', 255)->nullable()->comment('SEO Title по умолчанию (локально)');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO Keywords по умолчанию (локально)');
            $table->text('meta_desc')->nullable()->comment('SEO Description по умолчанию (локально)');

            // SEO контроль
            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL (локально)');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации (локально)');

            $table->timestamps();

            // tenant-safe уникальность локали витрины
            $table->unique(['company_id', 'storefront_id', 'locale'], 'uq_msfls_company_storefront_locale');

            $table->index(['company_id', 'storefront_id', 'activity', 'sort'], 'ix_msfls_tenant_list');
            $table->index(['locale'], 'ix_msfls_locale');

            $table->comment('Маркет: локальные настройки витрины (каждая локаль независима)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_locale_settings');
    }
};
