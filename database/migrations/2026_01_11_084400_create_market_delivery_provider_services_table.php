<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_provider_services', function (Blueprint $table) {
            $table->id()->comment('ID сервиса/тарифа провайдера');

            $table->foreignId('provider_id')
                ->comment('Провайдер (market_delivery_providers.id)')
                ->constrained('market_delivery_providers')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность сервиса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('service_code', 64)->comment('Код сервиса у провайдера (например: CDEK_PVZ)');
            $table->string('name', 255)->comment('Название сервиса (например: ПВЗ стандарт)');

            $table->string('delivery_type', 32)->nullable()->comment('Тип доставки: courier/pickup/post/locker');
            $table->string('country_code', 2)->nullable()->comment('Ограничение по стране (ISO2: KZ/...)');

            $table->boolean('supports_tracking')->default(false)->comment('Поддерживает трекинг');
            $table->boolean('supports_cod')->default(false)->comment('Поддерживает наложенный платёж (COD)');

            $table->unsignedSmallInteger('min_days')->nullable()->comment('Мин. срок доставки по сервису (дней)');
            $table->unsignedSmallInteger('max_days')->nullable()->comment('Макс. срок доставки по сервису (дней)');

            $table->json('meta')->nullable()->comment('Данные провайдера (ограничения, параметры, классы, сырой payload)');

            $table->timestamps();

            // Один код сервиса уникален в рамках провайдера
            $table->unique(['provider_id', 'service_code'], 'uq_market_provider_services_provider_code');

            // Быстрые выборки (админка/интеграции)
            $table->index(['provider_id', 'activity', 'sort'], 'ix_market_provider_services_list');
            $table->index(['provider_id', 'delivery_type', 'activity', 'sort'], 'ix_market_provider_services_provider_type_list');

            $table->index(['activity', 'delivery_type'], 'ix_market_provider_services_type');
            $table->index('country_code', 'ix_market_provider_services_country');

            $table->comment('Маркет: сервисы/тарифы служб доставки (кеш справочник по провайдерам)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_provider_services');
    }
};
