<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_providers', function (Blueprint $table) {
            $table->id()->comment('ID провайдера доставки');

            $table->boolean('activity')->default(true)->comment('Активность провайдера');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            // Стабильный ключ для интеграций (cdek/dpd/boxberry/...)
            $table->string('code', 64)->comment('Уникальный код провайдера (cdek/dpd/boxberry/...)');
            $table->string('name', 255)->comment('Название провайдера');
            $table->string('type', 32)->default('api')->comment('Тип: api/manual/aggregator');

            // Возможности провайдера
            $table->boolean('supports_courier')->default(true)->comment('Поддерживает курьерскую доставку');
            $table->boolean('supports_pickup')->default(true)->comment('Поддерживает ПВЗ/пункты выдачи');
            $table->boolean('supports_tracking')->default(false)->comment('Поддерживает трекинг');
            $table->boolean('supports_cod')->default(false)->comment('Поддерживает наложенный платёж (COD)');

            $table->string('site_url', 255)->nullable()->comment('Сайт провайдера');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            // UNIQUE / INDEXES
            $table->unique('code', 'uq_market_delivery_providers_code');

            $table->index(['activity', 'sort'], 'ix_market_delivery_providers_list');
            $table->index(['activity', 'sort', 'code'], 'ix_market_delivery_providers_list_code');
            $table->index('type', 'ix_market_delivery_providers_type');

            $table->comment('Маркет: справочник служб доставки/провайдеров (CDEK/DPD/Boxberry и др.)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_providers');
    }
};
