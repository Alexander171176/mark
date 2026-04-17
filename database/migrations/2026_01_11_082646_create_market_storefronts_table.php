<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_storefronts', function (Blueprint $table) {
            $table->id()->comment('ID витрины');

            // Компания-владелец витрины (market_companies.id)
            $table->foreignId('company_id')
                ->comment('Компания-владелец витрины (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')->default(0)->comment('Позиция сортировки');
            $table->boolean('activity')->default(false)->comment('Активность витрины');

            $table->string('slug', 191)->comment('Slug витрины (уникален в рамках компании)');
            $table->boolean('is_main')->default(false)->comment('Флаг главной витрины компании (контроль в приложении)');

            // Домены/хосты
            $table->string('domain', 255)->nullable()->comment('Домен витрины (если отдельный домен)');
            $table->string('subdomain', 191)->nullable()->comment('Поддомен витрины (если используется)');
            $table->string('primary_host', 255)->nullable()->comment('Канонический host витрины (например shop.example.kz)');

            // Настройки по умолчанию
            $table->string('default_locale', 10)->nullable()->comment('Локаль витрины по умолчанию (ru/kk/en/...)');

            // Валюта по умолчанию (справочник -> запрещаем удалять, если используется)
            $table->foreignId('default_currency_id')
                ->nullable()
                ->comment('Валюта витрины по умолчанию (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* ============================
             * УНИКАЛЬНОСТИ / ИНДЕКСЫ
             * ============================ */

            // ✅ КРИТИЧНО для tenant-safe FK: (company_id, id)
            $table->unique(['company_id', 'id'], 'uq_market_storefronts_tenant_id');

            // Уникальный slug в рамках компании
            $table->unique(['company_id', 'slug'], 'uq_market_storefronts_company_slug');

            // Канонический host (если используешь). NULL допускается.
            $table->unique(['primary_host'], 'uq_market_storefronts_primary_host');

            // Индексы
            $table->index(['company_id', 'activity', 'sort'], 'ix_market_storefronts_company_activity_sort');
            $table->index(['company_id', 'is_main'], 'ix_market_storefronts_company_is_main');
            $table->index(['activity'], 'ix_market_storefronts_activity');

            $table->comment('Маркет: витрины компаний (storefronts)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefronts');
    }
};
