<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_faq_items
 * Элементы FAQ (вопрос-ответ), привязка к категории FAQ.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_faq_items', function (Blueprint $table) {
            $table->id()->comment('ID элемента FAQ (вопрос/ответ)');

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_faq_items_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // Категория FAQ (tenant-safe)
            $table->unsignedBigInteger('faq_category_id')
                ->comment('Категория FAQ (market_faq_categories.id)');

            // tenant-safe: категория должна быть из этой же витрины/компании
            $table->foreign(
                ['company_id', 'storefront_id', 'faq_category_id'],
                'fk_market_faq_items_category_tenant'
            )
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_faq_categories')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность элемента FAQ');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка внутри категории');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('question', 255)->comment('Вопрос');
            $table->longText('answer')->nullable()->comment('Ответ (HTML/Markdown)');

            // полезно: вынести “закреплённые/частые”
            $table->boolean('is_featured')->default(false)->comment('Закреплённый/частый вопрос');

            // метрики (как OZON: полезность/просмотры)
            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $table->unsignedInteger('helpful_yes')->default(0)->comment('Голосов "полезно"');
            $table->unsignedInteger('helpful_no')->default(0)->comment('Голосов "не полезно"');

            // автор/модерация (опционально)
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->comment('Кто обновил (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // быстрые списки
            $table->index(['storefront_id', 'locale', 'faq_category_id', 'activity', 'sort'], 'ix_market_faq_items_list');
            $table->index(['faq_category_id', 'activity', 'sort'], 'ix_market_faq_items_category_list');
            $table->index(['storefront_id', 'is_featured', 'activity'], 'ix_market_faq_items_featured');
            $table->index('locale', 'ix_market_faq_items_locale');
            $table->index('views', 'ix_market_faq_items_views');

            $table->comment('Маркет: элементы FAQ (вопрос/ответ), tenant-safe, с сортировкой и метриками полезности.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_faq_items');
    }
};
