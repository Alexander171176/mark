<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_warehouses', function (Blueprint $table) {
            $table->id()->comment('ID склада');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность склада');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->boolean('is_default')->default(false)->comment('Склад по умолчанию (контроль: в приложении)');
            $table->string('type', 32)->default('warehouse')->comment('warehouse|fulfillment|pickup|returns');

            $table->string('title')->comment('Название склада');

            // Адрес
            $table->string('country', 2)->nullable()->comment('Страна ISO2 (KZ/...)');
            $table->string('city', 128)->nullable()->comment('Город');
            $table->string('postcode', 32)->nullable()->comment('Индекс');
            $table->text('address')->nullable()->comment('Полный адрес');

            // Контакты/режим работы
            $table->string('phone', 50)->nullable()->comment('Телефон склада');
            $table->string('email', 255)->nullable()->comment('Email склада');
            $table->string('work_time', 255)->nullable()->comment('Время работы');

            // Мессенджер
            $table->string('messenger_type', 32)->nullable()->comment('whatsapp|telegram|viber|...');
            $table->string('messenger_contact', 255)->nullable()->comment('номер|username|ссылка');
            $table->boolean('messenger_public')->default(true)->comment('Показывать ли клиентам');

            // Геопозиция
            $table->decimal('lat', 10, 7)->nullable()->comment('Широта');
            $table->decimal('lng', 10, 7)->nullable()->comment('Долгота');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES (сначала ключи, потом FK)
             * ========================================================= */

            // ✅ нужно для tenant-safe FK на (company_id, storefront_id, warehouse_id)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_warehouses_tenant_id');

            // (опционально) уникальность названия склада в рамках витрины
            $table->unique(['storefront_id', 'title'], 'uq_market_warehouses_storefront_title');

            $table->index(['company_id', 'storefront_id'], 'ix_market_warehouses_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_market_warehouses_storefront_list');
            $table->index(['storefront_id', 'is_default'], 'ix_market_warehouses_storefront_default');
            $table->index(['storefront_id', 'type'], 'ix_market_warehouses_storefront_type');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_warehouses_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment('Маркет: склады компании/витрины (tenant-safe, cascade-only, без soft deletes)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_warehouses');
    }
};
