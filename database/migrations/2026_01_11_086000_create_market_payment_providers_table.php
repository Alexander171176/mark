<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_payment_providers', function (Blueprint $table) {
            $table->id()->comment('ID платёжного провайдера');

            $table->boolean('activity')->default(true)->comment('Активность провайдера');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');

            $table->string('code', 64)->comment('Код провайдера (kaspi/stripe/paypal/cloudpayments/...)');
            $table->string('name', 255)->comment('Название провайдера');

            $table->string('type', 32)->default('api')->comment('Тип: api/redirect/manual/aggregator');

            // Возможности провайдера
            $table->boolean('supports_cards')->default(true)->comment('Поддержка оплаты картой');
            $table->boolean('supports_bank')->default(false)->comment('Поддержка банковских переводов/счетов');
            $table->boolean('supports_qr')->default(false)->comment('Поддержка QR/Pay');
            $table->boolean('supports_wallet')->default(false)->comment('Поддержка кошельков');
            $table->boolean('supports_installments')->default(false)->comment('Рассрочка/кредит');
            $table->boolean('supports_refunds')->default(true)->comment('Поддержка возвратов');

            // UI/интеграции
            $table->string('site_url', 255)->nullable()->comment('Сайт/документация провайдера');
            $table->json('meta')->nullable()->comment('Доп. данные (страны, ограничения, подсказки, маппинги)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            // UNIQUE / INDEXES
            $table->unique(['code'], 'uq_market_payment_providers_code');
            $table->index(['activity', 'sort'], 'ix_market_payment_providers_list');

            $table->comment('Маркет: глобальный справочник платёжных провайдеров');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_payment_providers');
    }
};
