<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_storefront_has_currencies', function (Blueprint $table) {
            // Tenant (изоляция)
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            // Витрина (market_storefronts.id)
            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mshc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // Валюта (currencies.id) — общая мультивалютность
            $table->foreignId('currency_id')
                ->comment('Валюта (currencies.id)')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность валюты на витрине');
            $table->boolean('is_default')->default(false)->comment('Валюта по умолчанию для витрины');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка валют на витрине');

            $table->timestamps();

            $table->unique(['company_id','storefront_id','currency_id'], 'uq_mshc_company_storefront_currency');
            $table->unique(['storefront_id','currency_id'], 'uq_mshc_storefront_currency');

            $table->index(['company_id', 'storefront_id'], 'ix_mshc_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mshc_list');
            $table->index(['storefront_id', 'is_default'], 'ix_mshc_default_lookup');

            $table->comment('Маркет: доступные валюты витрины (pivot), мультивалютность общая через currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_storefront_has_currencies');
    }
};
