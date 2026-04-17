<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_statuses', function (Blueprint $table) {

            /* =========================================================
             * BASE
             * ========================================================= */

            $table->id()->comment('ID статуса отправления');

            $table->boolean('activity')->default(true)->comment('Активность статуса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('code', 64)->comment('Код статуса: created/shipped/in_transit/delivered/cancelled/returned/...');

            $table->string('title', 255)->comment('Название статуса (RU по умолчанию)');

            $table->string('type', 32)
                ->default('info')
                ->comment('Тип для UI: info/success/warning/danger');

            $table->boolean('is_final')
                ->default(false)
                ->comment('Финальный статус (доставлено/отменено/возврат)');

            $table->json('meta')
                ->nullable()
                ->comment('Расширение (например: маппинг статусов провайдеров)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique('code', 'uq_market_delivery_statuses_code');

            $table->index(['activity', 'sort'], 'ix_market_delivery_statuses_list');
            $table->index(['is_final', 'activity'], 'ix_market_delivery_statuses_final_active');
            $table->index(['type', 'activity'], 'ix_market_delivery_statuses_type_active');

            $table->comment('Маркет: справочник статусов отправлений (delivery tracking statuses)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_statuses');
    }
};
