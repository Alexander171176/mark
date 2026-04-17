<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_products', function (Blueprint $table) {
            $table->id()->comment('ID товара');

            /* ============================
             * TENANT / STOREFRONT
             * ============================ */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_products_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* ============================
             * FLAGS / SORT
             * ============================ */

            $table->boolean('activity')->default(true)->comment('Активность товара');
            $table->unsignedInteger('sort')->default(0)->comment('Позиция сортировки');

            $table->boolean('left')->default(false)->comment('В левой колонке');
            $table->boolean('main')->default(false)->comment('В главном блоке');
            $table->boolean('right')->default(false)->comment('В правой колонке');

            $table->boolean('is_new')->default(false)->comment('Новинка');
            $table->boolean('is_hit')->default(false)->comment('Хит/рекомендованный');
            $table->boolean('is_sale')->default(false)->comment('Распродажа');

            /* ============================
             * CONTENT / LOCALE
             * ============================ */

            $table->text('img')->nullable()->comment('Резервное изображение (например для парсера)');

            // SKU в мультивендоре не может быть глобально уникальным
            $table->string('sku', 191)->nullable()->comment('Артикул/sku товара (уникален в рамках компании)');

            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('title')->comment('Название товара');
            $table->string('slug', 191)->comment('Slug товара');
            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->longText('description')->nullable()->comment('Полное описание');

            /* ============================
             * STOCK / ATTRIBUTES
             * ============================ */

            $table->unsignedInteger('quantity')->default(0)->comment('Остаток');
            $table->string('unit', 30)->nullable()->comment('Единица измерения (шт/кг/м2/...)');
            $table->unsignedInteger('weight')->default(0)->comment('Вес (граммы, если используется)');
            $table->string('availability', 255)->nullable()->comment('Наличие (текст/статус)');

            /* ============================
             * PRICING
             * ============================ */

            $table->decimal('price', 18, 2)->default(0)->comment('Цена');
            $table->decimal('old_price', 18, 2)->default(0)->comment('Старая цена');
            $table->decimal('discount_price', 18, 2)->nullable()->comment('Цена со скидкой (если есть)');

            $table->boolean('is_manual')
                ->default(true)
                ->comment('Цена задана вручную (иначе пересчитана по курсу)');

            $table->foreignId('currency_id')
                ->comment('Валюта (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            /**
             * ✅ Гарантия: валюта должна быть включена на витрине
             * ВАЖНО по рациональности:
             * - currency_id тут NOT NULL
             * - значит никакого SET NULL, только RESTRICT (или CASCADE, если ты хочешь удалять товары при отключении валюты)
             * Я ставлю RESTRICT, чтобы случайное удаление строки pivot не снесло товары.
             */
            $table->foreign(['storefront_id', 'currency_id'], 'fk_market_products_storefront_currency_enabled')
                ->references(['storefront_id', 'currency_id'])
                ->on('market_storefront_has_currencies')
                ->restrictOnDelete();

            $table->string('barcode', 64)->nullable()->comment('Штрихкод');

            /* ============================
             * SEO
             * ============================ */

            $table->string('meta_title', 255)->nullable()->comment('SEO title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO keywords');
            $table->text('meta_desc')->nullable()->comment('SEO description');

            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            /* ============================
             * METRICS
             * ============================ */

            $table->decimal('rating_avg', 3, 2)->default(0)->comment('Средний рейтинг');
            $table->unsignedInteger('rating_count')->default(0)->comment('Количество оценок');

            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* ============================
             * UNIQUE / INDEXES
             * ============================ */

            // slug уникален в рамках storefront + locale
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_market_products_storefront_locale_slug');

            // sku уникален в рамках company (NULL допускается многократно в MySQL)
            $table->unique(['company_id', 'sku'], 'uq_market_products_company_sku');

            /**
             * ✅ Ключи под композитные FK из других таблиц:
             * - (storefront_id, id) — для tenant-safe ссылок типа order_items -> products
             * - (company_id, storefront_id, id) — если где-то делаешь ещё более строгие tenant-safe ссылки
             */
            $table->unique(['storefront_id', 'id'], 'uq_market_products_storefront_id');
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_products_tenant_id');

            // Индексы для списков/фильтров
            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_market_products_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_market_products_list');

            $table->index('locale', 'ix_market_products_locale');
            $table->index('sku', 'ix_market_products_sku');
            $table->index('barcode', 'ix_market_products_barcode');
            $table->index('availability', 'ix_market_products_availability');

            $table->index('price', 'ix_market_products_price');
            $table->index('old_price', 'ix_market_products_old_price');
            $table->index('discount_price', 'ix_market_products_discount_price');

            $table->index('views', 'ix_market_products_views');
            $table->index('left', 'ix_market_products_left');
            $table->index('main', 'ix_market_products_main');
            $table->index('right', 'ix_market_products_right');
            $table->index('is_new', 'ix_market_products_is_new');
            $table->index('is_hit', 'ix_market_products_is_hit');
            $table->index('is_sale', 'ix_market_products_is_sale');

            $table->comment('Маркет: товары, локали независимы (tenant/storefront isolation).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_products');
    }
};
